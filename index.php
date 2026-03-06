<?php
/**
 * MarkURL - Main Index
 * URL Shortener with Analytics
 */

require_once 'config.php';
require_once 'admin_check.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Database connection failed: " . htmlspecialchars($conn->connect_error));
}

function generateCode($conn, $length = 6) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $code = '';
    do {
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }
        $stmt = $conn->prepare("SELECT id FROM url_shortener WHERE short_code = ?");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $exists = $stmt->get_result()->num_rows > 0;
        $stmt->close();
    } while ($exists);
    return $code;
}

function formatNumber($num) {
    if ($num >= 1000000) return round($num / 1000000, 1) . 'M';
    elseif ($num >= 1000) return round($num / 1000, 1) . 'K';
    return number_format($num);
}

function timeAgo($datetime) {
    if (!$datetime) return 'Belum pernah';
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    if ($diff < 60) return 'Baru saja';
    elseif ($diff < 3600) return floor($diff / 60) . ' menit lalu';
    elseif ($diff < 86400) return floor($diff / 3600) . ' jam lalu';
    elseif ($diff < 604800) return floor($diff / 86400) . ' hari lalu';
    else return date('d/m/Y', $timestamp);
}

$msg = ''; $msg_type = ''; $short_url = ''; $urls = [];
$items_per_page = ITEMS_PER_PAGE;
$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

if ($is_admin) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST['admin_action'])) {
        $original = trim($_POST['original_url'] ?? '');
        $custom = trim($_POST['custom_code'] ?? '');
        if (empty($original)) {
            $msg = 'URL asli wajib diisi'; $msg_type = 'danger';
        } else {
            $code = null; $success = false;
            if (!empty($custom)) {
                $chk = $conn->prepare("SELECT id FROM url_shortener WHERE short_code = ?");
                $chk->bind_param("s", $custom);
                $chk->execute();
                if ($chk->get_result()->num_rows > 0) {
                    $msg = 'Kode "'.$custom.'" sudah terpakai'; $msg_type = 'warning';
                } else {
                    $code = $custom;
                    $stmt = $conn->prepare("INSERT INTO url_shortener (original_url, short_code, custom_code) VALUES (?, ?, ?)");
                    $stmt->bind_param("sss", $original, $code, $custom);
                    $success = $stmt->execute();
                    $stmt->close();
                }
                $chk->close();
            } else {
                $code = generateCode($conn);
                $stmt = $conn->prepare("INSERT INTO url_shortener (original_url, short_code) VALUES (?, ?)");
                $stmt->bind_param("ss", $original, $code);
                $success = $stmt->execute();
                $stmt->close();
            }
            if ($success && $code) {
                $short_url = rtrim(BASE_URL, '/') . '/r.php?code=' . $code;
                $msg = 'URL berhasil dipendekkan!'; $msg_type = 'success';
            } elseif (empty($msg)) {
                $msg = 'Gagal: ' . $conn->error; $msg_type = 'danger';
            }
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['admin_action'])) {
        if ($_POST['admin_action'] === 'edit' && !empty($_POST['short_code']) && !empty($_POST['new_url'])) {
            $stmt = $conn->prepare("UPDATE url_shortener SET original_url = ? WHERE short_code = ?");
            $stmt->bind_param("ss", $_POST['new_url'], $_POST['short_code']);
            if ($stmt->execute()) { $msg = 'URL /'.htmlspecialchars($_POST['short_code']).' diperbarui'; $msg_type = 'success'; }
            else { $msg = 'Gagal update'; $msg_type = 'warning'; }
            $stmt->close();
        }
        if ($_POST['admin_action'] === 'delete' && !empty($_POST['short_code'])) {
            $stmt = $conn->prepare("DELETE FROM url_shortener WHERE short_code = ?");
            $stmt->bind_param("s", $_POST['short_code']);
            if ($stmt->execute() && $stmt->affected_rows > 0) { $msg = 'URL /'.htmlspecialchars($_POST['short_code']).' dihapus'; $msg_type = 'success'; }
            else { $msg = 'Gagal hapus'; $msg_type = 'warning'; }
            $stmt->close();
        }
    }

    $count_result = $conn->query("SELECT COUNT(*) as total FROM url_shortener");
    $total_items = $count_result->fetch_assoc()['total'];
    $total_pages = ceil($total_items / $items_per_page);
    $offset = ($current_page - 1) * $items_per_page;

    $stmt = $conn->prepare("SELECT id, short_code, custom_code, original_url, click_count, last_clicked_at FROM url_shortener ORDER BY id DESC LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $items_per_page, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) $urls[] = $row;
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo SITE_NAME; ?> - URL Shortener</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{--p:#6366f1;--pd:#4f46e5;--s:#8b5cf6;--ok:#10b981;--wr:#f59e0b;--er:#ef4444;--dk:#1e293b;--gr:#64748b}
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Poppins',sans-serif;background:linear-gradient(135deg,#f0f4ff,#e0e7ff);color:var(--dk);min-height:100vh;display:flex;flex-direction:column}
        .navbar{background:rgba(255,255,255,0.95);backdrop-filter:blur(10px);box-shadow:0 2px 20px rgba(0,0,0,0.08);padding:0.8rem 0;position:sticky;top:0}
        .navbar-brand{font-weight:700;color:var(--p)!important}
        .nav-link{color:var(--gr)!important;margin:0 8px}
        .nav-link:hover{color:var(--p)!important}
        .btn-nav{padding:8px 20px;border-radius:50px}
        .btn-nav-p{background:var(--p);color:#fff;border:none}
        .hero{padding:4rem 0 2rem;text-align:center}
        .hero-title{font-size:2.5rem;font-weight:700;margin-bottom:1rem;background:linear-gradient(135deg,var(--p),var(--s));-webkit-background-clip:text;-webkit-text-fill-color:transparent}
        .card-c{background:#fff;border:none;border-radius:20px;box-shadow:0 10px 40px -10px rgba(0,0,0,0.15);padding:2rem;margin:1.5rem auto;max-width:650px}
        .form-control{border:2px solid #e2e8f0;border-radius:12px;padding:12px}
        .form-control:focus{border-color:var(--p);box-shadow:0 0 0 4px rgba(99,102,241,0.15)}
        .btn-pc{background:linear-gradient(135deg,var(--p),var(--s));color:#fff;border:none;border-radius:12px;padding:12px;font-weight:600;width:100%}
        .alert-c{border:none;border-radius:12px;padding:1rem;margin-bottom:1.5rem}
        .alert-ok{background:#dcfce7;color:#166534}
        .alert-wr{background:#fef3c7;color:#92400e}
        .alert-er{background:#fee2e2;color:#991b1b}
        .res-box{background:#f0f9ff;border:2px solid #bae6fd;border-radius:12px;padding:1.25rem;margin:1rem 0;text-align:center}
        .res-url{font-weight:600;color:var(--p);word-break:break-all}
        .btn-cp{background:#fff;color:var(--p);border:2px solid var(--p);border-radius:8px;padding:6px 16px}
        .url-item{background:#fff;border-radius:16px;padding:1.25rem;margin-bottom:1rem;border-left:4px solid var(--p)}
        .url-hdr{display:flex;justify-content:space-between;align-items:flex-start;gap:10px;flex-wrap:wrap}
        .short-lnk{font-weight:600;color:var(--p);text-decoration:none}
        .orig-url{color:var(--gr);word-break:break-all;font-size:0.95rem}
        .ana-bdg{display:inline-flex;align-items:center;gap:6px;background:#f0f9ff;color:#0369a1;padding:4px 12px;border-radius:20px;font-size:0.85rem}
        .ana-bdg.hot{background:#fef3c7;color:#92400e}
        .act-grp{display:flex;gap:8px}
        .btn-act{padding:8px 16px;border-radius:10px;font-size:0.9rem;border:none}
        .btn-ed{background:#dbeafe;color:#1d4ed8}
        .btn-del{background:#fee2e2;color:#dc2626}
        .edit-fc{background:#fffbeb;border:2px solid #fcd34d;border-radius:12px;padding:1rem;margin-top:8px;display:none}
        .edit-fc.show{display:block}
        .pag-cont{display:flex;justify-content:center;gap:8px;margin-top:2rem;flex-wrap:wrap}
        .btn-pg{min-width:40px;height:40px;display:flex;align-items:center;justify-content:center;border-radius:10px;border:2px solid #e2e8f0;background:#fff;color:var(--dk);text-decoration:none}
        .btn-pg.active{background:var(--p);border-color:var(--p);color:#fff}
        .btn-pg.dis{opacity:0.5;pointer-events:none}
        .login-cta{background:linear-gradient(135deg,var(--p),var(--s));color:#fff;border-radius:20px;padding:3rem 2rem;text-align:center;margin:2rem auto;max-width:650px}
        .btn-cta{background:#fff;color:var(--p);border:none;border-radius:50px;padding:12px 40px;font-weight:600}
        .footer{background:var(--dk);color:#fff;padding:2rem 0;margin-top:auto;text-align:center}
        .toast-cont{position:fixed;top:20px;right:20px;z-index:9999}
        .toast{background:#fff;border-radius:12px;box-shadow:0 10px 40px rgba(0,0,0,0.15);padding:1rem 1.25rem;margin-bottom:10px;display:flex;align-items:center;gap:10px;animation:slideIn 0.3s ease,fadeOut 0.3s ease 2.7s forwards;border-left:4px solid var(--p)}
        @keyframes slideIn{from{transform:translateX(100%);opacity:0}to{transform:translateX(0);opacity:1}}
        @keyframes fadeOut{to{opacity:0;transform:translateX(100%)}}
        @media(max-width:768px){.hero-title{font-size:2rem}.card-c{margin:1rem;padding:1.5rem}.url-hdr{flex-direction:column}.act-grp{width:100%}}
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-link mr-2"></i><?php echo SITE_NAME; ?></a>
            <div class="ml-auto">
                <?php if($is_admin): ?>
                <a href="logout.php" class="btn btn-nav btn-outline-danger btn-sm"><i class="fas fa-sign-out-alt mr-1"></i>Keluar</a>
                <?php else: ?>
                <a href="login.php" class="btn btn-nav btn-nav-p btn-sm"><i class="fas fa-user-lock mr-1"></i>Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="container">
            <h1 class="hero-title"><?php echo $is_admin ? 'Panel Admin' : 'MarkURL Shortener'; ?></h1>
            <p class="text-muted"><?php echo $is_admin ? 'Kelola URL pendek Anda' : 'Login untuk akses fitur lengkap'; ?></p>
            
            <?php if($is_admin && $msg): ?>
            <div class="alert-c alert-<?php echo $msg_type; ?>">
                <i class="fas fa-<?php echo $msg_type=='success'?'check-circle':($msg_type=='warning'?'exclamation-triangle':'exclamation-circle'); ?> mr-2"></i>
                <?php echo htmlspecialchars($msg); ?>
            </div>
            <?php endif; ?>

            <?php if($is_admin && $short_url): ?>
            <div class="res-box">
                <small class="text-muted d-block mb-1">URL Pendek:</small>
                <a href="<?php echo htmlspecialchars($short_url); ?>" target="_blank" class="res-url"><?php echo htmlspecialchars($short_url); ?></a>
                <button class="btn-cp mt-2" onclick="copyText('<?php echo addslashes($short_url); ?>')"><i class="fas fa-copy mr-1"></i>Salin</button>
            </div>
            <?php endif; ?>

            <?php if($is_admin): ?>
            <div class="card-c">
                <form method="post">
                    <div class="form-group">
                        <label class="form-label">URL Asli *</label>
                        <input type="url" name="original_url" class="form-control" required placeholder="https://contoh.com/..." value="<?php echo htmlspecialchars($_POST['original_url'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kode Custom (opsional)</label>
                        <input type="text" name="custom_code" class="form-control" placeholder="misal: promo" maxlength="20" pattern="[a-zA-Z0-9\-_]+" value="<?php echo htmlspecialchars($_POST['custom_code'] ?? ''); ?>">
                        <small class="text-muted">Kosongkan untuk kode acak</small>
                    </div>
                    <button type="submit" class="btn-pc"><i class="fas fa-magic mr-2"></i>Buat Short URL</button>
                </form>
            </div>
            <?php else: ?>
            <div class="login-cta">
                <h3 class="mb-3"><i class="fas fa-lock mr-2"></i>Akses Admin</h3>
                <p class="mb-4">Login untuk membuat & mengelola URL pendek</p>
                <a href="login.php" class="btn-cta"><i class="fas fa-sign-in-alt mr-2"></i>Login Sekarang</a>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <?php if($is_admin): ?>
    <section class="py-4">
        <div class="container">
            <div class="card-c" style="max-width:900px">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="fas fa-list mr-2"></i>Daftar URL</h5>
                    <span class="badge badge-primary"><?php echo count($urls); ?> URL</span>
                </div>
                <?php if(empty($urls)): ?>
                <p class="text-center text-muted py-4">Belum ada URL</p>
                <?php else: ?>
                <?php foreach($urls as $u): 
                    $fs = rtrim(BASE_URL,'/').'/r.php?code='.urlencode($u['short_code']);
                    $cc = (int)($u['click_count'] ?? 0);
                    $hot = $cc >= 10;
                ?>
                <div class="url-item" id="item-<?php echo $u['short_code']; ?>">
                    <div class="url-hdr">
                        <div>
                            <a href="<?php echo htmlspecialchars($fs); ?>" target="_blank" class="short-lnk">
                                <i class="fas fa-external-link-alt mr-1"></i>
                                /<?php echo htmlspecialchars($u['short_code']); ?>
                            </a>
                            <?php if(!empty($u['custom_code'])): ?>
                            <span class="badge badge-info ml-2">custom</span>
                            <?php endif; ?>
                            <div class="ana-bdg <?php echo $hot?'hot':''; ?> mt-2">
                                <i class="fas fa-<?php echo $hot?'fire':'chart-line'; ?> mr-1"></i>
                                <?php echo formatNumber($cc); ?> klik • <?php echo timeAgo($u['last_clicked_at']); ?>
                            </div>
                        </div>
                        <div class="act-grp">
                            <button class="btn-act btn-ed" onclick="toggleEdit('<?php echo $u['short_code']; ?>')">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </button>
                            <form method="post" class="d-inline" onsubmit="return confirm('Hapus?')">
                                <input type="hidden" name="admin_action" value="delete">
                                <input type="hidden" name="short_code" value="<?php echo htmlspecialchars($u['short_code']); ?>">
                                <button class="btn-act btn-del"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                    <div class="orig-url mt-2" id="orig-<?php echo $u['short_code']; ?>">
                        <i class="fas fa-arrow-right mr-2 text-muted"></i>
                        <?php echo htmlspecialchars($u['original_url']); ?>
                    </div>
                    <form method="post" class="edit-fc" id="edit-<?php echo $u['short_code']; ?>">
                        <input type="hidden" name="admin_action" value="edit">
                        <input type="hidden" name="short_code" value="<?php echo htmlspecialchars($u['short_code']); ?>">
                        <div class="d-flex gap-2">
                            <input type="url" name="new_url" class="form-control" value="<?php echo htmlspecialchars($u['original_url']); ?>" required>
                            <button class="btn-act btn-sv" style="background:var(--ok);color:#fff">Simpan</button>
                            <button type="button" class="btn-act btn-ca" onclick="cancelEdit('<?php echo $u['short_code']; ?>')">Batal</button>
                        </div>
                    </form>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>

                <?php if($total_pages > 1): ?>
                <div class="pag-cont">
                    <span class="text-muted small">Halaman <?php echo $current_page; ?> dari <?php echo $total_pages; ?></span>
                    <?php if($current_page > 1): ?>
                    <a href="?page=<?php echo $current_page-1; ?>#admin-panel" class="btn-pg"><i class="fas fa-chevron-left"></i></a>
                    <?php else: ?>
                    <span class="btn-pg dis"><i class="fas fa-chevron-left"></i></span>
                    <?php endif; ?>
                    <?php for($i=max(1,$current_page-2);$i<=min($total_pages,$current_page+2);$i++): ?>
                    <a href="?page=<?php echo $i; ?>#admin-panel" class="btn-pg <?php echo $i==$current_page?'active':''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                    <?php if($current_page < $total_pages): ?>
                    <a href="?page=<?php echo $current_page+1; ?>#admin-panel" class="btn-pg"><i class="fas fa-chevron-right"></i></a>
                    <?php else: ?>
                    <span class="btn-pg dis"><i class="fas fa-chevron-right"></i></span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <footer class="footer">
        <div class="container">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
    <script>
    function copyText(t){navigator.clipboard.writeText(t).then(()=>alert('Tersalin!')).catch(()=>{const e=document.createElement('textarea');e.value=t;document.body.appendChild(e);e.select();document.execCommand('copy');document.body.removeChild(e);alert('Tersalin!')})}
    function toggleEdit(c){const f=document.getElementById('edit-'+c),o=document.getElementById('orig-'+c);if(f.classList.contains('show')){f.classList.remove('show');o.style.display='block'}else{o.style.display='none';f.classList.add('show')}}
    function cancelEdit(c){const f=document.getElementById('edit-'+c),o=document.getElementById('orig-'+c);f.classList.remove('show');o.style.display='block'}
    </script>
</body>
</html>
