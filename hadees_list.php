<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "hadees_app");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sortOrder = (isset($_GET['sort']) && $_GET['sort'] === 'oldest') ? 'ASC' : 'DESC';

$searchSql = "";
if ($search !== '') {
    $search = $conn->real_escape_string($search);
    $searchSql = "WHERE hadees_text LIKE '%$search%' OR hadees_book_name LIKE '%$search%'";
}

$user_id = $_SESSION['user_id'];
$bookmarkedHadees = [];
$bmQuery = "SELECT hadees_id FROM bookmarks WHERE user_id = $user_id AND is_deleted = 0";
$bmResult = $conn->query($bmQuery);
if ($bmResult) {
    while ($bmRow = $bmResult->fetch_assoc()) {
        $bookmarkedHadees[] = $bmRow['hadees_id'];
    }
}

// Fetch distinct book names
$booksQuery = "SELECT DISTINCT hadees_book_name FROM hadees ORDER BY hadees_book_name";
$booksResult = $conn->query($booksQuery);

$sql = "SELECT * FROM hadees $searchSql ORDER BY hadees_date_added $sortOrder";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Browse Hadees</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Amiri&display=swap');

    html, body {
      background: linear-gradient(135deg, #e0f2f1, #a5d6a7);
      font-family: 'Segoe UI', sans-serif;
      overflow-x: hidden !important;
      width: 100%;
      max-width: 100%;
      height: 100vh; /* Ensure full height */
    }

    .container {
      max-width: 800px; /* Center content with a max width */
      margin: auto; /* Center the container */
      padding: 20px; /* Add padding */
    }

    .page-title {
      color: #255F38;
      font-weight: 700;
      font-family: 'Amiri', serif;
      margin-bottom: 25px;
      text-align: center;
      animation: fadeInDown 1s ease;
      font-size: 2rem;
      border-bottom: 2px solid #cfcfcf;
      padding-bottom: 10px;
    }

    .card {
      border-left: 4px solid #255F38;
      border-radius: 10px;
      background: linear-gradient(135deg, #e8f5e9, #ffffff);
      box-shadow: 0 4px 12px rgba(0,0,0,0.07);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      animation: fadeInUp 0.7s ease;
      margin: 0 auto 1.5rem auto; /* auto for horizontal centering */
      max-width: 700px;
    }

    .card:hover {
      transform: scale(1.015);
      box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }

    .card-text, .card-title {
      color: #255F38;
      font-weight: bold;
      position: relative;
      word-wrap: break-word;
      word-break: break-word;
      overflow-wrap: break-word;
    }

    .bookmark-btn {
      position: absolute;
      right: 0;
      top: 0;
      border: 1px solid #255F38;
      color: #255F38;
      background: transparent;
      padding: 0.25rem 0.5rem;
      border-radius: 5px;
      cursor: pointer;
      font-size: 1.1rem;
    }

    .bookmark-btn:hover {
      background-color: #255F38;
      color: white;
    }

    .bookmark-btn .bookmarked {
      color: green !important;
    }

    .hadees-meta {
      font-size: 0.85rem;
      color: #6c757d;
      margin-top: 10px;
    }

    .search-bar .form-control {
      border-color: #255F38;
      box-shadow: none;
      margin: 0 auto;
      width: 60%;
    }

    .search-bar .btn {
      background-color: #255F38;
      border-color: #255F38;
      color: #fff;
    }

    .search-bar .btn:hover {
      background-color: #1d442b;
      border-color: #1d442b;
    }

    .alert-info {
      background-color: #f1fef6;
      color: #1c402a;
      border: 1px solid #c2ebd5;
    }

    .book-tabs {
      overflow-x: auto;
      white-space: nowrap;
      margin: 20px auto;
      display: flex;
      gap: 10px;
padding: 0 10px;
      max-width: 80%;
     
      justify-content: flex-start;
    }

    .book-tabs button {
      flex: none;
      border: 1px solid #255F38;
      background: white;
      color: #255F38;
      padding: 6px 12px;
      border-radius: 20px;
      cursor: pointer;
      transition: 0.3s;
    }

    .book-tabs button.active,
    .book-tabs button:hover {
      background: #255F38;
      color: white;
    }

    @keyframes fadeInUp {
      from {opacity: 0; transform: translateY(20px);}
      to {opacity: 1; transform: translateY(0);}
    }

    @keyframes fadeInDown {
      from {opacity: 0; transform: translateY(-20px);}
      to {opacity: 1; transform: translateY(0);}
    }

    footer {
      width: 100%; /* Ensure footer spans full width */
      position: absolute; /* Position footer at the bottom */
      bottom: 0;
      left: 0;
      background-color: #f8f9fa; /* Light background for footer */
      padding: 10px 0; /* Padding for footer */
      text-align: center; /* Center text in footer */
    }
  </style>
</head>
<body>
<?php include("includes/header.php"); ?>
<div class="container px-0">

  <div class="row justify-content-center">
    <div class="col-md-12 mx-auto">
      <h3 class="page-title"><i class="bi bi-book me-2"></i> Browse All Hadees</h3>

      <!-- Search and Sort Form -->
      <form class="row mb-4 g-2 align-items-center search-bar" method="GET">
        <div class="col-md-7 d-flex">
          <input type="text" name="search" class="form-control me-2" placeholder="Search by text or book..." value="<?php echo htmlspecialchars($search); ?>">
          <button type="submit" class="btn"><i class="bi bi-search me-1"></i> Search</button>
        </div>
        <div class="col-md-3">
          <select name="sort" class="form-select" id="sort-select">
            <option value="newest" <?php echo $sortOrder == 'DESC' ? 'selected' : ''; ?>>Newest First</option>
            <option value="oldest" <?php echo $sortOrder == 'ASC' ? 'selected' : ''; ?>>Oldest First</option>
          </select>
        </div>
      </form>

      <!-- Book Filter Tabs -->
      <div class="book-tabs">
        <button class="filter-tab active" data-book="all">All</button>
        <?php while ($bookRow = $booksResult->fetch_assoc()): ?>
          <button class="filter-tab" data-book="<?php echo htmlspecialchars($bookRow['hadees_book_name']); ?>">
            <?php echo htmlspecialchars($bookRow['hadees_book_name']); ?>
          </button>
        <?php endwhile; ?>
      </div>

      <!-- Hadees Container -->
      <div id="hadees-list">
        <?php if ($result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): 
            $isBookmarked = in_array($row['hadees_id'], $bookmarkedHadees);
          ?>
            <div class="card mb-4">
              <div class="card-body">
                <h5 class="card-title">
                  <?php echo htmlspecialchars($row['hadees_book_name']); ?>

                  <a href="#" 
                     class="bookmark-btn" 
                     data-hadees-id="<?php echo $row['hadees_id']; ?>" 
                     title="<?php echo $isBookmarked ? 'Remove Bookmark' : 'Bookmark this Hadees'; ?>">
                    <i class="bi <?php echo $isBookmarked ? 'bi-bookmark-fill bookmarked' : 'bi-bookmark'; ?>"></i>
                  </a>
                </h5>

                <p class="card-text"><?php echo nl2br(htmlspecialchars($row['hadees_text'])); ?></p>
                <div class="hadees-meta">
                  <i class="bi bi-calendar-check"></i>
                  <?php echo date("F d, Y", strtotime($row['hadees_date_added'])); ?>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <div class="alert alert-info text-center">No Hadees found.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>

<script>
$(document).ready(function() {
  // Toggle bookmark AJAX
  $(document).on('click', '.bookmark-btn', function(e) {
    e.preventDefault();
    var $icon = $(this).find('i');
    var hadeesId = $(this).data('hadees-id');
    $.ajax({
      url: 'toggle_bookmark.php',
      type: 'POST',
      data: { hadees_id: hadeesId },
      success: function(response) {
        var data = (typeof response === 'object') ? response : JSON.parse(response);
        if (data.status === 'bookmarked') {
          $icon.removeClass('bi-bookmark').addClass('bi-bookmark-fill bookmarked');
        } else if (data.status === 'unbookmarked') {
          $icon.removeClass('bi-bookmark-fill bookmarked').addClass('bi-bookmark');
        }
      }
    });
  });

  // Filter tabs AJAX
  $('.filter-tab').click(function() {
    $('.filter-tab').removeClass('active');
    $(this).addClass('active');
    var book = $(this).data('book');
    var search = $('input[name="search"]').val();
    var sort = $('#sort-select').val();

    $.ajax({
      url: 'filter_hadees.php',
      type: 'GET',
      data: { book: book, search: search, sort: sort },
      success: function(data) {
        $('#hadees-list').html(data);
      }
    });
  });

  // Sort change AJAX
  $('#sort-select').change(function() {
    var sort = $(this).val();
    var book = $('.filter-tab.active').data('book');
    var search = $('input[name="search"]').val();

    $.ajax({
      url: 'filter_hadees.php',
      type: 'GET',
      data: { book: book, search: search, sort: sort },
      success: function(data) {
        $('#hadees-list').html(data);
      }
    });
  });
});
</script>

<?php include("includes/footer.php"); ?>

</body>
</html>
