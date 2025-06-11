<?php
session_start();
include 'includes/db.php';

// Check if user_id is passed
if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
    $current_admin_id = $_SESSION['admin_id'] ?? 0;

    // Prevent admin from deactivating themselves (optional safety)
    if ($user_id == $current_admin_id) {
        $_SESSION['error_msg'] = "You cannot deactivate your own account.";
    } else {
        $query = "UPDATE users SET is_deleted = 1 WHERE user_id = $user_id";

        if (mysqli_query($conn, $query)) {
            $_SESSION['success_msg'] = "User deactivated successfully.";
        } else {
            $_SESSION['error_msg'] = "Failed to deactivate user. Please try again.";
        }
    }
} else {
    $_SESSION['error_msg'] = "Invalid request. User ID missing.";
}

header("Location: manage_users.php");
exit();
