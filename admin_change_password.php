<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'includes/db.php';

$user_id = $_SESSION['user_id'];
$msg = $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'];
    $newPassword     = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    $sql = "SELECT user_password FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($hashedPassword);
    $stmt->fetch();
    $stmt->close();

    if (password_verify($currentPassword, $hashedPassword)) {
        if ($newPassword === $confirmPassword) {
            $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE users SET user_password = ? WHERE user_id = ?");
            $update->bind_param("si", $newHashedPassword, $user_id);
            if ($update->execute()) {
    // Log out the user after password change
    session_destroy();
    session_start();
    $_SESSION['success_msg'] = "Password updated successfully! Please login with your new password.";
    header("Location: login.php");
    exit();
}
else {
                $error = "Something went wrong. Try again!";
            }
        } else {
            $error = "New passwords do not match!";
        }
    } else {
        $error = "Current password is incorrect!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Change Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e0f2f1, #a5d6a7);
    font-family: 'Segoe UI', sans-serif;
        }

        .card {
background: linear-gradient(135deg, #e8f5e9, #ffffff);
            border-radius: 16px;
            box-shadow: 0 15px 25px rgba(0,0,0,0.1);
            animation: slideFade 0.6s ease;
        }

        @keyframes slideFade {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .strength-meter {
            height: 8px;
            border-radius: 4px;
        }

        .strength-weak { background-color: red; width: 33%; }
        .strength-medium { background-color: orange; width: 66%; }
        .strength-strong { background-color: green; width: 100%; }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 38px;
            cursor: pointer;
            z-index: 2;
        }
    </style>
</head>
<body>

<div class="layout-wrapper d-flex">
<?php include 'admin_sidebar.php'; ?>
<div class="main flex-grow-1 p-0 ">
 <nav class="navbar navbar-dark px-3 mb-4 d-flex justify-content-center" style="background-color: #1b4d3e;">
    <span class="navbar-brand text-white fs-4 fw-bold">⚙️ Admin Settings Panel</span>
</nav>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <?php if ($msg): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i><?= $msg ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card p-4 bg-white">
                <h4 class="text-center mb-3"><i class="bi bi-shield-lock-fill me-2"></i>Change Password</h4>
                <form method="POST" novalidate>
                    <div class="mb-3 position-relative">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-control password-field" required>
                        <i class="bi bi-eye-slash toggle-password"></i>
                    </div>

                    <div class="mb-3 position-relative">
                        <label class="form-label">New Password</label>
                        <input type="password" id="new_password" name="new_password" class="form-control password-field" required>
                        <i class="bi bi-eye-slash toggle-password"></i>
                        <div class="strength-meter mt-2 bg-light">
                            <div id="strengthBar" class="strength-meter-inner rounded"></div>
                        </div>
                    </div>

                    <div class="mb-3 position-relative">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control password-field" required>
                        <i class="bi bi-eye-slash toggle-password"></i>
                    </div>

                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-arrow-repeat me-1"></i>Update Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Show/hide password toggle
document.querySelectorAll('.toggle-password').forEach(toggle => {
    toggle.addEventListener('click', function () {
        const input = this.previousElementSibling;
        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);
        this.classList.toggle('bi-eye');
        this.classList.toggle('bi-eye-slash');
    });
});

// Password Strength Meter
document.getElementById('new_password').addEventListener('input', function () {
    const strengthBar = document.getElementById('strengthBar');
    const value = this.value;
    let strength = 0;

    if (value.length > 5) strength++;
    if (value.match(/[a-z]+/)) strength++;
    if (value.match(/[A-Z]+/)) strength++;
    if (value.match(/[0-9]+/)) strength++;
    if (value.match(/[\W]+/)) strength++;

    strengthBar.className = 'strength-meter-inner rounded';

    if (strength <= 2) {
        strengthBar.classList.add('strength-weak');
    } else if (strength <= 4) {
        strengthBar.classList.add('strength-medium');
    } else {
        strengthBar.classList.add('strength-strong');
    }
});
</script>
</body>
</html>
