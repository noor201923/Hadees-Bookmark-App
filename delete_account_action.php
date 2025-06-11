<?php
session_start();
if (!isset($_SESSION['user_name'])) {
    header("Location: login.php?status=deleted");
    exit();
}

$userName = $_SESSION['user_name'];

// Database connection (adjust credentials)
$host = "localhost";
$dbUser = "root";
$dbPass = "";   // change as per your setup
$dbName = "hadees_app";  // change to your DB name

$conn = new mysqli($host, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Delete user account query (assuming table 'users' and column 'user_name')
$sql = "DELETE FROM users WHERE user_name = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userName);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    // Account deleted successfully
    $stmt->close();
    $conn->close();

    // Destroy session to logout user
    session_unset();
    session_destroy();

    // Start a new session just to store the success message for redirect
    session_start();
    $_SESSION['delete_success'] = "Your account has been deleted successfully.";

    // Redirect to login page
    header("Location: login.php?status=deleted");
    exit();
} else {
    // Could not delete (maybe user not found)
    $stmt->close();
    $conn->close();
    echo "<script>alert('Error deleting account. Please try again.'); window.location.href = 'delete_account.php';</script>";
    exit();
}
?>
