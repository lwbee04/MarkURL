<?php
/**
 * MarkURL - Logout Handler
 */
session_start();
session_destroy();
header('Location: index.php');
exit;
?>
