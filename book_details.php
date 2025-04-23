<?php
require_once 'classes\Database.php';
require_once 'classes\Book.php';
require_once 'classes\Cart.php';

error_reporting(0);
session_start();

$db = new Database();
$book = new Book($db);
$cart = new Cart($db);
$user_id = $_SESSION['user_id'] ?? null;

if (isset($_POST['add_to_cart'])) {
    if (!isset($user_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Please Login to get your books']);
        exit;
    }
    
    $book_name = $_POST['book_name'];
    $book_id = $_POST['book_id'];
    $book_image = $_POST['book_image'];
    $book_price = $_POST['book_price'];
    $book_quantity = $_POST['quantity'];
    $total_price = number_format($book_price * $book_quantity);
    
    $select_book = $cart->checkBookInCart($book_name, $user_id);
    
    if (mysqli_num_rows($select_book) > 0) {
        echo json_encode(['status' => 'error', 'message' => 'This book is already in your cart']);
        exit;
    }
    
    if ($cart->addToCart($book_id, $user_id, $book_name, $book_price, $book_image, $book_quantity, $total_price)) {
        echo json_encode(['status' => 'success', 'message' => 'Book added to cart successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add book to cart']);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VibeReads-Book Details</title>
    <link rel="stylesheet" href="./css/index_book.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f5f5f5;
        }

        .details {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px;
        }

        .row_box {
            display: flex;
            gap: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .col_box img {
            width: 250px;
            height: auto;
            border-radius: 10px;
        }

        .col_box {
            flex: 1;
        }

        h1, h4, h3 {
            margin: 10px 0;
        }

        .buttons {
            display: flex;
            gap: 10px;
            margin: 15px 0;
        }

        .btn {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s ease;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .message {
            position: fixed;
            top: 10px;
            right: 10px;
            background-color: #fff;
            padding: 10px 20px;
            border: 2px solid #007bff;
            border-radius: 5px;
            display: none;
            z-index: 1000;
        }

        .message.success {
            border-color: green;
        }

        .message.error {
            border-color: red;
        }
    </style>
</head>

<body>
<div class="details">
        <?php
        if (isset($_GET['details'])) {
            $get_id = $_GET['details'];
            $get_book = $book->getBookById($get_id);
            
            if ($get_book && mysqli_num_rows($get_book) > 0) {
                while ($fetch_book = mysqli_fetch_assoc($get_book)) {
        ?>
                    <div class="row_box">
                        <div class="col_box">
                            <img src="./added_books/<?php echo htmlspecialchars($fetch_book['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($fetch_book['name']); ?>">
                        </div>
                        <div class="col_box">
                            <h4>Author: <?php echo htmlspecialchars($fetch_book['title']); ?></h4>
                            <h1>Name: <?php echo htmlspecialchars($fetch_book['name']); ?></h1>
                            <h3>Price: RS<?php echo htmlspecialchars($fetch_book['price']); ?>/-</h3>
                            
                            <label for="quantity">Quantity:</label>
                            <input type="number" id="quantity" value="1" min="1" max="10">
                            <div class="buttons">
                                <button class="btn add-to-cart" 
                                        data-id="<?php echo htmlspecialchars($fetch_book['bid']); ?>" 
                                        data-name="<?php echo htmlspecialchars($fetch_book['name']); ?>" 
                                        data-price="<?php echo htmlspecialchars($fetch_book['price']); ?>" 
                                        data-image="<?php echo htmlspecialchars($fetch_book['image']); ?>">
                                    Add to Cart
                                </button>
                            </div>
                            <h3>Book Details</h3>
                            <p><?php echo htmlspecialchars($fetch_book['description']); ?></p>
                        </div>
                    </div>
        <?php
                }
            } else {
                echo '<p>Book not found!</p>';
            }
        } else {
            echo '<p>No book details provided!</p>';
        }
        ?>
    </div>

    <div class="message"></div>

    <script>
        $(document).ready(function () {
            $(".add-to-cart").click(function () {
                const bookId = $(this).data("id");
                const bookName = $(this).data("name");
                const bookPrice = $(this).data("price");
                const bookImage = $(this).data("image");
                const quantity = $("#quantity").val();

                $.ajax({
                    url: "",
                    method: "POST",
                    data: {
                        add_to_cart: true,
                        book_id: bookId,
                        book_name: bookName,
                        book_price: bookPrice,
                        book_image: bookImage,
                        quantity: quantity
                    },
                    success: function (response) {
                        const data = JSON.parse(response);
                        $(".message")
                            .text(data.message)
                            .addClass(data.status)
                            .fadeIn();

                        setTimeout(() => {
                            $(".message").fadeOut();
                        }, 3000);
                    }
                });
            });
        });
    </script>
</body>

</html>
