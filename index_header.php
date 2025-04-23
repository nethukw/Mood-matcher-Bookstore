<?php

include_once 'config.php';

// Cart count function
function getCartItemCount($conn, $user_id) {
    if (!isset($user_id)) return 0;
    
    $query = "SELECT SUM(quantity) as total_items FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['total_items'] ?? 0;
}

// Get the cart count
$cartCount = 0;
if (isset($_SESSION['user_id'])) {
    $cartCount = getCartItemCount($conn, $_SESSION['user_id']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VibeReads - Header</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/hello.css">
    <style>
        #mobile-menu {
    position: fixed;
    top: 70px; 
    left: 0;
    width: 100%;
    background-color: #fff;
    padding: 20px;
    border-bottom: 1px solid #ddd;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

#mobile-menu .nav-link {
    display: block;
    padding: 10px;
    border-bottom: 1px solid #ddd;
}

#mobile-menu .nav-link:hover {
    background-color: #f8f9fa;
}
        .nav-link:hover {
            color: #0d6efd !important;
            transform: scale(1.05);
            transition: all 0.3s ease;
        }

        .logo-hover:hover {
            transform: scale(1.05);
            transition: transform 0.3s ease;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
            color: #0d6efd;
            transition: all 0.3s ease;
        }

        .cart-icon:hover,
        .user-icon:hover {
            color: #0d6efd !important;
            transform: scale(1.1);
            transition: all 0.3s ease;
        }

        .mobile-nav-link:hover {
            background-color: #f8f9fa;
            color: #0d6efd !important;
            transition: all 0.3s ease;
        }

        
        .sub-menu-wrap {
            position: fixed;
            top: 9%;
            right: -1%;
            width: 320px;
            max-height: 0px;
            overflow: hidden;
            transition: max-height 0.5s;
            z-index: 100;
        }

        .sub-menu-wrap.open-menu {
            max-height: 400px;
        }

        .sub-menu {
            background: #fff;
            padding: 20px;
            margin: 10px;
            border-bottom-right-radius: 16px;
            border-bottom-left-radius: 16px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .user-info h3 {
            font-weight: 500;
            margin-bottom: 5px;
        }

        .user-info img {
            width: 60px;
            border-radius: 50%;
            margin-right: 15px;
        }

        .sub-menu hr {
            border: 0;
            height: 1px;
            width: 100%;
            background: #ccc;
            margin: 15px 0;
        }

        .sub-menu-link {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #525252;
            margin: 12px 0;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .sub-menu-link:hover {
            background-color: #f8f9fa;
        }

        .sub-menu-link p {
            margin: 0;
            flex-grow: 1;
        }

        .sub-menu-link span {
            font-size: 22px;
            transition: transform 0.5s;
        }

        .sub-menu-link:hover span {
            transform: translateX(5px);
        }

        .sub-menu-link:hover p {
            font-weight: 600;
        }

        .link_btn {
            background-color: brown;
            padding: 6px;
            border-radius: 10px;
            margin-left: 10px;
            color: white;
            font-weight: 500;
        }
    </style>
</head>
<body>

<!-- Header -->
<header class="fixed-top bg-white shadow-sm" style="height: 70px;">

    <div class="container-fluid px-4 py-3">
        <div class="row align-items-center">
            <!-- Logo (Col 1-3) -->
            <div class="col-6 col-md-3 d-flex align-items-center justify-content-start">
                <a href="index.php" class="text-decoration-none d-flex align-items-center logo-hover">
                    <img src="img/logo.jpeg" alt="Book Mood Matcher Logo" class="img-fluid me-2" style="height: 3rem; max-height: 5rem;">
                    <span class="fs-4 fw-bold text-primary">Vibe<span class="text-danger">Reads</span></span>
                </a>
            </div>

            <!-- Navigation (Col 4-8) -->
            <nav class="col-6 d-none d-md-flex justify-content-center">
                <div class="nav nav-pills">
                    <a href="index.php" class="nav-link text-secondary">Home</a>
                    <a href="mood_matcher.php" class="nav-link text-secondary">Mood Matcher</a>
                    <a href="shop.php" class="nav-link text-secondary">Shop</a>
                    <!--<a href="ebooks.php" class="nav-link text-secondary">E-Books</a>-->
                    <a href="orders.php" class="nav-link text-secondary">Orders</a>
                    <a href="about-us.php" class="nav-link text-secondary">About Us</a>
                </div>
            </nav>

            <!-- Mobile Menu Toggle (Col 9-12) -->
            <div class="col-6 col-md-3 d-flex align-items-center justify-content-end">
                <!-- Mobile Menu Toggle Button -->
                <button id="mobile-menu-toggle" class="btn btn-secondary d-md-none me-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <!-- Search Bar -->
                <div class="d-none d-md-flex me-2">
                    <form action="search_books.php" method="GET" class="position-relative">
                        <input 
                            type="text" 
                            name="query" 
                            placeholder="Search books..." 
                            class="form-control rounded-pill"
                            style="width: 12rem;"
                        >
                        <button type="submit" class="btn position-absolute top-50 end-0 translate-middle-y border-0 bg-transparent">
                            <img src="img/searchIcon.png" alt="Search" style="height: 1.25rem; width: 1.25rem;">
                        </button>
                    </form>
                </div>

                <!-- Categories Dropdown -->
                <div class="dropdown d-none d-md-block me-2">
                    <button id="category-btn" class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Categories
                    </button>
                    <ul id="category-dropdown" class="dropdown-menu">
                        <li><a class="dropdown-item" href="index.php#New">New Arrived</a></li>
                        <li><a class="dropdown-item" href="index.php#Adventure">Adventure</a></li>
                        <li><a class="dropdown-item" href="index.php#Magical">Magic</a></li>
                        <li><a class="dropdown-item" href="index.php#Knowledge">Knowledge</a></li>
                    </ul>
                </div>

                <!-- User Account & Cart -->
                <div class="d-flex align-items-center">
                    <!-- Shopping Cart -->
                    <a href="cart.php" class="position-relative me-2 text-decoration-none text-secondary cart-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63 ```html
                            .184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.75rem;">
                            <?php echo $cartCount; ?>
                        </span>
                    </a>

                    <!-- User Icon Dropdown -->
                    <div class="dropdown">
                        <button onclick="toggleMenu()" id="user-dropdown-btn" class="btn btn-outline-secondary border-0 dropdown-toggle user-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </button>
                        
                        <!-- New Submenu -->
                        <div class="sub-menu-wrap" id="subMenu">
                            <div class="sub-menu">
                                <?php if(isset($_SESSION['user_name'])): ?>
                                    <div class="user-info">
                                        <img src="images/ds2.png" />
                                        <div class="user-info" style="display: block;">
                                            <h3>Hello, <?php echo $_SESSION['user_name']?></h3>
                                            <h6><?php echo $_SESSION['user_email']?></h6>
                                        </div>
                                    </div>
                                    <hr />
                                    <a href="cart.php" class="sub-menu-link">
                                        <p>Cart</p>
                                        <span>></span>
                                    </a>
                                    <a href="contact-us.php" class="sub-menu-link">
                                        <p>Contact Us</p>
                                        <span>></span>
                                    </a>
                                    <a href="orders.php" class="sub-menu-link">
                                        <p>Order history</p>
                                        <span>></span>
                                    </a>
                                    <a href="logout.php" class="sub-menu-link">
                                        <p style="background-color: red; border-radius:8px; text-align:center; color:white; font-weight:600; margin-top:5px; padding:5px;">Logout</p>
                                    </a>
                                <?php else: ?>
                                    <div class="sub-menu">
                                        <a href="login.php" class="sub-menu-link">
                                            <p>Login</p>
                                            <span>></span>
                                        </a>
                                        <a href="register.php" class="sub-menu-link">
                                            <p>Register</p>
                                            <span>></span>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="d-md-none d-none">
        <div class="nav nav-pills flex-column">
            <a href="index.php" class="nav-link text-secondary">Home</a>
            <a href="mood_matcher.php" class="nav-link text-secondary">Mood Matcher</a>
            <a href="shop.php" class="nav-link text-secondary">Shop</a>
           <!-- <a href="ebooks.php" class="nav-link text-secondary">E-Books</a>-->
            <a href="orders.php" class="nav-link text-secondary">Orders</a>
            <a href="about-us.php" class="nav-link text-secondary">About Us</a>
        </div>
    </div>
</header>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script>
    let subMenu = document.getElementById("subMenu");
    
    function toggleMenu() {
        subMenu.classList.toggle("open-menu");
    }

    document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');

    // Toggle mobile menu
    mobileMenuToggle.addEventListener('click', function() {
        mobileMenu.classList.toggle('d-none');
    });

    // Close submenu when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('#user-dropdown-btn') && !event.target.closest('#subMenu')) {
            subMenu.classList.remove('open-menu');
        }
    });
});
</script>
</body>
</html>