<?php
session_start();
if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Change Password - Hadees App</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    body {
      background: linear-gradient(to right, #dfe9f3, #ffffff);
      font-family: 'Segoe UI', sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    main {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .password-container {
      max-width: 520px;
      width: 100%;
      background: linear-gradient(135deg, #e8f5e9, #ffffff);
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 10px 25px rgba(0, 64, 0, 0.1);
      animation: slideIn 1s ease forwards;
      opacity: 0;
    }

    @keyframes slideIn {
      0% {
        transform: translateY(20px);
        opacity: 0;
      }
      100% {
        transform: translateY(0);
        opacity: 1;
      }
    }

    .form-title {
      font-size: 1.8rem;
      font-weight: bold;
      color: #14532d;
      margin-bottom: 25px;
      text-align: center;
      position: relative;
    }

    

    .form-control {
      border-radius: 10px;
      transition: box-shadow 0.3s ease;
    }

    .form-control:focus {
      box-shadow: 0 0 0 0.25rem rgba(46, 125, 50, 0.3);
      border-color: #2e7d32;
    }

    .btn-update {
      background: linear-gradient(135deg, #388e3c, #a5d6a7);
      color: white;
      font-weight: 600;
      border: none;
      padding: 12px 24px;
      margin-top: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(46, 125, 50, 0.5);
      transition: all 0.3s ease;
    }

    .btn-update:hover {
      background: linear-gradient(135deg, #1b5e20, #81c784);
      transform: translateY(-2px);
      box-shadow: 0 6px 18px rgba(27, 94, 32, 0.7);
    }

    .alert {
      animation: fadeIn 0.8s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: scale(0.95); }
      to { opacity: 1; transform: scale(1); }
    }
  </style>
</head>
<body>

<?php include 'includes/header.php'; ?>
<main class="content">
  <div class="password-container">
    <h2 class="form-title">Change Password</h2>

    <?php if (isset($_GET['status'])): ?>
      <?php if ($_GET['status'] === 'success'): ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
              <i class="bi bi-check-circle-fill"></i> Password updated successfully! Please log in with your new password.
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
      <?php elseif ($_GET['status'] === 'missing_fields'): ?>
          <div class="alert alert-danger">All fields are required.</div>
      <?php elseif ($_GET['status'] === 'password_mismatch'): ?>
          <div class="alert alert-danger">New password and confirm password do not match.</div>
      <?php elseif ($_GET['status'] === 'incorrect_password'): ?>
          <div class="alert alert-danger">Current password is incorrect.</div>
      <?php elseif ($_GET['status'] === 'error'): ?>
          <div class="alert alert-danger">Something went wrong. Please try again.</div>
      <?php endif; ?>
    <?php endif; ?>

    <form action="update_password.php" method="post">
      <div class="mb-3">
        <label for="currentPassword" class="form-label">Current Password</label>
        <input type="password" class="form-control" id="currentPassword" name="current_password" required>
      </div>
      <div class="mb-3">
        <label for="newPassword" class="form-label">New Password</label>
        <input type="password" class="form-control" id="newPassword" name="new_password" required>
      </div>
      <div class="mb-3">
        <label for="confirmPassword" class="form-label">Confirm New Password</label>
        <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
      </div>
      <button type="submit" class="btn btn-update w-100">
        <i class="bi bi-shield-lock-fill"></i> Update Password
      </button>
    </form>
  </div>
</main>
<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
