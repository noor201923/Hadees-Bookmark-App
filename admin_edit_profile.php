<?php
session_start();
if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

include 'includes/db.php';

$userName = $_SESSION['user_name'];
$msg = "";

$query = $conn->prepare("SELECT user_id, user_name, user_email, profile_image FROM users WHERE user_name = ?");
$query->bind_param("s", $userName);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newName = $_POST['user_name'];
    $newEmail = $_POST['user_email'];
    $userId = $_POST['user_id'];
    $profileImage = $user['profile_image'];

    if (!empty($_FILES['profile_image']['name'])) {
        $imgName = time() . '_' . basename($_FILES["profile_image"]["name"]);
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        $targetFilePath = $targetDir . $imgName;
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $targetFilePath)) {
            $profileImage = $imgName;
        }
    }

    $update = $conn->prepare("UPDATE users SET user_name = ?, user_email = ?, profile_image = ? WHERE user_id = ?");
    $update->bind_param("sssi", $newName, $newEmail, $profileImage, $userId);
    $update->execute();

    $_SESSION['user_name'] = $newName;
    header("Location: admin_profile.php?update=success");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Profile - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e0f2f1, #a5d6a7);
    font-family: 'Segoe UI', sans-serif;
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .profile-card {
            background: linear-gradient(135deg, #e8f5e9, #ffffff);
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            padding: 30px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .profile-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        }

        /* Profile image container centered with pencil icon */
        .profile-img-container {
            position: relative;
            width: 130px;
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 30px;
        }

        .profile-img {
            width: 130px;
            height: 130px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #0d6efd;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: default;
        }

        .profile-img:hover {
            transform: scale(1.07);
            box-shadow: 0 0 15px rgba(13, 110, 253, 0.5);
        }

        .edit-icon-label {
            position: absolute;
            bottom: 0;
            right: 0;
            background-color: #0d6efd;
            border-radius: 50%;
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 0 8px rgba(13, 110, 253, 0.8);
            transition: background-color 0.3s ease;
        }

        .edit-icon-label:hover {
            background-color: #084298 !important;
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
            transition: 0.3s ease;
        }

        .btn-success {
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-success:hover {
            background-color: #198754;
            transform: scale(1.02);
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

        <div class="container d-flex align-items-center justify-content-center" style="min-height: 80vh;">
            <main class="col-md-8 col-lg-9">
                <?php if ($msg): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $msg ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="profile-card mx-auto col-md-8">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">

                        <!-- Updated Profile Image + Pencil Icon -->
                        <div class="profile-img-container">
                            <img id="preview" src="<?= $user['profile_image'] ? 'uploads/' . htmlspecialchars($user['profile_image']) : 'includes/default.png' ?>" 
                                 alt="Profile Picture" 
                                 class="profile-img rounded-circle border border-4 border-primary">

                            <label for="profile_image" class="edit-icon-label" title="Change Profile Picture" aria-label="Change Profile Picture">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="white" viewBox="0 0 24 24" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 20h9" />
                                    <path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4 12.5-12.5z" />
                                </svg>
                            </label>
                            <input type="file" id="profile_image" name="profile_image" accept="image/*" onchange="previewImage(event)" style="display:none;" aria-hidden="true" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="user_name" value="<?= htmlspecialchars($user['user_name']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="user_email" value="<?= htmlspecialchars($user['user_email']) ?>" required>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Update Profile</button>
                    </form>
                </div>
            </main>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function previewImage(event) {
    const input = event.target;
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
</body>
</html>
