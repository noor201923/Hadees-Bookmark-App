<?php
session_start();
include 'includes/db.php'; // Make sure this defines $conn

// Check if user is logged in
if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

$userName = $_SESSION['user_name'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validate required fields
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        header("Location: change_password.php?status=missing_fields");
        exit();
    }

    // Check if new password and confirm password match
    if ($newPassword !== $confirmPassword) {
        header("Location: change_password.php?status=password_mismatch");
        exit();
    }

    // Fetch current hashed password from DB
    $query = "SELECT user_password FROM users WHERE user_name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $userName);
    $stmt->execute();
    $stmt->bind_result($hashedPassword);
    $stmt->fetch();
    $stmt->close();

    if (!password_verify($currentPassword, $hashedPassword)) {
        header("Location: change_password.php?status=incorrect_password");
        exit();
    }

    // Hash and update password
    $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $updateQuery = "UPDATE users SET user_password = ? WHERE user_name = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("ss", $newHashedPassword, $userName);

    if ($updateStmt->execute()) {
        // ✅ Logout user after password change
        session_destroy();
        // ✅ Redirect with success flag
        header("Location: login.php?status=success");
        exit();
    } else {
        header("Location: change_password.php?status=update_failed");
        exit();
    }
} else {
    header("Location: change_password.php");
    exit();
}
?>
