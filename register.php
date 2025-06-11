<?php
session_start();
$success = $error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $conn = new mysqli("localhost", "root", "", "hadees_app");

  if ($conn->connect_error) {
    $error = "❌ Database connection failed: " . $conn->connect_error;
  } else {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Backend validation
    if ($password !== $confirm_password) {
      $error = "⚠️ Password and Confirm Password do not match.";
    } elseif (strlen($password) < 8 || strlen($password) > 20) {
      $error = "⚠️ Password must be 8-20 characters.";
    } elseif (!preg_match('/[A-Z]/', $password)) {
      $error = "⚠️ Password must have at least one uppercase letter.";
    } elseif (!preg_match('/[a-z]/', $password)) {
      $error = "⚠️ Password must have at least one lowercase letter.";
    } elseif (!preg_match('/\d/', $password)) {
      $error = "⚠️ Password must contain at least one number.";
    } elseif (!preg_match('/[@$!%*?&]/', $password)) {
      $error = "⚠️ Password must include at least one special character (@$!%*?&).";
    } else {
      $checkEmail = $conn->prepare("SELECT user_email FROM users WHERE user_email = ?");
      $checkEmail->bind_param("s", $email);
      $checkEmail->execute();
      $checkEmail->store_result();

      if ($checkEmail->num_rows > 0) {
        $error = "⚠️ Email is already registered. Please <a href='login.php'>login</a>.";
      } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (user_name, user_email, user_password, role) VALUES (?, ?, ?, 'user')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $email, $hashed_password);

        if ($stmt->execute()) {
          // ✅ Insert notification for admin
          $admin_id = 1; // hardcoded
          $notif_msg = "New user registered: $name";

          $user_id = $stmt->insert_id; // Get the ID of the new user

          $notif_stmt = $conn->prepare("INSERT INTO notifications (message, user_id) VALUES (?, ?)");
          $notif_stmt->bind_param("si", $notif_msg, $user_id);
          $notif_stmt->execute();
          $notif_stmt->close();

          $success = "✅ Registration successful! <a href='login.php'>Login here</a>.";
        } else {
          $error = "❌ Something went wrong: " . $stmt->error;
        }
        $stmt->close();
      }
      $checkEmail->close();
    }
    $conn->close();
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register - Hadees App</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
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
    }
    h2 {
      color: white;
      font-weight: bold;
    }

    /* Fade in animation for form */
    @keyframes fadeInUp {
      0% {
        opacity: 0;
        transform: translateY(30px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .animated-form {
      animation: fadeInUp 1s ease-out;
    }

    /* Shake animation for error box */
    @keyframes shake {
      0% { transform: translateX(0); }
      25% { transform: translateX(-10px); }
      50% { transform: translateX(10px); }
      75% { transform: translateX(-10px); }
      100% { transform: translateX(0); }
    }

    .shake {
      animation: shake 0.5s;
    }
  </style>
</head>
<body>
  <div class="container mt-5">
    <h2 class="text-center mb-4">Register</h2>

    <?php if (!empty($success)): ?>
      <div class="alert alert-success text-center"><?php echo $success; ?></div>
    <?php elseif (!empty($error)): ?>
      <div class="alert alert-warning text-center shake"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="register.php" method="post" class="w-50 mx-auto form-container animated-form" id="registerForm">
      <div class="mb-3">
        <label>Name:</label>
        <input type="text" name="name" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Email:</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Password:</label>
        <div class="input-group">
          <input type="password" name="password" id="password" class="form-control"
            required minlength="8" maxlength="20"
            pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,20}">
          <button type="button" class="btn btn-outline-success" onclick="togglePassword('password', 'toggleIcon')" title="Show Password">
            <i class="bi bi-eye" id="toggleIcon"></i>
          </button>
        </div>
        <small class="form-text text-muted">
          Must include uppercase, lowercase, number, and special character.
        </small>
      </div>
      <div class="mb-3">
        <label>Confirm Password:</label>
        <div class="input-group">
          <input type="password" name="confirm_password" id="confirm_password" class="form-control"
            required minlength="8" maxlength="20">
          <button type="button" class="btn btn-outline-success" onclick="togglePassword('confirm_password', 'toggleConfirmIcon')" title="Show Confirm Password">
            <i class="bi bi-eye" id="toggleConfirmIcon"></i>
          </button>
        </div>
      </div>
      <button type="submit" name="register" class="btn btn-success w-100">Register</button>
      <p class="mt-3 text-center">Already have an account? <a href="login.php" class="text-primary fw-bold">Login</a></p>
    </form>
  </div>

  <script>
    function togglePassword(fieldId, iconId) {
      const input = document.getElementById(fieldId);
      const icon = document.getElementById(iconId);
      const isHidden = input.type === "password";
      input.type = isHidden ? "text" : "password";
      icon.classList.toggle("bi-eye");
      icon.classList.toggle("bi-eye-slash");
    }

    document.getElementById('registerForm').addEventListener('submit', function(event) {
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirm_password').value;
      const strongPasswordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])/;

      if (!strongPasswordPattern.test(password)) {
        alert('Password must include uppercase, lowercase, number, and special character.');
        event.preventDefault();
      } else if (password !== confirmPassword) {
        alert('Password and Confirm Password do not match.');
        event.preventDefault();
      }
    });
  </script>
</body>
</html>
