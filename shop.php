<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 0;
}
$user_id = $_SESSION['user_id'];

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Pagination settings
$items_per_page = 9;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// Add to Cart Handler
if (isset($_POST['add_to_cart']) && isset($_POST['csrf_token']) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    if ($user_id === 0) {
        header('Location: login.php');
        exit();
    }

    $book_id = filter_input(INPUT_POST, 'book_id', FILTER_VALIDATE_INT);
    $book_name = filter_input(INPUT_POST, 'book_name', FILTER_SANITIZE_STRING);
    $book_price = filter_input(INPUT_POST, 'book_price', FILTER_VALIDATE_FLOAT);
    $book_image = filter_input(INPUT_POST, 'book_image', FILTER_SANITIZE_STRING);
    if ($book_id && $book_name && $book_price && $book_image) {
        $check_stmt = $conn->prepare("SELECT * FROM `cart` WHERE book_id = ? AND user_id = ?");
        $check_stmt->bind_param("ii", $book_id, $user_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $update_stmt = $conn->prepare("UPDATE `cart` SET quantity = quantity + 1, total = quantity * ? WHERE book_id = ? AND user_id = ?");
            $update_stmt->bind_param("dii", $book_price, $book_id, $user_id);
            $update_stmt->execute();
        } else {
            $insert_stmt = $conn->prepare("INSERT INTO `cart` (user_id, book_id, name, price, image, quantity, total) VALUES (?, ?, ?, ?, ?, 1, ?)");
            $insert_stmt->bind_param("iisssd", $user_id, $book_id, $book_name, $book_price, $book_image, $book_price);
            $insert_stmt->execute();
        }
    }
}

$mood = $_GET['mood'] ?? '';
$sort = $_GET['sort'] ?? 'name_asc';

// Modified query to include pagination
$query = "SELECT * FROM book_info WHERE 1=1";
$count_query = "SELECT COUNT(*) as total FROM book_info WHERE 1=1";
$params = [];
$types = '';

if (!empty($mood)) {
    $moods = is_array($mood) ? $mood : explode(',', $mood);
    $placeholders = implode(',', array_fill(0, count($moods), '?'));
    $query .= " AND mood IN ($placeholders)";
    $count_query .= " AND mood IN ($placeholders)";
    $types = str_repeat('s', count($moods));
    $params = $moods;
}

switch ($sort) {
    case 'price_asc':
        $query .= " ORDER BY price ASC";
        break;
    case 'price_desc':
        $query .= " ORDER BY price DESC";
        break;
    case 'name_desc':
        $query .= " ORDER BY name DESC";
        break;
    default:
        $query .= " ORDER BY name ASC";
}

// Add LIMIT and OFFSET to the main query
$query .= " LIMIT ? OFFSET ?";
$types .= "ii";
$params[] = $items_per_page;
$params[] = $offset;

// Get total count for pagination
$count_stmt = $conn->prepare($count_query);
if (!empty($types) && count($params) > 2) {
    $count_stmt->bind_param(substr($types, 0, -2), ...array_slice($params, 0, -2));
}
$count_stmt->execute();
$total_result = $count_stmt->get_result()->fetch_assoc();
$total_items = $total_result['total'];
$total_pages = ceil($total_items / $items_per_page);

// Get books for current page
$stmt = $conn->prepare($query);
if (!empty($types)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$books = $result->fetch_all(MYSQLI_ASSOC);

// Get unique moods
$moodQuery = "SELECT DISTINCT mood FROM book_info ORDER BY mood";
$moodResult = $conn->query($moodQuery);
$availableMoods = $moodResult->fetch_all(MYSQLI_ASSOC);

// Add query for featured books
$featured_query = "SELECT * FROM book_info ORDER BY RAND() LIMIT 5";
$featured_result = $conn->query($featured_query);
$featured_books = $featured_result->fetch_all(MYSQLI_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VibeReads Book Store</title>
    <link rel="stylesheet" href="css/hello.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
         body {
            background-color: #f4f6f9;
            font-family: 'Arial', sans-serif;
        }
        .navbar {
            background-color: #333;
        }
        .navbar-brand {
            color: #fff;
            font-weight: bold;
        }
        .filter-sidebar {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            height: 100%;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }
        .card-img-top {
            border-radius: 12px 12px 0 0;
            object-fit: cover;
            height: 250px;
        }
        .description {
            font-size: 0.9rem;
            color: #666;
            margin-top: 10px;
        }
        .author {
            color: #555;
            font-style: italic;
        }
        .sort-select {
            width: auto;
            min-width: 200px;
        }
        .filter-title {
            color: #333;
            font-size: 1.2rem;
            margin-bottom: 15px;
        }
        .mood-checkbox {
            margin-bottom: 10px;
        }

        .card {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .modal-custom {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            max-width: 90%;
            width: 400px;
            text-align: center;
            animation: modalPop 0.3s ease-out;
        }
        
        .modal-backdrop-custom {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            backdrop-filter: blur(5px);
        }
        
        .modal-icon {
            font-size: 3em;
            color: #ffd700;
            margin-bottom: 20px;
        }
        
        .modal-title {
            color: #333;
            font-size: 1.5em;
            margin-bottom: 15px;
        }
        
        .modal-message {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        
        .modal-button {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .modal-button:hover {
            background: #45a049;
            transform: scale(1.05);
        }
        
        @keyframes modalPop {
            0% {
                transform: translate(-50%, -60%);
                opacity: 0;
            }
            100% {
                transform: translate(-50%, -50%);
                opacity: 1;
            }
        }
        
        .sparkle {
            animation: sparkleAnim 1.5s infinite;
            display: inline-block;
        }
        
        @keyframes sparkleAnim {
            0% { transform: rotate(0deg) scale(1); }
            50% { transform: rotate(180deg) scale(1.2); }
            100% { transform: rotate(360deg) scale(1); }
        }
        
        /* Add pagination styles */
        .pagination {
            margin-top: 2rem;
            justify-content: center;
        }
        
        .pagination .page-item.active .page-link {
            background-color: #4CAF50;
            border-color: #4CAF50;
        }
        
        .pagination .page-link {
            color: #4CAF50;
        }
        
        .pagination .page-link:hover {
            color: #fff;
            background-color: #45a049;
            border-color: #45a049;
        }

        .carousel {
            margin-bottom: 2rem;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .carousel-item {
            height: 400px;
            background: #000;
        }

        .carousel-item img {
            object-fit: cover;
            filter: brightness(0.6);
            height: 100%;
            width: 100%;
        }

        .carousel-caption {
            background: rgba(0,0,0,0.5);
            border-radius: 10px;
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }

        .carousel-price {
            font-size: 1.5rem;
            color: #4CAF50;
            font-weight: bold;
        }

        .carousel-button {
            background: #4CAF50;
            border: none;
            padding: 10px 25px;
            border-radius: 25px;
            transition: all 0.3s;
        }

        .carousel-button:hover {
            background: #45a049;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
<?php include 'index_header.php'; ?>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="#">VibeReads Bookstore</a>
    </div>
</nav>

<div class="container mt-4">
    <!-- Carousel Section -->
    <div id="featuredBooks" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <?php for($i = 0; $i < count($featured_books); $i++): ?>
                <button type="button" data-bs-target="#featuredBooks" 
                        data-bs-slide-to="<?php echo $i; ?>" 
                        <?php echo $i === 0 ? 'class="active"' : ''; ?>
                        aria-label="Slide <?php echo $i + 1; ?>"></button>
            <?php endfor; ?>
        </div>
        <div class="carousel-inner">
            <?php foreach($featured_books as $index => $book): ?>
                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                    <img src="added_books/<?php echo htmlspecialchars($book['image']); ?>" 
                         class="d-block w-100" alt="<?php echo htmlspecialchars($book['name']); ?>">
                    <div class="carousel-caption">
                        <h3><?php echo htmlspecialchars($book['name']); ?></h3>
                        <p><?php echo htmlspecialchars(substr($book['description'], 0, 150)) . '...'; ?></p>
                        <p class="carousel-price">RS <?php echo number_format($book['price'], 2); ?></p>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="book_id" value="<?php echo $book['bid']; ?>">
                            <input type="hidden" name="book_name" value="<?php echo htmlspecialchars($book['name']); ?>">
                            <input type="hidden" name="book_price" value="<?php echo $book['price']; ?>">
                            <input type="hidden" name="book_image" value="<?php echo htmlspecialchars($book['image']); ?>">
                            <button type="submit" name="add_to_cart" class="btn btn-light carousel-button">
                                Add to Cart
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#featuredBooks" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#featuredBooks" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <div class="container mt-4">
        <div class="row">
            <!-- Filter Sidebar -->
            <div class="col-md-3">
                <div class="filter-sidebar">
                    <h3 class="filter-title">Filters</h3>
                    <form id="filterForm" method="GET">
                        <h5>Mood</h5>
                        <?php
                        foreach ($availableMoods as $moodOption) {
                            $checked = !empty($mood) && in_array($moodOption['mood'], is_array($mood) ? $mood : explode(',', $mood)) ? 'checked' : '';
                            echo "<div class='mood-checkbox'>
                                    <input class='form-check-input' type='checkbox' name='mood[]' 
                                        value='" . htmlspecialchars($moodOption['mood']) . "' $checked>
                                    <label class='form-check-label ms-2'>" . 
                                        ucfirst(htmlspecialchars($moodOption['mood'])) . 
                                    "</label>
                                  </div>";
                        }
                        ?>
                        <button type="submit" class="btn btn-primary w-100 mt-3">Apply Filters</button>
                    </form>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Our Book Collection</h2>
                    <select class="form-select sort-select" name="sort" onchange="window.location.href=this.value">
                        <?php
                        $baseUrl = '?';
                        if (!empty($mood)) {
                            $baseUrl .= http_build_query(['mood' => is_array($mood) ? $mood : explode(',', $mood)]) . '&';
                        }
                        ?>
                        <option value="<?php echo $baseUrl; ?>sort=name_asc" 
                            <?php echo $sort === 'name_asc' ? 'selected' : ''; ?>>Name (A-Z)</option>
                        <option value="<?php echo $baseUrl; ?>sort=name_desc" 
                            <?php echo $sort === 'name_desc' ? 'selected' : ''; ?>>Name (Z-A)</option>
                        <option value="<?php echo $baseUrl; ?>sort=price_asc" 
                            <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="<?php echo $baseUrl; ?>sort=price_desc" 
                            <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                    </select>
                </div>

                <div class="row row-cols-1 row-cols-md-3 g-4">
                    <?php
                    if (!empty($books)) {
                        foreach ($books as $book) {
                            echo "<div class='col'>
                                    <div class='card' onclick='showBookMessage(\"" . htmlspecialchars($book['name']) . "\")'>
                                        <img src='added_books/" . htmlspecialchars($book['image']) . "' 
                                            class='card-img-top' alt='" . htmlspecialchars($book['name']) . "'>
                                        <div class='card-body'>
                                            <h5 class='card-title'>" . htmlspecialchars($book['name']) . "</h5>
                                            <p class='author'>" . htmlspecialchars($book['title']) . "</p>
                                            <p class='description'>" . htmlspecialchars($book['description']) . "</p>
                                            <div class='d-flex justify-content-between align-items-center'>
                                                <span class='text-muted'>RS " . number_format($book['price'], 2) . "</span>
                                                <form method='POST' class='d-inline'>
                                                    <input type='hidden' name='csrf_token' value='" . $_SESSION['csrf_token'] . "'>
                                                    <input type='hidden' name='book_id' value='" . $book['bid'] . "'>
                                                    <input type='hidden' name='book_name' value='" . htmlspecialchars($book['name']) . "'>
                                                    <input type='hidden' name='book_price' value='" . $book['price'] . "'>
                                                    <input type='hidden' name='book_image' value='" . htmlspecialchars($book['image']) . "'>
                                                    <button type='submit' name='add_to_cart' class='btn btn-sm btn-outline-primary'>
                                                        <i class='fas fa-cart-plus'></i> Add to Cart
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>";
                        }
                    }
                    ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination">
                        <?php
                        // Previous page link
                        $prevPage = $page - 1;
                        $prevUrl = $baseUrl . "sort=$sort&page=$prevPage";
                        if ($page > 1) {
                            echo "<li class='page-item'><a class='page-link' href='$prevUrl'>&laquo;</a></li>";
                        }

                        // Page numbers
                        for ($i = 1; $i <= $total_pages; $i++) {
                            $pageUrl = $baseUrl . "sort=$sort&page=$i";
                            $active = $i == $page ? 'active' : '';
                            echo "<li class='page-item $active'><a class='page-link' href='$pageUrl'>$i</a></li>";
                        }

                        // Next page link
                        $nextPage = $page + 1;
                        $nextUrl = $baseUrl . "sort=$sort&page=$nextPage";
                        if ($page < $total_pages) {
                            echo "<li class='page-item'><a class='page-link' href='$nextUrl'>&raquo;</a></li>";
                        }
                        ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal and JavaScript remain the same -->
    <div class="modal-backdrop-custom" id="modalBackdrop" onclick="hideModal()"></div>
    <div class="modal-custom" id="bookModal">
        <div class="modal-icon">
            <span class="sparkle">✨</span> 📚 <span class="sparkle">✨</span>
        </div>
        <h3 class="modal-title">Adventure Awaits!</h3>
        <p class="modal-message">
            Ready to embark on a journey with <span id="bookTitle" style="font-weight: bold; color: #4CAF50"></span>?
            Head back to the homepage, where your literary treasure hunt begins!
            Search for this gem and make it yours! 🌟
        </p>
        <button class="modal-button" onclick="hideModal()">Got it, thanks!</button>
    </div>

    <script>
        function showBookMessage(bookName) {
            document.getElementById('bookTitle').textContent = bookName;
            document.getElementById('modalBackdrop').style.display = 'block';
            document.getElementById('bookModal').style.display = 'block';
            document.body.style.overflow = 'hidden'; 
        }

        function hideModal() {
            document.getElementById('modalBackdrop').style.display = 'none';
            document.getElementById('bookModal').style.display = 'none';
            document.body.style.overflow = 'auto'; 
        }
    </script>
</body>
</html>