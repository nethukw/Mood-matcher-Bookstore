<?php
session_start();
// Centralized configuration
require_once 'config.php';

// Ensure user session
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 0; // Guest user
}
$user_id = $_SESSION['user_id'];

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Filter and Sort Handling
$mood = $_GET['mood'] ?? '';
$sort = $_GET['sort'] ?? 'name_asc';

// Book Query
$query = "SELECT * FROM book_info WHERE 1=1";
$params = [];
$types = '';

// Mood Filtering
if (!empty($mood)) {
    $moods = is_array($mood) ? $mood : explode(',', $mood);
    $placeholders = implode(',', array_fill(0, count($moods), '?'));
    $query .= " AND mood IN ($placeholders)";
    $types = str_repeat('s', count($moods));
    $params = $moods;
}

// Sorting
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

// Prepare and execute query
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VibeReads eBook Store</title>
    <link rel="stylesheet" href="css/hello.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        <style>
    :root {
        --primary-color: #6366f1;
        --secondary-color: #8b5cf6;
        --background-color: #f0f4f8;
        --card-shadow: 0 10px 25px rgba(0,0,0,0.08);
    }

    body {
        background-color: var(--background-color);
        font-family: 'Inter', sans-serif;
    }

    .filter-sidebar {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: var(--card-shadow);
    }

    .book-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: var(--card-shadow);
    }

    .book-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }

    .book-card .card-img-top {
        height: 250px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .book-card:hover .card-img-top {
        transform: scale(1.05);
    }

    .card-body {
        padding: 15px;
        background: white;
    }

    .card-title {
        font-weight: 600;
        color: #1a202c;
        margin-bottom: 8px;
    }

    .text-muted {
        color: #718096 !important;
    }

    .add-to-cart-btn {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 25px;
        transition: all 0.3s ease;
    }

    .add-to-cart-btn:hover {
        background: var(--secondary-color);
        transform: translateY(-3px);
    }

    .sort-select {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
    }

    .mood-checkbox {
        margin-bottom: 10px;
        display: flex;
        align-items: center;
    }

    .mood-checkbox input {
        margin-right: 10px;
    }

    .filter-title {
        color: #2d3748;
        font-weight: 600;
        margin-bottom: 15px;
    }

    @media (max-width: 768px) {
        .book-card {
            margin-bottom: 20px;
        }
    }
</style>
    </style>
</head>
<body>
    <?php include 'index_header.php'; ?>

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
                    <h2>eBook Collection</h2>
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
                                    <div class='card book-card'>
                                        <img src='added_books/" . htmlspecialchars($book['image']) . "' 
                                            class='card-img-top book-image' alt='" . htmlspecialchars($book['name']) . "'>
                                        <div class='card-body'>
                                            <h5 class='card-title'>" . htmlspecialchars($book['name']) . "</h5>
                                            <p class='text-muted'>" . htmlspecialchars($book['title']) . "</p>
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
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const addToCartForms = document.querySelectorAll('form[name="add_to_cart"]');
        
        addToCartForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Check if user is logged in
                <?php if ($user_id === 0): ?>
                    window.location.href = 'login.php';
                    return;
                <?php endif; ?>

                // Proceed with add to cart logic
                const formData = new FormData(form);
                
                fetch('add_to_cart.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Book added to cart successfully!');
                    } else {
                        alert('Failed to add book to cart: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            });
        });
    });
    </script>
</body>
</html>