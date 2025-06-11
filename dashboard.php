<?php
session_start();
if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] === 'admin') {
    header("Location: admin_dashboard.php");
    exit();
} elseif ($_SESSION['role'] === 'user') {
    header("Location: user_dashboard.php");
    exit();
} else {
    echo "Invalid role!";
    exit();
}
?>
