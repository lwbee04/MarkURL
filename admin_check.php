<?php
/**
 * MarkURL - Admin Session Check
 */
$is_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
?>
