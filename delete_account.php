<?php
session_start();
if (!isset($_SESSION['user_name'])) {
    header("Location: login.php?status=deleted");
    exit();
}
$userName = $_SESSION['user_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Delete Account - Hadees App</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
main {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .delete-container {
      max-width: 600px;
width: 100%;
      margin: 60px auto;
      background: linear-gradient(135deg, #e8f5e9, #ffffff);
      padding: 40px 35px;
      border-radius: 20px;
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
      text-align: center;
animation: fadeInUp 0.7s ease forwards;
position: relative;
      overflow: hidden;
    }
    .delete-title {
      font-size: 2rem;
      font-weight: 700;
      color: #b30000;
      margin-bottom: 25px;
    }
    .warning-text {
      color: #6c757d;
      font-size: 1.1rem;
      margin-bottom: 30px;
      font-weight: 500;
    }
    .form-check-label.text-danger {
      font-weight: 600;
      font-size: 1rem;
      user-select: none;
    }
   .btn-delete {
  background: linear-gradient(45deg, #ff5f6d, #ffc371);
  color: #3b0a0a; /* Dark red-brown for better contrast */
  font-weight: bold;
  border: none;
  padding: 10px 25px;
  font-size: 1.1rem;
  border-radius: 12px;
  box-shadow: 0 4px 8px rgba(255, 95, 109, 0.4);
  transition: background 0.3s ease;
}

.btn-delete:hover {
  background: linear-gradient(45deg, #ff7a82, #ffd98f);
  color: #520000;
  box-shadow: 0 6px 12px rgba(255, 122, 130, 0.6);
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
<div class="delete-container">

  <?php if (isset($_GET['status']) && $_GET['status'] == 'deleted'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle-fill"></i> Your account has been <strong>successfully deleted.</strong> We're sorry to see you go.
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <h2 class="delete-title"><i class="bi bi-trash3-fill"></i> Delete Your Account</h2>
  <p class="warning-text">
    This action will <strong>permanently delete</strong> your account and all related data.<br />
    <span style="color:#b30000; font-weight:700;">This cannot be undone.</span>
  </p>

  <form action="delete_account_action.php" method="post" onsubmit="return confirmDelete();">
    <div class="form-check mb-4">
      <input class="form-check-input" type="checkbox" id="confirm" required>
      <label class="form-check-label text-danger" for="confirm">
        I understand and want to permanently delete my account.
      </label>
    </div>

    <button type="submit" class="btn btn-delete">
      <i class="bi bi-trash-fill"></i> Confirm Delete
    </button>
  </form>
</div>
</main>
<?php include 'includes/footer.php'; ?>

<script>
function confirmDelete() {
  return confirm("⚠️ Are you absolutely sure you want to delete your account? This action cannot be undone!");
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
