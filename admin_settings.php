<?php
session_start();
if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}
$userName = $_SESSION['user_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Settings - Hadees App</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="style.css" rel="stylesheet" />
<style>
  body {
    background: linear-gradient(135deg, #e0f2f1, #a5d6a7);
    font-family: 'Segoe UI', sans-serif;
    animation: fadeIn 0.8s ease-in-out;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .settings-container {
    max-width: 680px;
    margin: 60px auto;
background: linear-gradient(135deg, #e8f5e9, #ffffff);
    
    padding: 45px 40px;
    border-radius: 20px;
    box-shadow: 0 10px 35px rgba(0, 0, 0, 0.12);
    transition: 0.4s ease-in-out;
    animation: slideIn 0.6s ease-in-out;
  }

  @keyframes slideIn {
    0% {
      transform: scale(0.96);
      opacity: 0;
    }
    100% {
      transform: scale(1);
      opacity: 1;
    }
  }

  .settings-title {
    font-size: 2.1rem;
    font-weight: 600;
    color: #14532d;
    margin-bottom: 30px;
    text-align: center;
    border-bottom: 2px solid #e0e0e0;
    padding-bottom: 10px;
  }

  .settings-option {
    margin: 20px 0;
    padding: 18px 26px;
    border-radius: 14px;
    background-color: #f0fdf4;
    color: #166534;
    display: flex;
    justify-content: space-between;
    align-items: center;
    text-decoration: none;
    font-size: 1.1rem;
    font-weight: 500;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(0, 128, 0, 0.05);
    transform: scale(1);
  }

  .settings-option:hover {
    background-color: #d1fae5;
    transform: scale(1.02);
    box-shadow: 0 6px 20px rgba(0, 128, 0, 0.15);
  }

  .settings-option i.bi-chevron-right {
    transition: transform 0.3s ease;
  }

  .settings-option:hover i.bi-chevron-right {
    transform: translateX(6px);
  }

  .settings-option i {
    font-size: 1.3rem;
    transition: transform 0.3s ease;
  }

  .settings-option:hover i:first-child {
    transform: scale(1.2);
  }

  .btn.btn-logout {
    background-color: #dc2626;
    color: white !important;
    border: none;
    padding: 12px 32px;
    margin: 35px auto 0 auto;
    border-radius: 10px;
    font-size: 1rem;
    font-weight: 500;
    width: fit-content;
    display: block;
    box-shadow: 0 8px 20px rgba(220, 38, 38, 0.25);
    animation: pulse 2s infinite;
  }

  .btn-logout:hover {
    background-color: #b91c1c !important;
  }

  @keyframes pulse {
    0% {
      transform: scale(1);
      box-shadow: 0 0 0 rgba(220, 38, 38, 0.4);
    }
    50% {
      transform: scale(1.05);
      box-shadow: 0 0 8px rgba(220, 38, 38, 0.5);
    }
    100% {
      transform: scale(1);
      box-shadow: 0 0 0 rgba(220, 38, 38, 0.4);
    }
  }
</style>

</head>
<body>

<div class="layout-wrapper d-flex">
<?php include 'admin_sidebar.php'; ?>
<div class="main flex-grow-1 p-0">

<nav class="navbar navbar-dark px-3 mb-4 d-flex justify-content-center" style="background-color: #1b4d3e;">
            <span class="navbar-brand text-white fs-4 fw-bold">⚙️ Admin Settings Panel</span>
        </nav>
  <div class="settings-container">

    <a href="admin_edit_profile.php" class="settings-option">
      <span><i class="bi bi-person-circle me-2"></i> Edit Profile</span>
      <i class="bi bi-chevron-right"></i>
    </a>

    <a href="admin_change_password.php" class="settings-option">
      <span><i class="bi bi-lock me-2"></i> Change Password</span>
      <i class="bi bi-chevron-right"></i>
    </a>

    <a href="admin_notifications.php" class="settings-option">
      <span><i class="bi bi-bell me-2"></i> Notification Preferences</span>
      <i class="bi bi-chevron-right"></i>
    </a>

    <a href="admin_delete_account.php" class="settings-option">
      <span><i class="bi bi-trash3 me-2"></i> Delete Account</span>
      <i class="bi bi-chevron-right"></i>
    </a>

<a href="logout.php" class="btn btn-logout">
  <i class="bi bi-box-arrow-right me-2"></i> Logout
</a>

    </form>
  </div>

</div>
<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
