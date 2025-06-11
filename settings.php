<?php
session_start();
if (!isset($_SESSION['user_id'])) {
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
      background: linear-gradient(to right, #e9f5ee, #f3f8fa);
      font-family: 'Segoe UI', sans-serif;
    }
.content {
display:flex;
justify-content: center;
align-items: center;
min-height: 100vh;
width: 100%;
 
}
    .settings-container {
      max-width: 650px;
width: 100%;
      margin: 60px auto;
      background: linear-gradient(135deg, #e8f5e9, #ffffff);
      padding: 40px 35px;
      border-radius: 18px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
      transition: 0.3s ease-in-out;
animation: fadeIn 0.5s ease;
    }
    .settings-title {
      font-size: 2rem;
      font-weight: 600;
      color: #14532d;
      margin-bottom: 30px;
      text-align: center;
      border-bottom: 2px solid #e0e0e0;
      padding-bottom: 10px;
    }
    .settings-option {
      margin: 18px 0;
      padding: 16px 24px;
      border-radius: 12px;
      background-color: #f0fdf4;
      color: #166534;
      display: flex;
      justify-content: space-between;
      align-items: center;
      text-decoration: none;
      font-size: 1.1rem;
      font-weight: 500;
      transition: all 0.25s ease;
      box-shadow: 0 4px 12px rgba(0, 128, 0, 0.05);
    }
    .settings-option i {
      transition: transform 0.3s ease;
    }
    .settings-option:hover {
      background-color: #dcfce7;
      box-shadow: 0 6px 18px rgba(0, 128, 0, 0.1);
    }
    .settings-option:hover i.bi-chevron-right {
      transform: translateX(4px);
    }
   .btn.btn-logout {
  background-color: #dc2626; /* Bootstrap 'danger' red */
  color: white !important;
  border: none;
  padding: 10px 30px;
  margin: 30px auto 0 auto;
  border-radius: 10px;
  font-size: 1rem;
  font-weight: 500;
  transition: background-color 0.3s ease;
  width: fit-content;
display: block;

}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
}
.btn-logout:hover {
  background-color: #b91c1c !important; /* Darker red */
}

  </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="content">
  <div class="settings-container">
    <h2 class="settings-title">⚙️ Settings Panel</h2>

    <a href="edit_profile.php" class="settings-option">
      <span><i class="bi bi-person-circle me-2"></i> Edit Profile</span>
      <i class="bi bi-chevron-right"></i>
    </a>

    <a href="change_password.php" class="settings-option">
      <span><i class="bi bi-lock me-2"></i> Change Password</span>
      <i class="bi bi-chevron-right"></i>
    </a>

    <a href="notifications.php" class="settings-option">
      <span><i class="bi bi-bell me-2"></i> Notification Preferences</span>
      <i class="bi bi-chevron-right"></i>
    </a>

    <a href="delete_account.php" class="settings-option">
      <span><i class="bi bi-trash3 me-2"></i> Delete Account</span>
      <i class="bi bi-chevron-right"></i>
    </a>

    <a href="logout.php"
      class="btn btn-logout"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
    </form>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
