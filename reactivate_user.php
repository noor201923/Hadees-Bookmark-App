<?php
session_start();
include 'includes/db.php';

if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);

    // Reactivate user and update last_login to NOW()
    $query = "UPDATE users SET is_deleted = 0, last_login = NOW() WHERE user_id = $user_id";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success_msg'] = "User reactivated successfully.";
    } else {
        $_SESSION['error_msg'] = "Failed to reactivate user. Please try again.";
    }
} else {
    $_SESSION['error_msg'] = "Invalid request. User ID missing.";
}

header("Location: manage_users.php");
exit();
