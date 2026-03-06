<?php
/**
 * MarkURL - Auto Installer
 * Download semua file aplikasi via cURL/wget
 * Setup config.php otomatis
 */

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Remote file server URL (Ganti dengan URL hosting Anda)
$REMOTE_BASE = 'https://lwbee04.github.io/MarkURL/';

$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$messages = [];
$errors = [];

// Default values
$defaults = [
    'db_host' => 'localhost',
    'db_name' => 'markurl_db',
    'db_user' => 'markurl_user',
    'db_pass' => '',
    'base_url' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']),
    'site_name' => 'MarkURL',
    'admin_user' => 'admin',
    'admin_pass' => '',
];

// Files to download
$files_to_download = [
    'index.php',
    'r.php',
    'login.php',
    'logout.php',
    'admin_check.php',
    '.htaccess'
];

// Handle installation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['install'])) {
    
    $config = [
        'db_host' => trim($_POST['db_host'] ?? 'localhost'),
        'db_name' => trim($_POST['db_name'] ?? 'markurl_db'),
        'db_user' => trim($_POST['db_user'] ?? ''),
        'db_pass' => $_POST['db_pass'] ?? '',
        'base_url' => rtrim(trim($_POST['base_url'] ?? ''), '/'),
        'site_name' => trim($_POST['site_name'] ?? 'MarkURL'),
        'admin_user' => trim($_POST['admin_user'] ?? 'admin'),
        'admin_pass' => trim($_POST['admin_pass'] ?? ''),
    ];
    
    // Validation
    if (empty($config['db_name']) || empty($config['db_user']) || empty($config['admin_pass'])) {
        $errors[] = "⚠️ Nama database, username database, dan password admin wajib diisi!";
    }
    
    if (empty($errors)) {
        $result = runInstallation($config, $REMOTE_BASE, $files_to_download);
        
        if ($result['success']) {
            $messages = $result['messages'];
            $step = 2; // Success
        } else {
            $errors = $result['errors'];
        }
    }
}

// ============================================================================
// INSTALLATION FUNCTIONS
// ============================================================================

function runInstallation($config, $remote_base, $files_to_download) {
    $result = ['success' => false, 'messages' => [], 'errors' => []];
    
    try {
        // 1. Test Database Connection
        $conn = new mysqli($config['db_host'], $config['db_user'], $config['db_pass']);
        if ($conn->connect_error) {
            throw new Exception("Koneksi database gagal: " . $conn->connect_error);
        }
        
        // 2. Create database
        $conn->query("CREATE DATABASE IF NOT EXISTS `" . $config['db_name'] . "` 
                     CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $conn->select_db($config['db_name']);
        $result['messages'][] = "✅ Database '{$config['db_name']}' siap";
        
        // 3. Create tables
        createTables($conn);
        $result['messages'][] = "✅ Tabel database dibuat";
        
        // 4. Create admin user
        createAdminUser($conn, $config['admin_user'], $config['admin_pass']);
        $result['messages'][] = "✅ Admin '{$config['admin_user']}' dibuat";
        
        $conn->close();
        
        // 5. Create config.php
        if (createConfigFile($config)) {
            $result['messages'][] = "✅ File config.php dibuat";
        } else {
            throw new Exception("Gagal menulis config.php - cek permission folder");
        }
        
        // 6. Download application files
        $downloaded = downloadFiles($remote_base, $files_to_download);
        if ($downloaded) {
            $result['messages'][] = "✅ File aplikasi didownload (" . count($files_to_download) . " file)";
        } else {
            throw new Exception("Gagal mendownload file aplikasi");
        }
        
        $result['success'] = true;
        $result['messages'][] = "🎉 Instalasi MarkURL selesai!";
        $result['messages'][] = "⚠️ Hapus install.php setelah ini untuk keamanan";
        
    } catch (Exception $e) {
        $result['errors'][] = "❌ " . $e->getMessage();
    }
    
    return $result;
}

function createTables($conn) {
    $sql = [
        "CREATE TABLE IF NOT EXISTS url_shortener (
            id INT AUTO_INCREMENT PRIMARY KEY,
            short_code VARCHAR(20) UNIQUE NOT NULL,
            custom_code VARCHAR(20) DEFAULT NULL,
            original_url TEXT NOT NULL,
            click_count INT DEFAULT 0,
            last_clicked_at TIMESTAMP NULL DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_short_code (short_code),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        "CREATE TABLE IF NOT EXISTS admin_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            last_login TIMESTAMP NULL DEFAULT NULL,
            INDEX idx_username (username)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    ];
    
    foreach ($sql as $q) {
        if (!$conn->query($q)) {
            throw new Exception("Gagal query: " . $conn->error);
        }
    }
}

function createAdminUser($conn, $user, $pass) {
    $hashed = password_hash($pass, PASSWORD_DEFAULT);
    $check = $conn->prepare("SELECT id FROM admin_users WHERE username = ?");
    $check->bind_param("s", $user);
    $check->execute();
    
    if ($check->get_result()->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $user, $hashed);
        if (!$stmt->execute()) {
            throw new Exception("Gagal buat admin: " . $conn->error);
        }
        $stmt->close();
    }
    $check->close();
}

function createConfigFile($config) {
    $c = "<?php\n";
    $c .= "// MarkURL Configuration - Auto Generated " . date('Y-m-d H:i:s') . "\n";
    $c .= "// Jangan edit file ini manual\n\n";
    $c .= "define('DB_HOST', '" . addslashes($config['db_host']) . "');\n";
    $c .= "define('DB_NAME', '" . addslashes($config['db_name']) . "');\n";
    $c .= "define('DB_USER', '" . addslashes($config['db_user']) . "');\n";
    $c .= "define('DB_PASS', '" . addslashes($config['db_pass']) . "');\n";
    $c .= "define('BASE_URL', '" . addslashes($config['base_url']) . "');\n";
    $c .= "define('SITE_NAME', '" . addslashes($config['site_name']) . "');\n";
    $c .= "define('ITEMS_PER_PAGE', 5);\n";
    $c .= "define('CODE_LENGTH', 6);\n";
    $c .= "define('VERSION', '1.0.0');\n";
    $c .= "define('INSTALL_DATE', '" . date('Y-m-d H:i:s') . "');\n";
    return file_put_contents('config.php', $c) !== false;
}

function downloadFiles($remote_base, $files) {
    $success = true;
    
    foreach ($files as $file) {
        $url = $remote_base . $file;
        $local = $file;
        
        // Try cURL first
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            $content = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($http_code === 200 && $content) {
                file_put_contents($local, $content);
                continue;
            }
        }
        
        // Fallback to file_get_contents
        if (ini_get('allow_url_fopen')) {
            $content = @file_get_contents($url);
            if ($content !== false) {
                file_put_contents($local, $content);
                continue;
            }
        }
        
        // Last resort: wget via shell_exec
        if (function_exists('shell_exec')) {
            shell_exec("wget -q -O {$local} {$url} 2>/dev/null");
            if (file_exists($local) && filesize($local) > 0) {
                continue;
            }
        }
        
        $success = false;
    }
    
    return $success;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>🚀 MarkURL Installer</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); min-height: 100vh; padding: 2rem 0; }
        .installer { background: #fff; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); padding: 3rem; max-width: 700px; margin: 0 auto; }
        .installer h2 { color: #6366f1; font-weight: 700; margin-bottom: 0.5rem; }
        .form-control { border: 2px solid #e2e8f0; border-radius: 12px; padding: 12px 16px; }
        .form-control:focus { border-color: #6366f1; box-shadow: 0 0 0 4px rgba(99,102,241,0.15); }
        .btn-install { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: #fff; border: none; border-radius: 12px; padding: 14px 40px; font-weight: 600; width: 100%; font-size: 1.1rem; }
        .btn-install:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(99,102,241,0.4); }
        .alert-c { border: none; border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 1rem; }
        .alert-ok { background: #dcfce7; color: #166534; }
        .alert-er { background: #fee2e2; color: #991b1b; }
        .success-box { text-align: center; padding: 2rem; }
        .success-box i { font-size: 4rem; color: #10b981; margin-bottom: 1rem; }
        .checklist { text-align: left; max-width: 500px; margin: 2rem auto; }
        .checklist-item { display: flex; align-items: center; gap: 10px; padding: 0.75rem; background: #f8fafc; border-radius: 8px; margin-bottom: 0.5rem; }
        .checklist-item i { color: #10b981; }
        .cred-box { background: #f0f9ff; border: 2px solid #bae6fd; border-radius: 12px; padding: 1rem; margin: 1.5rem 0; }
        .warning-box { background: #fef3c7; border: 2px solid #fcd34d; border-radius: 12px; padding: 1rem; margin: 1rem 0; }
        .download-status { background: #f1f5f9; border-radius: 8px; padding: 1rem; margin: 1rem 0; }
        .download-item { display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0; border-bottom: 1px solid #e2e8f0; }
        .download-item:last-child { border-bottom: none; }
        small.text-muted { font-size: 0.85rem; }
    </style>
</head>
<body>
<div class="container">
    <div class="installer">
        <div class="text-center mb-4">
            <h2><i class="fas fa-rocket mr-2"></i>MarkURL Installer</h2>
            <p class="text-muted mb-0">Setup database & download file otomatis</p>
        </div>

        <?php if ($step === 2): ?>
            <!-- SUCCESS STATE -->
            <div class="success-box">
                <i class="fas fa-check-circle"></i>
                <h3 class="mb-3">🎉 Instalasi Berhasil!</h3>
                <p class="text-muted">MarkURL siap digunakan</p>
                
                <div class="checklist">
                    <?php foreach ($messages as $m): ?>
                    <div class="checklist-item"><i class="fas fa-check-circle"></i><span><?= $m ?></span></div>
                    <?php endforeach; ?>
                </div>

                <div class="cred-box">
                    <h6 class="mb-2"><i class="fas fa-key mr-2"></i>Kredensial Admin:</h6>
                    <p class="mb-1"><strong>Username:</strong> <?= htmlspecialchars($config['admin_user']) ?></p>
                    <p class="mb-0"><strong>Password:</strong> <?= htmlspecialchars($config['admin_pass']) ?></p>
                    <small class="text-muted d-block mt-2">⚠️ Ganti password setelah login pertama!</small>
                </div>

                <div class="warning-box">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Hapus install.php untuk keamanan!</strong><br>
                    <small>File ini tidak diperlukan lagi setelah instalasi</small>
                </div>

                <div class="mt-4">
                    <a href="index.php" class="btn btn-install"><i class="fas fa-home mr-2"></i>Buka MarkURL</a>
                    <a href="login.php" class="btn btn-outline-primary mt-3 w-100"><i class="fas fa-sign-in-alt mr-2"></i>Login Admin</a>
                </div>
            </div>

        <?php else: ?>
            <!-- INSTALL FORM -->
            <?php if (!empty($errors)): ?>
                <?php foreach ($errors as $e): ?>
                <div class="alert-c alert-er"><i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($e) ?></div>
                <?php endforeach; ?>
            <?php endif; ?>

            <form method="post">
                <h5 class="mb-3"><i class="fas fa-database mr-2"></i>Database</h5>
                <div class="form-row">
                    <div class="col-md-6 form-group">
                        <label>Host</label>
                        <input type="text" name="db_host" class="form-control" value="<?= htmlspecialchars($_POST['db_host'] ?? $defaults['db_host']) ?>" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Nama Database</label>
                        <input type="text" name="db_name" class="form-control" value="<?= htmlspecialchars($_POST['db_name'] ?? $defaults['db_name']) ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6 form-group">
                        <label>Username</label>
                        <input type="text" name="db_user" class="form-control" value="<?= htmlspecialchars($_POST['db_user'] ?? $defaults['db_user']) ?>" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Password</label>
                        <input type="password" name="db_pass" class="form-control" value="<?= htmlspecialchars($_POST['db_pass'] ?? $defaults['db_pass']) ?>">
                    </div>
                </div>

                <h5 class="mb-3 mt-4"><i class="fas fa-globe mr-2"></i>Website</h5>
                <div class="form-group">
                    <label>Base URL</label>
                    <input type="url" name="base_url" class="form-control" value="<?= htmlspecialchars($_POST['base_url'] ?? $defaults['base_url']) ?>" required>
                    <small class="text-muted">Contoh: https://domain-anda.com</small>
                </div>
                <div class="form-group">
                    <label>Nama Situs</label>
                    <input type="text" name="site_name" class="form-control" value="<?= htmlspecialchars($_POST['site_name'] ?? $defaults['site_name']) ?>" required>
                </div>

                <h5 class="mb-3 mt-4"><i class="fas fa-user-shield mr-2"></i>Admin</h5>
                <div class="form-row">
                    <div class="col-md-6 form-group">
                        <label>Username</label>
                        <input type="text" name="admin_user" class="form-control" value="<?= htmlspecialchars($_POST['admin_user'] ?? $defaults['admin_user']) ?>" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Password</label>
                        <input type="password" name="admin_pass" class="form-control" required placeholder="Minimal 6 karakter">
                        <small class="text-muted">⚠️ Ganti setelah instalasi!</small>
                    </div>
                </div>

                <button type="submit" name="install" class="btn-install mt-4">
                    <i class="fas fa-magic mr-2"></i>🚀 Jalankan Instalasi
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
</body>
</html>
