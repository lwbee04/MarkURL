<?php
/**
 * MarkURL - Admin Login
 */
session_start();
error_reporting(0);
require_once 'config.php';

if (isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php#admin-panel"); exit;
}

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';
    
    if (empty($u) || empty($p)) {
        $err = 'Username & password wajib';
    } else {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            $err = 'DB error';
        } else {
            $stmt = $conn->prepare("SELECT password FROM admin_users WHERE username = ?");
            $stmt->bind_param("s", $u);
            $stmt->execute();
            $res = $stmt->get_result();
            
            if ($res->num_rows === 1 && password_verify($p, $res->fetch_assoc()['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_user'] = $u;
                $upd = $conn->prepare("UPDATE admin_users SET last_login = NOW() WHERE username = ?");
                $upd->bind_param("s", $u);
                $upd->execute();
                $upd->close();
                header("Location: index.php#admin-panel"); exit;
            } else {
                $err = 'Kredensial salah';
            }
            $stmt->close();
        }
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body{font-family:sans-serif;background:linear-gradient(135deg,#6366f1,#8b5cf6);min-height:100vh;display:flex;align-items:center;justify-content:center}
        .card{background:#fff;border-radius:20px;padding:2.5rem;box-shadow:0 20px 60px rgba(0,0,0,0.3);width:100%;max-width:400px}
        .form-control{border:2px solid #e2e8f0;border-radius:12px;padding:12px}
        .form-control:focus{border-color:#6366f1;box-shadow:0 0 0 4px rgba(99,102,241,0.15)}
        .btn-login{background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;border:none;border-radius:12px;padding:12px;font-weight:600;width:100%}
    </style>
</head>
<body>
    <div class="card">
        <h3 class="text-center mb-4" style="color:#6366f1"><i class="fas fa-user-lock mr-2"></i>Login Admin</h3>
        <?php if($err): ?><div class="alert alert-danger"><?php echo $err; ?></div><?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn-login"><i class="fas fa-sign-in-alt mr-2"></i>Login</button>
        </form>
        <p class="text-center mt-3"><a href="index.php" style="color:#6366f1">← Kembali</a></p>
    </div>
</body>
</html>
