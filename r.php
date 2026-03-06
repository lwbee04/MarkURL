<?php
/**
 * MarkURL - Redirect Handler with Analytics
 */
error_reporting(0);
require_once 'config.php';

$code = isset($_GET['code']) ? trim($_GET['code']) : '';
if (empty($code)) { header("Location: " . BASE_URL); exit; }

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) { http_response_code(500); exit; }

$stmt = $conn->prepare("SELECT original_url FROM url_shortener WHERE short_code = ?");
$stmt->bind_param("s", $code);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    $stmt->close(); $conn->close();
    http_response_code(404); exit("URL not found");
}

$url = $res->fetch_assoc()['original_url'];

// Update analytics
$upd = $conn->prepare("UPDATE url_shortener SET click_count = click_count + 1, last_clicked_at = NOW() WHERE short_code = ?");
$upd->bind_param("s", $code);
$upd->execute();
$upd->close(); $stmt->close(); $conn->close();

header("Location: " . $url, true, 301);
exit;
?>
