<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'includes/db.php';

$userId = $_SESSION['user_id'];

$sql = "SELECT user_name, user_email, user_joined_date, profile_image FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($userName, $userEmail, $joinedDate, $profileImage);

if (!$stmt->fetch()) {
    $userName = "Unknown User";
    $userEmail = "Email not found";
    $joinedDate = "Date not found";
    $profileImage = null;
}

$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Profile - Hadees App</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="style.css" rel="stylesheet" />
  <style>
    /* Base & Layout */
    body {
      background: linear-gradient(135deg, #e3f6f5 0%, #b1d8d8 100%);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #1a3c40;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      margin: 0;
    }
    main.content {
      flex-grow: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 2rem;
    }
    .profile-card {
      background: linear-gradient(135deg, #e8f5e9, #ffffff);
      max-width: 600px;
      width: 100%;
      padding: 2.5rem 3rem;
      border-radius: 20px;
      box-shadow: 0 12px 30px rgba(26, 60, 64, 0.2);
      animation: fadeInUp 0.7s ease forwards;
      border: 2px solid #1b4d3e;
      position: relative;
      overflow: hidden;
    }

    /* Islamic Pattern Decoration */
    .profile-card::before {
      content: "";
      position: absolute;
      top: -50px;
      right: -50px;
      width: 150px;
      height: 150px;
      background: radial-gradient(circle at center, #1b4d3e33 20%, transparent 70%);
      clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
      transform: rotate(45deg);
      z-index: 0;
    }

    .profile-header {
      display: flex;
      align-items: center;
      gap: 2rem;
      margin-bottom: 2rem;
      position: relative;
      z-index: 1;
    }

    /* Avatar Styles */
    .profile-avatar, .profile-avatar-img {
      width: 110px;
      height: 110px;
      border-radius: 50%;
      border: 4px solid #1b4d3e;
      background-color: #1b4d3e;
      color: #d0f0e7;
      font-size: 52px;
      display: flex;
      justify-content: center;
      align-items: center;
      text-transform: uppercase;
      box-shadow: 0 0 10px #3ba55c88;
      transition: transform 0.3s ease;
      object-fit: cover;
      flex-shrink: 0;
    }
    .profile-avatar:hover, .profile-avatar-img:hover {
      transform: scale(1.1);
      box-shadow: 0 0 25px #3ba55ccc;
    }

    /* Text Info */
    .profile-name {
      font-size: 2.4rem;
      font-weight: 700;
      color: #145234;
      margin-bottom: 0.3rem;
      font-family: 'Cairo', sans-serif; /* Islamic vibe font */
      text-shadow: 0 1px 2px #b1d8d8;
    }
    .profile-info {
      font-size: 1.15rem;
      color: #2e7d5a;
      margin-bottom: 0.7rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-weight: 500;
    }
    .profile-info i {
      font-size: 1.3rem;
      color: #3ba55c;
    }

    /* Button */
    .btn-edit-profile {
      background-color: #3ba55c;
      border: none;
      color: white;
      padding: 12px 28px;
      border-radius: 30px;
      font-size: 1.1rem;
      font-weight: 600;
      letter-spacing: 0.03em;
      cursor: pointer;
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
      box-shadow: 0 6px 12px #3ba55c88;
      margin-top: 1rem;
      display: inline-block;
    }
    .btn-edit-profile:hover {
      background-color: #2e7d5a;
      box-shadow: 0 8px 20px #2e7d5acc;
    }

    /* Animation */
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

    /* Responsive */
    @media (max-width: 600px) {
      .profile-card {
        padding: 2rem 1.5rem;
      }
      .profile-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
      }
      .profile-avatar, .profile-avatar-img {
        width: 90px;
        height: 90px;
        font-size: 42px;
        border-width: 3px;
      }
      .profile-name {
        font-size: 2rem;
      }
      .btn-edit-profile {
        width: 100%;
        padding: 14px 0;
      }
    }

  </style>
  <!-- Optional: Include Google Fonts for Arabic/Islamic vibe -->
  <link href="https://fonts.googleapis.com/css2?family=Cairo&display=swap" rel="stylesheet" />
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="content">

  <div class="profile-card">
    <div class="profile-header">
      <?php if (!empty($profileImage) && file_exists("uploads/profile_pics/$profileImage")): ?>
        <img src="uploads/profile_pics/<?php echo htmlspecialchars($profileImage); ?>" alt="Profile Picture" class="profile-avatar-img" />
      <?php else: ?>
        <div class="profile-avatar"><?php echo strtoupper($userName[0]); ?></div>
      <?php endif; ?>
      <div>
        <h2 class="profile-name"><?php echo htmlspecialchars($userName); ?></h2>
        <p class="profile-info"><i class="bi bi-envelope"></i><?php echo htmlspecialchars($userEmail); ?></p>
        <p class="profile-info"><i class="bi bi-calendar-event"></i>Joined on <?php echo htmlspecialchars($joinedDate); ?></p>
      </div>
    </div>
    <button class="btn-edit-profile" onclick="window.location.href='edit_profile.php'">Edit Profile</button>
  </div>

</main>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
