<?php
session_start();
$error = "";

// ✅ Show success message after password update
if (isset($_GET['status']) && $_GET['status'] === 'success') {
    $_SESSION['success_msg'] = "✅ Password updated successfully! Please log in with your new password.";
}

// Redirect if already logged in
if (isset($_SESSION['user_name'])) {
    header("Location: dashboard.php");
    exit();
}

// Optional: success message after account deletion
$accountDeleted = false;
if (isset($_GET['status']) && $_GET['status'] === 'deleted') {
    $accountDeleted = true;
}

// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "hadees_app");

    if ($conn->connect_error) {
        $error = "❌ Database connection failed.";
    } else {
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        $sql = "SELECT user_id, user_name, user_password, role, user_email FROM users WHERE user_email=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($user_id, $user_name, $hashed_password, $role, $user_email);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                $conn->query("UPDATE users SET last_login = NOW() WHERE user_id = $user_id");

                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_name'] = $user_name;
                $_SESSION['user_email'] = $user_email;
                $_SESSION['role'] = $role;

                // Insert login notification
                $notif_msg = "$user_name just logged in.";
                $notif_stmt = $conn->prepare("INSERT INTO notifications (message, user_id) VALUES (?, ?)");
                $notif_stmt->bind_param("si", $notif_msg, $user_id);
                $notif_stmt->execute();

                header("Location: dashboard.php");
                exit();
            } else {
                $error = "❌ Invalid password.";
            }
        } else {
            $error = "❌ Email not found.";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login - Hadees App</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    body {
      background-image: url('img.png');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      min-height: 100vh;
    }
    .form-container {
      background-color: rgba(255, 255, 255, 0.9);
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 0 10px rgba(0,0,0,0.3);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .form-container:hover {
      transform: translateY(-8px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.4);
    }
    h2 {
      color: white;
      font-weight: bold;
      text-shadow: 0 0 8px rgba(0,0,0,0.7);
      animation: glow 2.5s ease-in-out infinite alternate;
    }
    @keyframes glow {
      from {
        text-shadow: 0 0 8px rgba(255,255,255,0.7);
      }
      to {
        text-shadow: 0 0 20px rgba(0,123,255,0.9);
      }
    }

    /* Alert Animations */
    .alert {
      opacity: 1;
      transform: translateY(0);
      transition: opacity 0.6s ease, transform 0.6s ease;
    }
    .alert.hide {
      opacity: 0;
      transform: translateY(-20px);
    }

    /* Password toggle button */
    .input-group .btn {
      border-top-left-radius: 0;
      border-bottom-left-radius: 0;
    }
  </style>
</head>
<body>
  <div class="container mt-5">
    <h2 class="text-center mb-4">Login</h2>

    <?php if ($accountDeleted): ?>
      <div class="alert alert-success text-center alert-dismissible fade show" role="alert" id="successAlert">
        ✅ Account successfully deleted.
      </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success_msg'])): ?>
      <div class="alert alert-success text-center alert-dismissible fade show" role="alert" id="successAlert">
        <?php echo $_SESSION['success_msg']; ?>
      </div>
      <?php unset($_SESSION['success_msg']); ?>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger text-center"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="login.php" method="post" class="w-50 mx-auto form-container" id="loginForm">
      <div class="mb-3">
        <label>Email:</label>
        <input type="email" name="email" class="form-control" required />
      </div>
      <div class="mb-3">
        <label>Password:</label>
        <div class="input-group">
          <input type="password" name="password" id="password" class="form-control" required />
          <button type="button" class="btn btn-outline-primary" onclick="togglePassword()" title="Show/Hide Password">
            <i class="bi bi-eye" id="toggleIcon"></i>
          </button>
        </div>
      </div>
      <button type="submit" class="btn btn-primary w-100">Login</button>
      <p class="mt-3 text-center">Don't have an account? <a href="register.php" class="text-success fw-bold">Register</a></p>
    </form>
  </div>

  <script>
    function togglePassword() {
      const passwordField = document.getElementById('password');
      const icon = document.getElementById('toggleIcon');
      if (passwordField.type === 'password') {
        passwordField.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
      } else {
        passwordField.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
      }
    }

    // Auto-hide success alert smoothly
    setTimeout(() => {
      const alert = document.getElementById('successAlert');
      if (alert) {
        alert.classList.add('hide'); // triggers CSS fade + slide up animation
        setTimeout(() => alert.remove(), 600); // removes after animation
      }
    }, 4000);
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
