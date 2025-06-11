<?php
session_start();
include 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$currentId = $_SESSION['user_id'];
$newUsername = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$profileImagePath = null;

// Validate required fields
if (empty($newUsername) || empty($email)) {
    header("Location: edit_profile.php?status=missing_fields");
    exit();
}

// Check if email already exists for a different user
$check_stmt = $conn->prepare("SELECT user_id FROM users WHERE user_email = ? AND user_id != ?");
$check_stmt->bind_param("si", $email, $currentId);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    // Email already in use by another user
    header("Location: edit_profile.php?status=duplicate_email");
    exit();
}
$check_stmt->close();

// Handle profile image upload
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    $imageTmpPath = $_FILES['profile_image']['tmp_name'];
    $imageName = $_FILES['profile_image']['name'];
    $imageExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    if (in_array($imageExtension, $allowedExtensions)) {
        $newFileName = uniqid('profile_', true) . '.' . $imageExtension;
        $uploadDir = 'uploads/profile_pics/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $destination = $uploadDir . $newFileName;

        if (move_uploaded_file($imageTmpPath, $destination)) {
            $profileImagePath = $newFileName; // Store only the filename
        }
    }
}

// Prepare SQL query based on whether profile image was updated
if ($profileImagePath) {
    $query = "UPDATE users SET user_name = ?, user_email = ?, profile_image = ? WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $newUsername, $email, $profileImagePath, $currentId);
} else {
    $query = "UPDATE users SET user_name = ?, user_email = ? WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $newUsername, $email, $currentId);
}

// Execute query and handle result
if ($stmt->execute()) {
    $_SESSION['user_name'] = $newUsername;

    // Insert notification using current user_id directly
    $notif_sql = "INSERT INTO notifications (user_id, message, type) VALUES (?, ?, 'user')";
    $notif_stmt = $conn->prepare($notif_sql);
    $notif_message = "Your profile has been updated successfully.";
    $notif_stmt->bind_param("is", $currentId, $notif_message);
    $notif_stmt->execute();
    $notif_stmt->close();

    header("Location: profile.php?status=success");
    exit();
} else {
    if ($stmt->errno === 1062) {
        header("Location: edit_profile.php?status=duplicate_email");
    } else {
        header("Location: edit_profile.php?status=error");
    }
    exit();
}
?>
