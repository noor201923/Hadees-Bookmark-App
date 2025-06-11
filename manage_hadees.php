<?php
// manage_ahadees.php
session_start();

include 'includes/db.php'; 

$query = "SELECT * FROM hadees WHERE is_deleted = 0 ORDER BY hadees_date_added DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Manage Ahadees</title>

<!-- Include Bootstrap CSS & Bootstrap Icons CDN here if not included in your sidebar/footer -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

<style>
body {
      background: linear-gradient(135deg, #e0f2f1, #a5d6a7);
    font-family: 'Segoe UI', sans-serif;
    }
    .main {
        margin-left: 0px;
        padding: 20px;
    }
    .table-wrapper {
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        animation: fadeIn 0.5s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    /* Toast styling */
    #toast {
        position: fixed;
        bottom: 20px;
        right: 20px;
        min-width: 250px;
        display: none;
        z-index: 9999;
    }
</style>
</head>
<body>
<div class="layout-wrapper d-flex">
<?php include 'admin_sidebar.php'; ?>
<div class="main flex-grow-1 p-0">
    <nav class="navbar navbar-dark px-3 mb-4 d-flex justify-content-center" style="background-color: #1b4d3e;">
        <span class="navbar-brand text-white fw-bold fs-4 ">📖 Manage Ahadees</span>
    </nav>
<div class="container py-5 justify-content-center">
    <?php if (isset($_SESSION['success_msg']) || isset($_SESSION['error_msg'])): ?>
        <div class="alert <?= isset($_SESSION['success_msg']) ? 'alert-success' : 'alert-danger' ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['success_msg'] ?? $_SESSION['error_msg']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success_msg'], $_SESSION['error_msg']); ?>
    <?php endif; ?>

    <div class="container">
        <div class="table-wrapper">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-success">
                    <tr>
                        <th>#</th>
                        <th>Book Name</th>
                        <th>Hadees Text</th>
                        <th>Date Added</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                    <tr data-id="<?= $row['hadees_id'] ?>">
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($row['hadees_book_name']) ?></td>
                        <td><?= htmlspecialchars($row['hadees_text']) ?></td>
                        <td><?= date('d M, Y', strtotime($row['hadees_date_added'])) ?></td>
                        <td>
                            <a href="edit_hadees.php?hadees_id=<?= $row['hadees_id'] ?>" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $row['hadees_id'] ?>">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
</main>
</div>


<!-- Toast notification -->
<div id="toast">
    <div class="toast-body bg-danger text-white d-flex justify-content-between align-items-center rounded">
        <span id="toast-message"></span>
        <button id="toast-undo" class="btn btn-sm btn-light ms-3">Undo</button>
        <button id="toast-close" class="btn btn-sm btn-light ms-2">&times;</button>
    </div>
</div>
</div>

<!-- Bootstrap JS Bundle (Popper + Bootstrap JS) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toast-message');
    const toastUndo = document.getElementById('toast-undo');
    const toastClose = document.getElementById('toast-close');
    let lastDeletedId = null;
    let lastDeletedRow = null;
    let toastTimeout = null;

    function showToast(message) {
        toastMessage.textContent = message;
        toast.style.display = 'block';

        // Clear any existing timeout
        if (toastTimeout) clearTimeout(toastTimeout);
        
        // Auto hide after 5 seconds
        toastTimeout = setTimeout(() => {
            hideToast();
            lastDeletedId = null;
            lastDeletedRow = null;
        }, 5000);
    }

    function hideToast() {
        toast.style.display = 'none';
    }

    // Delete button click handler
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function () {
            if (!confirm('Are you sure you want to delete this hadees?')) return;

            const hadeesId = this.dataset.id;
            lastDeletedId = hadeesId;
            lastDeletedRow = this.closest('tr');

            fetch('ajax_delete_hadees.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'hadees_id=' + encodeURIComponent(hadeesId)
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    // Hide row
                    if (lastDeletedRow) lastDeletedRow.style.display = 'none';
                    showToast('Hadees deleted!');
                } else {
                    alert('Delete failed: ' + data.message);
                }
            })
            .catch(() => alert('Error while deleting'));
        });
    });

    // Undo button click handler
    toastUndo.addEventListener('click', function () {
        if (!lastDeletedId) return;

        fetch('ajax_undo_delete.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'hadees_id=' + encodeURIComponent(lastDeletedId)
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                // Show row back
                if (lastDeletedRow) lastDeletedRow.style.display = '';
                hideToast();
                lastDeletedId = null;
                lastDeletedRow = null;
            } else {
                alert('Undo failed: ' + data.message);
            }
        })
        .catch(() => alert('Error while undoing'));
    });

    // Close toast button
    toastClose.addEventListener('click', hideToast);
});
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>
