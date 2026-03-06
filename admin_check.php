<?php
/**
 * MarkURL - Admin Session Check
 * FIX: session_start() included for safety
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$is_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
?>
