<?php
session_start();
include("includes/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

// Determine sort order from query param (default desc = newest first)
$sort_order = 'DESC';
if (isset($_GET['sort']) && strtolower($_GET['sort']) === 'asc') {
    $sort_order = 'ASC';
}

$sql = "SELECT b.bookmark_id, b.hadees_id, b.bookmarked_at, b.note, h.hadees_text, h.hadees_book_name
        FROM bookmarks b
        JOIN hadees h ON b.hadees_id = h.hadees_id
        WHERE b.user_id = ? AND b.is_deleted = 0
        ORDER BY b.bookmarked_at $sort_order";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>My Bookmarks</title>
  <!-- Google Fonts for Islamic vibe -->
  <link href="https://fonts.googleapis.com/css2?family=Amiri&family=Cairo&display=swap" rel="stylesheet" />
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  
  <style>
    /* Your existing CSS here */
    :root {
      --primary-green: #2e7d32;
      --primary-gold: #d4af37;
      --background-beige: #f9f4ef;
      --text-dark: #2b2b2b;
      --card-shadow: rgba(46, 125, 50, 0.2);
margin-left:550px;
margin-right:300px;

    }

    body {
     background: linear-gradient(135deg, #e0f2f1, #a5d6a7);
    font-family: 'Segoe UI', sans-serif;
      color: #1b4332;
      min-height: 100vh;
      position: relative;
      overflow-x: hidden;
    }


    body::before {
      content: "";
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      pointer-events: none;
      opacity: 0.05;
      
      background-repeat: repeat;
      background-position: center;
      background-size: 200px 200px;
      z-index: 0;
    }

    .container {

      position: relative;
      z-index: 1;
      background: rgba(255,255,255,0.95);

      padding: 30px 400px;
margin-left:200px;
      border-radius: 15px;
      box-shadow: 0 6px 18px rgba(46,125,50,0.15);

    }

    h3 {
      font-family: 'Amiri', serif;
      color: var(--primary-green);
      text-align: center;
      margin-bottom: 1.5rem;

      font-weight: 700;
      text-shadow: 1px 1px 2px #bfae5f;
    }

    .sort-container {
      text-align: center;
      margin-bottom: 1.5rem;
    }

    .card {
      border: 2px solid var(--primary-green);
      border-radius: 1rem;
      box-shadow: 0 6px 12px var(--card-shadow);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
     
background: linear-gradient(135deg, #e0f2f1, #a5d6a7);
    font-family: 'Segoe UI', sans-serif;

    }

    .card:hover {
      transform: translateY(-8px);
      box-shadow: 0 12px 24px var(--card-shadow);
    }

    .card-title {
      color: var(--primary-green);
      font-family: 'Amiri', serif;
      font-weight: 700;
    }

    .card-text {
      font-size: 1.1rem;
      line-height: 1.6;
      white-space: pre-line;
    }

    .bookmark-note {
      font-style: italic;
      color: #555;
      white-space: pre-line;
    }

    .text-muted {
      font-size: 0.85rem;
      color: #777 !important;
    }

    .btn-outline-secondary {
      border-color: var(--primary-green);
      color: var(--primary-green);
      transition: background-color 0.3s ease, color 0.3s ease;
    }

    .btn-outline-secondary:hover {
      background-color: var(--primary-green);
      color: white;
      box-shadow: 0 0 8px var(--primary-green);
    }

    .btn-outline-danger {
      border-color: #b71c1c;
      color: #b71c1c;
      transition: background-color 0.3s ease, color 0.3s ease;
    }

    .btn-outline-danger:hover {
      background-color: #b71c1c;
      color: white;
      box-shadow: 0 0 8px #b71c1c;
    }

    #toast-container {
      position: fixed;
      top: 1rem;
      right: 1rem;
      z-index: 1080;
      font-family: 'Cairo', sans-serif;
    }

    .toast {
      border-radius: 0.5rem;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      font-weight: 600;
    }

    .modal-content {
      border-radius: 1rem;
      border: 2px solid var(--primary-green);
      font-family: 'Cairo', sans-serif;
    }

    .modal-header {
      background-color: var(--primary-green);
      color: white;
      border-top-left-radius: 1rem;
      border-top-right-radius: 1rem;
    }

    .modal-footer {
      border-top: 1px solid #ddd;
    }

    .btn-primary {
      background-color: var(--primary-green);
      border-color: var(--primary-green);
      transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
      background-color: #1b4d23;
      border-color: #1b4d23;
    }

    .btn-secondary {
      background-color: #bfbfbf;
      border-color: #bfbfbf;
    }

    /* Responsive tweaks */
    @media (max-width: 576px) {
      .card {
        font-size: 0.95rem;
      }
      h3 {
        font-size: 1.8rem;
      }
    }
  </style>
</head>
<body>
<?php include 'includes/header.php'; ?>



<div class="container mt-5 mb-5">
  <h3><i class="bi bi-bookmark-fill"></i> My Bookmarks</h3>

  <div class="sort-container">
    <label for="sortSelect" class="form-label me-2"><strong>Sort by Date:</strong></label>
    <select id="sortSelect" class="form-select d-inline-block w-auto">
      <option value="desc" <?= $sort_order === 'DESC' ? 'selected' : '' ?>>Newest First</option>
      <option value="asc" <?= $sort_order === 'ASC' ? 'selected' : '' ?>>Oldest First</option>
    </select>
  </div>

  <div id="toast-container"></div>

  <?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="card mb-4 p-3" id="bookmark-<?= $row['bookmark_id'] ?>">
        <div class="card-body">
          <h5 class="card-title"><?= htmlspecialchars($row['hadees_book_name']); ?></h5>
          <p class="card-text"><?= nl2br(htmlspecialchars($row['hadees_text'])); ?></p>
          <p><strong>Note:</strong> <span class="bookmark-note"><?= nl2br(htmlspecialchars($row['note'] ?? 'No notes added')); ?></span></p>
          <small class="text-muted">Bookmarked on: <?= date("F d, Y", strtotime($row['bookmarked_at'])); ?></small>
          <br><br>
          <button class="btn btn-sm btn-outline-success edit-note-btn" 
                  data-bookmark-id="<?= $row['bookmark_id']; ?>" 
                  data-note="<?= htmlspecialchars($row['note']); ?>">
            <i class="bi bi-pencil"></i> Edit Note
          </button>
          <button class="btn btn-sm btn-outline-danger delete-bookmark-btn" data-bookmark-id="<?= $row['bookmark_id']; ?>">
            <i class="bi bi-trash"></i> Delete
          </button>
        </div>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p class="text-center fw-semibold mt-5" style="font-size: 1.2rem;">
      No bookmarks found. Add some from your favorite Ahadees!
    </p>
  <?php endif; ?>
</div>

<!-- Edit Note Modal (keep your existing modal here) -->
<div class="modal fade" id="editNoteModal" tabindex="-1" aria-labelledby="editNoteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editNoteForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editNoteModalLabel">Edit Bookmark Note</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="editBookmarkId" name="bookmark_id" />
        <div class="mb-3">
          <label for="editBookmarkNote" class="form-label">Note:</label>
          <textarea class="form-control" id="editBookmarkNote" name="note" rows="3" required></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Save Changes</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>

    </form>
  </div>
</div>
<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery (optional for convenience) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
  // Sorting dropdown
  $('#sortSelect').on('change', function () {
    const sort = $(this).val();
    const url = new URL(window.location.href);
    url.searchParams.set('sort', sort);
    window.location.href = url.toString();
  });

  // Open edit modal with note
  $('.edit-note-btn').on('click', function () {
    const bookmarkId = $(this).data('bookmark-id');
    const note = $(this).data('note') || '';
    $('#editBookmarkId').val(bookmarkId);
    $('#editBookmarkNote').val(note);
    const editModal = new bootstrap.Modal(document.getElementById('editNoteModal'));
    editModal.show();
  });

  // Save edited note via AJAX
  $('#editNoteForm').on('submit', function (e) {
    e.preventDefault();
    const formData = $(this).serialize();

    $.post('update_note.php', formData, function (response) {
      if (response.success) {
        showToast('Note updated successfully!', 'success');
        setTimeout(() => location.reload(), 1000);
      } else {
        showToast('Failed to update note.', 'danger');
      }
    }, 'json');
  });

  // Delete bookmark (soft delete) via AJAX
  $('.delete-bookmark-btn').on('click', function () {
    const bookmarkId = $(this).data('bookmark-id');
    const card = $('#bookmark-' + bookmarkId);
    $.post('ajax_soft_delete.php', { bookmark_id: bookmarkId }, function (res) {
      if (res.success) {
        card.slideUp();
        showToast(`Bookmark deleted. <a href="#" class="text-light fw-bold undo-link" data-id="${bookmarkId}">Undo</a>`);
      }
    }, 'json');
  });

  $(document).on('click', '.undo-link', function (e) {
    e.preventDefault();
    const id = $(this).data('id');
    $.post('ajax_restore.php', { bookmark_id: id }, function (res) {
      if (res.success) {
        location.reload();
      }
    }, 'json');
  });

  function showToast(message) {
    const toast = $(`
      <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" data-bs-delay="4000">
        <div class="d-flex">
          <div class="toast-body">${message}</div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
      </div>
    `);
    $('#toast-container').append(toast);
    const bsToast = new bootstrap.Toast(toast[0]);
    bsToast.show();
  }

  // Toast with Undo option
  function showToastWithUndo(message, type, bookmarkId) {
    const toastId = `toast-${Date.now()}`;
    const toastHTML = `
      <div id="${toastId}" class="toast align-items-center text-bg-${type} border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex justify-content-between">
          <div class="toast-body">${message}</div>
          <div class="me-2">
            <button class="btn btn-sm btn-outline-light undo-btn" data-bookmark-id="${bookmarkId}">Undo</button>
          </div>
        </div>
      </div>`;
    $('#toast-container').append(toastHTML);
    const toastEl = document.getElementById(toastId);
    new bootstrap.Toast(toastEl, { delay: 5000 }).show();

    // Undo button listener
    $(toastEl).on('click', '.undo-btn', function () {
      $.post('undo_delete.php', { bookmark_id: bookmarkId }, function (response) {
        if (response.success) {
          $(`#bookmark-${bookmarkId}`).show();
          showToast('Deletion undone.', 'success');
        } else {
          showToast('Undo failed.', 'danger');
        }
      }, 'json');
    });
  }
</script>


<?php include 'includes/footer.php'; ?>
</body>
</html>