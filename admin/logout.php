<?php
require_once '../config.php';
requireLogin();

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

header('Location: login.php');
exit;
?>

