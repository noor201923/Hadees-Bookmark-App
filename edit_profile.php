<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userName = $_SESSION['user_id'];

include 'includes/db.php';
$query = "SELECT user_id, user_email, profile_image FROM users WHERE user_name = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $userName);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$email = $userData['user_email'] ?? '';
$profileImage = $userData['profile_image'] ?? '';
$imagePath = (!empty($profileImage) && file_exists("uploads/profile_pics/$profileImage")) 
    ? "uploads/profile_pics/$profileImage" 
    : "uploads/default.png"; // Default image if no profile image exists
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Edit Profile - Hadees App</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    body {
      background: linear-gradient(135deg, #e0f2f1, #a5d6a7);
      font-family: 'Segoe UI', sans-serif;
    }
    main {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .form-container {
      max-width: 600px;
      width: 100%;
      margin: 60px auto;
      background: linear-gradient(135deg, #e8f5e9, #ffffff);
      padding: 40px;
      border-radius: 16px;
animation: fadeInUp 0.7s ease forwards;
position: relative;
      overflow: hidden;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    .form-title {
      font-size: 1.8rem;
      font-weight: bold;
      color: #14532d;
      margin-bottom: 25px;
      text-align: center;
    }
    label {
      color: #14532d;
      font-weight: 600;
    }
    .form-control {
      border-radius: 10px;
    }
    .btn-save {
      background: linear-gradient(to right, #14532d, #1e3a2c);
      color: white !important;
      font-weight: 600;
      padding: 12px 24px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(20, 83, 45, 0.3);
      margin-top: 20px;
      border: none;
      transition: all 0.3s ease-in-out;
    }
    .btn-save:hover {
      background: linear-gradient(to right, #1e3a2c, #14532d);
      transform: translateY(-2px);
      box-shadow: 0 6px 14px rgba(20, 83, 45, 0.4);
      color: white;
    }

    /* Container for profile pic + edit icon */
    .profile-pic-wrapper {
      position: relative;
      display: inline-block;
      cursor: pointer;
      width: 120px;
      height: 120px;
      border-radius: 50%;
      padding: 5px; /* space for border */
      background: linear-gradient(270deg, #4caf50, #81c784, #a5d6a7, #4caf50);
      background-size: 600% 600%;
      animation: gradient-border 8s ease infinite;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    /* Inner circle white background */
    .profile-pic-inner {
      width: 100%;
      height: 100%;
      border-radius: 50%;
      background: white;
      padding: 3px;
      box-sizing: border-box;
    }
    .profile-pic {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 50%;
      display: block;
      box-shadow: inset 0 0 8px rgba(0,0,0,0.15);
    }

    @keyframes gradient-border {
      0% {background-position: 0% 50%;}
      50% {background-position: 100% 50%;}
      100% {background-position: 0% 50%;}
    }

    /* Edit icon styling */
    .edit-icon {
      position: absolute;
      bottom: 4px;
      right: 4px;
      background: #14532d;
      color: white;
      border-radius: 50%;
      padding: 6px;
      font-size: 18px;
      border: 2px solid white;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      display: flex;
      justify-content: center;
      align-items: center;
      width: 34px;
      height: 34px;
      z-index: 2;
    }
    .edit-icon:hover {
      transform: scale(1.3);
      box-shadow: 0 0 8px #4caf50;
    }
    /* Hide file input */
    #profile_image {
      display: none;
    }

    /* Tooltip container */
    .tooltip-wrapper {
      position: relative;
      display: inline-block;
    }
    .tooltip-wrapper:hover .tooltip-text {
      visibility: visible;
      opacity: 1;
      transform: translateY(-8px);
    }
    .tooltip-text {
      visibility: hidden;
      opacity: 0;
      width: 90px;
      background-color: #14532d;
      color: #fff;
      text-align: center;
      border-radius: 6px;
      padding: 6px 8px;
      position: absolute;
      z-index: 10;
      bottom: 130%;
      right: 50%;
      margin-right: -45px;
      font-size: 12px;
      transition: opacity 0.3s ease, transform 0.3s ease;
      pointer-events: none;
      user-select: none;
    }
@keyframes fadeInUp {
      0% {
        opacity: 0;
        transform: translateY(25px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="content">
  <div class="form-container">
    <h2 class="form-title">Edit Profile</h2>

    <?php if (isset($_GET['status'])): ?>
      <?php if ($_GET['status'] === 'missing_fields'): ?>
        <div class="alert alert-danger">Username and Email are required.</div>
      <?php elseif ($_GET['status'] === 'duplicate_email'): ?>
        <div class="alert alert-danger">This email is already in use. Please use a different one.</div>
      <?php elseif ($_GET['status'] === 'success'): ?>
        <div class="alert alert-success">Profile updated successfully!</div>
      <?php elseif ($_GET['status'] === 'error'): ?>
        <div class="alert alert-danger">Something went wrong. Please try again.</div>
      <?php endif; ?>
    <?php endif; ?>

    <form action="update_profile.php" method="post" enctype="multipart/form-data">

      <!-- Profile Picture with animated gradient border and edit icon -->
      <div class="text-center mb-4">
        <label for="profile_image" class="profile-pic-wrapper tooltip-wrapper" aria-label="Edit Photo" tabindex="0">
          <div class="profile-pic-inner">
            <img src="<?= htmlspecialchars($imagePath); ?>" alt="Profile Picture" class="profile-pic" id="profilePreview">
          </div>
          <div class="edit-icon" title="Edit Photo">
            <i class="bi bi-pencil-fill"></i>
          </div>
          <span class="tooltip-text">Edit Photo</span>
        </label>
        <input type="file" name="profile_image" id="profile_image" accept="image/*" onchange="previewImage(event)">
      </div>

      <!-- Username -->
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($userName); ?>" required>
      </div>

      <!-- Email -->
      <div class="mb-3">
        <label for="email" class="form-label">Email address</label>
        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email); ?>" required>
      </div>

      <!-- Save Button -->
      <button type="submit" class="btn btn-save w-100"><i class="bi bi-save"></i> Save Changes</button>
    </form>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function previewImage(event) {
  const reader = new FileReader();
  reader.onload = function () {
    const output = document.getElementById('profilePreview');
    output.src = reader.result;
  };
  reader.readAsDataURL(event.target.files[0]);
}
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>
