<?php
session_start();
include 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

$currentUsername = $_SESSION['user_name'];
$newUsername = $_POST['user_name'] ?? '';
$email = $_POST['user_email'] ?? '';
$profileImagePath = null;

// Validate required fields
if (empty($newUsername) || empty($email)) {
    header("Location: admin_edit_profile.php?status=missing_fields");
    exit();
}

// Handle profile image upload
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    $imageTmpPath = $_FILES['profile_image']['tmp_name'];
    $imageName = $_FILES['profile_image']['name'];
    $imageExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    if (in_array($imageExtension, $allowedExtensions)) {
        $newFileName = uniqid('profile_', true) . '.' . $imageExtension;
        $uploadDir = 'uploads/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $destination = $uploadDir . $newFileName;

        if (move_uploaded_file($imageTmpPath, $destination)) {
            $profileImagePath = $destination;
        }
    }
}

// Prepare SQL query based on whether profile image was updated
if ($profileImagePath) {
    $query = "UPDATE users SET user_name = ?, user_email = ?, profile_image = ? WHERE user_name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $newUsername, $email, $profileImagePath, $currentUsername);
} else {
    $query = "UPDATE users SET user_name = ?, user_email = ? WHERE user_name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $newUsername, $email, $currentUsername);
}

// Execute query and handle result
if ($stmt->execute()) {
    // Update session username if changed
    $_SESSION['user_name'] = $newUsername;
    header("Location: admin_edit_profile.php?status=success");
    exit();
} else {
    // Check for duplicate email error
    if ($stmt->errno === 1062) {
        header("Location: admin_edit_profile.php?status=duplicate_email");
    } else {
        header("Location: admin_edit_profile.php?status=error");
    }
    exit();
}
?>
