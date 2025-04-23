<?php
include 'config.php';

// Turn off error reporting
error_reporting(0);
session_start();

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Check if the 'add_to_cart' button is clicked
if (isset($_POST['add_to_cart'])) {
    // Check if the user is logged in
    if (!isset($user_id)) {
        // If not, redirect to the login page
        header('Location: login.php');
        exit();
    } else {
        // Get the book details from the form
        $book_name = $_POST['book_name'];
        $book_id = $_POST['book_id'];
        $book_image = $_POST['book_image'];
        $book_price = $_POST['book_price'];
        $book_quantity = '1';

        $total_price = number_format($book_price * $book_quantity);

        $select_book = $conn->query("SELECT * FROM cart WHERE book_id= '$book_id' AND user_id='$user_id' ") or die('query failed');

        if (mysqli_num_rows($select_book) > 0) {
            $message[] = 'This Book is alredy in your cart';
        } else {
            $conn->query("INSERT INTO cart (`user_id`,`book_id`,`name`, `price`, `image`,`quantity` ,`total`) VALUES('$user_id','$book_id','$book_name','$book_price','$book_image','$book_quantity', '$total_price')") or die('Add to cart Query failed');
            $message[] = 'Book Added To Cart Successfully';
            header('location:index.php');
        }
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/hello.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link href="https://unpkg.com/tailwindcss@^1.0/dist/tailwind.min.css" rel="stylesheet" />
    <title>VibeReads-Home</title>

    <style>
        img {
            border: none;
        }
        .message {
  position: sticky;
  top: 0;
  margin: 0 auto;
  width: 61%;
  background-color: #fff;
  padding: 6px 9px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  z-index: 100;
  gap: 0px;
  border: 2px solid rgb(68, 203, 236);
  border-top-right-radius: 8px;
  border-bottom-left-radius: 8px;
}
.message span {
  font-size: 22px;
  color: rgb(240, 18, 18);
  font-weight: 400;
}
.message i {
  cursor: pointer;
  color: rgb(3, 227, 235);
  font-size: 15px;
}


@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes slideUp {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.fade-in {
  opacity: 0;
  animation: fadeIn 2s forwards;
}

.carousel-item img {
    object-fit: cover;
    height: 80vh; 
    filter: brightness(50%); 
    transition: transform 0.5s ease-in-out;
}

.carousel-item img:hover {
    transform: scale(1.05);
}

.carousel-caption {
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
    background-color: rgba(0, 0, 0, 0.6); 
    padding: 40px;
    text-align: center;
}

.caption-content {
    max-width: 80%; 
}

.carousel-caption h1 {
    font-size: 4rem; 
    letter-spacing: 3px;
    text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.8); 
    line-height: 1.2;
}

.carousel-caption p {
    font-size: 1.5rem; 
    font-weight: 400;
    margin-bottom: 20px;
    line-height: 1.6;
    letter-spacing: 1px;
}

.carousel-caption .btn {
    font-size: 1.5rem;
    padding: 15px 35px;
    border-radius: 50px;
    transition: all 0.3s ease;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
}

.carousel-caption .btn:hover {
    background-color: #ff5f00;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.4);
    transform: translateY(-5px); 
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
    background-color: rgba(255, 255, 255, 0.7);
    border-radius: 50%;
    padding: 12px;
    transition: all 0.3s ease;
}

.carousel-control-prev-icon:hover,
.carousel-control-next-icon:hover {
    background-color: rgba(255, 255, 255, 0.9);
    transform: scale(1.1); 
}





@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.btn-gradient-primary {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        border: none;
    }

    .btn-gradient-primary:hover {
        background: linear-gradient(135deg, #2575fc 0%, #6a11cb 100%);
    }
    </style>
</head>

<body>
    <?php include 'index_header.php' ?>
    <?php
    if (isset($message)) {
        foreach ($message as $message) {
            echo '
        <div class="message" id= "messages"><span>' . $message . '</span>
        </div>
        ';
        }
    }
    ?>

<!-- Hero Section -->
<div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="img/hero.jpg" class="d-block w-100" alt="Image 1">
      <div class="carousel-caption d-flex align-items-center justify-content-center">
        <div class="caption-content text-center">
          <h1 class="display-2 text-light fw-bold">Welcome to VibeReads</h1>
          <p class="lead text-light">Find the perfect book that matches your mood</p>
          <a href="mood_matcher.php" class="btn btn-lg btn-primary rounded-pill shadow-lg">Start Matching Your Mood</a>
        </div>
      </div>
    </div>
    <div class="carousel-item">
      <img src="img/hero2.png" class="d-block w-100" alt="Image 2">
      <div class="carousel-caption d-flex align-items-center justify-content-center">
        <div class="caption-content text-center">
          <h1 class="display-2 text-light fw-bold">Explore New Worlds</h1>
          <p class="lead text-light">Books to inspire and entertain you</p>
          <a href="mood_matcher.php" class="btn btn-lg btn-primary rounded-pill shadow-lg">Start Matching Your Mood</a>
        </div>
      </div>
    </div>
    <div class="carousel-item">
      <img src="img/hero3.jpg" class="d-block w-100" alt="Image 3">
      <div class="carousel-caption d-flex align-items-center justify-content-center">
        <div class="caption-content text-center">
          <h1 class="display-2 text-light fw-bold">Your Next Adventure Awaits</h1>
          <p class="lead text-light">Books to discover, characters to love</p>
          <a href="mood_matcher.php" class="btn btn-lg btn-primary rounded-pill shadow-lg">Start Matching Your Mood</a>
        </div>
      </div>
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>


<!-- New Arrived Section -->
    <section id="New">

        <div class="container px-5 mx-auto">
            <h2 class="m-8 font-extrabold text-4xl text-center border-t-2 " style="color: rgb(0, 167, 245);">
                New Arrived
            </h2>
        </div>
    </section>
    <!-- Show Products Section -->
    <section class="show-products">
        <div class="box-container">

            <?php
            // Query to get new arrived books
          $select_book = mysqli_query($conn, "SELECT * FROM `book_info` ORDER BY date DESC LIMIT 8") or die('Query failed: ' . mysqli_error($conn));
          // Check if there are any books  
          if (mysqli_num_rows($select_book) > 0) {
                while ($fetch_book = mysqli_fetch_assoc($select_book)) {
            ?>

                    <div class="box" style="width: 255px; height:355px;">
                        <a href="book_details.php.php?details=<?php echo $fetch_book['bid'];
                                                            echo '-name=', $fetch_book['name']; ?>"> <img style="height: 200px;width: 125px;margin: auto;" class="books_images" src="added_books/<?php echo $fetch_book['image']; ?>" alt=""></a>
                        <div style="text-align:left ;">

                            <div style="font-weight: 500; font-size:18px; text-align: center; " class="name"> <?php echo $fetch_book['name']; ?></div>
                        </div>
                        <div class="price">Price: RS <?php echo $fetch_book['price']; ?>/=</div>
                        
                        <form action="" method="POST">
                            <input class="hidden_input" type="hidden" name="book_name" value="<?php echo $fetch_book['name'] ?>">
                            <input class="hidden_input" type="hidden" name="book_id" value="<?php echo $fetch_book['bid'] ?>">
                            <input class="hidden_input" type="hidden" name="book_image" value="<?php echo $fetch_book['image'] ?>">
                            <input class="hidden_input" type="hidden" name="book_price" value="<?php echo $fetch_book['price'] ?>">
                            <button onclick="myFunction()" name="add_to_cart"><img src="./images/cart2.png" alt="Add to cart">
                                <a href="book_details.php?details=<?php echo $fetch_book['bid'];
                                                                    echo '-name=', $fetch_book['name']; ?>" class="update_btn">Know More</a>
                        </form>
                        
                    </div>
            <?php
                }
            } else {
                echo '<p class="empty">no products added yet!</p>';
            }
            ?>
        </div>
    </section>

     <!-- Adventure Section -->
    <section id="Adventure">
        </div>

        <div class="container px-5 mx-auto">
            <h2 class="text-gray-400 m-8 font-extrabold text-4xl text-center border-t-2 text-red-800" style="color: rgb(0, 167, 245);" >
                Adventure
            </h2>
        </div>
    </section>
     <!-- Show Products Section -->
    <section class="show-products">
        <div class="box-container">

            <?php
            // Query to get adventure books
           $select_book = mysqli_query($conn, "SELECT * FROM `book_info` WHERE category = 'Adventure' LIMIT 8") or die('Query failed: ' . mysqli_error($conn));
           // Check if there are any books 
           if (mysqli_num_rows($select_book) > 0) {
                while ($fetch_book = mysqli_fetch_assoc($select_book)) {
            ?>

                    <div class="box" style="width: 255px;height: 355px;">
                        <a href="book_details.php?details=<?php echo $fetch_book['bid'];
                                                            echo '-name=', $fetch_book['name']; ?>"> <img style="height: 200px;width: 125px;margin: auto;" class="books_images" src="added_books/<?php echo $fetch_book['image']; ?>" alt=""></a>
                        <div style="text-align:left ;">

                            <div style="font-weight: 500; font-size:18px; text-align: center; " class="name"> <?php echo $fetch_book['name']; ?></div>
                        </div>
                        <div class="price">Price: RS <?php echo $fetch_book['price']; ?>/-</div>
                        
                        <form action="" method="POST">
                            <input class="hidden_input" type="hidden" name="book_name" value="<?php echo $fetch_book['name'] ?>">
                            <input class="hidden_input" type="hidden" name="book_image" value="<?php echo $fetch_book['image'] ?>">
                            <input class="hidden_input" type="hidden" name="book_price" value="<?php echo $fetch_book['price'] ?>">
                            <button name="add_to_cart"><img src="./images/cart2.png" alt="Add to cart">
                            <a href="book_details.php?details=<?php echo $fetch_book['bid']; echo '-name=', $fetch_book['name']; ?>">Know More</a>
                        </form>
                        </div>
            <?php
                }
            } else {
                echo '<p class="empty">no products added yet!</p>';
            }
            ?>
        </div>
    </section>
    <hr style="color: black; width:5px;">

     <!-- Magical Section -->
    <section id="Magical">

        <div class="container px-5 mx-auto">
            <h2 class="text-gray-400 m-8 font-extrabold text-4xl text-center border-t-2 text-red-800"style="color: rgb(0, 167, 245);">
                Magical
            </h2>
        </div>
    </section>
    <!-- Show Products Section -->
    <section class="show-products">
        <div class="box-container">

            <?php
            // Query to get magical books
         $select_book = mysqli_query($conn, "SELECT * FROM book_info WHERE category = 'Magic' LIMIT 8") or die('Query failed: ' . mysqli_error($conn));
         // Check if there are any books   
         if (mysqli_num_rows($select_book) > 0) {
                while ($fetch_book = mysqli_fetch_assoc($select_book)) {
            ?>

                    <div class="box" style="width: 255px;height: 355px;">
                        <a href="book_details.php?details=<?php echo $fetch_book['bid'];
                                                            echo '-name=', $fetch_book['name']; ?>"> <img style="height: 200px;width: 125px;margin: auto;" class="books_images" src="added_books/<?php echo $fetch_book['image']; ?>" alt=""></a>
                        <div style="text-align:left ;">

                            <div style="font-weight: 500; font-size:18px; text-align: center;" class="name"> <?php echo $fetch_book['name']; ?></div>
                        </div>
                        <div class="price">Price: RS <?php echo $fetch_book['price']; ?>/-</div>
                        
                        <form action="" method="POST">
                            <input class="hidden_input" type="hidden" name="book_name" value="<?php echo $fetch_book['name'] ?>">
                            <input class="hidden_input" type="hidden" name="book_image" value="<?php echo $fetch_book['image'] ?>">
                            <input class="hidden_input" type="hidden" name="book_price" value="<?php echo $fetch_book['price'] ?>">
                            <button name="add_to_cart"><img src="./images/cart2.png" alt="Add to cart">
                            <a href="book_details.php?details=<?php echo $fetch_book['bid']; echo '-name=', $fetch_book['name']; ?>">Know More</a>
                        </form>
                        </div>
            <?php
                }
            } else {
                echo '<p class="empty">no products added yet!</p>';
            }
            ?>
        </div>
    </section>

    <!-- Knowledge Section -->
    <section id="Knowledge">

        <div class="container px-5 mx-auto">
            <h2 class="text-gray-400 m-8 font-extrabold text-4xl text-center border-t-2 text-red-800" style="color: rgb(0, 167, 245);">
                Knowledge
            </h2>
        </div>
    </section>
    <!-- Show Products Section -->
    <section class="show-products">
        <div class="box-container">

            <?php
            // Query to get knowledge books
          $select_book = mysqli_query($conn, "SELECT * FROM book_info WHERE category = 'knowledge' LIMIT 8") or die('Query failed: ' . mysqli_error($conn));
          // Check if there are any books  
          if (mysqli_num_rows($select_book) > 0) {
                while ($fetch_book = mysqli_fetch_assoc($select_book)) {
            ?>

                    <div class="box" style="width: 255px;height: 355px;">
                        <a href="book_details.php?details=<?php echo $fetch_book['bid'];
                                                            echo '-name=', $fetch_book['name']; ?>"> <img style="height: 200px;width: 125px;margin: auto;" class="books_images" src="added_books/<?php echo $fetch_book['image']; ?>" alt=""></a>
                        <div style="text-align:left ;">

                            <div style="font-weight: 500; font-size:18px; text-align: center;" class="name"> <?php echo $fetch_book['name']; ?></div>
                        </div>
                        <div class="price">Price: RS <?php echo $fetch_book['price']; ?>/-</div>
                       <form action="" method="POST">
                            <input class="hidden_input" type="hidden" name="book_name" value="<?php echo $fetch_book['name'] ?>">
                            <input class="hidden_input" type="hidden" name="book_image" value="<?php echo $fetch_book['image'] ?>">
                            <input class="hidden_input" type="hidden" name="book_price" value="<?php echo $fetch_book['price'] ?>">
                            <button name="add_to_cart"><img src="./images/cart2.png" alt="Add to cart">
                            <a href="book_details.php?details=<?php echo $fetch_book['bid']; echo '-name=', $fetch_book['name']; ?>">Know More</a>
                        </form>
                       </div>
            <?php
                }
            } else {
                echo '<p class="empty">no products added yet!</p>';
            }
            ?>
        </div>
    </section>

     <!-- Include the footer file -->
    <?php include 'index_footer.php'; ?>

    <script>
        // Hide messages after 8 seconds
        setTimeout(() => {
            const box = document.getElementById('messages');

            // 👇️ hides element (still takes up space on page)
            box.style.display = 'none';
        }, 8000);


       // jQuery for the hero section's functionality
        $(document).ready(function () {
        // AJAX for the mood matcher button click
        $('#mood-matcher-btn').click(function (e) {
            e.preventDefault(); // Prevent the default link action

            // Perform an AJAX request
            $.ajax({
                url: 'mood_matcher.php', 
                type: 'GET',
                success: function (response) {
                    // Handle the response from mood_matcher.php
                    $('#messages').html('<span>Matching your mood with the best books...</span>'); 
                    $('#messages').addClass('fade-in'); 
                },
                error: function () {
                    $('#messages').html('<span>Failed to load mood matching. Please try again.</span>');
                }
            });
        });

        // AJAX to add to cart functionality
        $('.add-to-cart').click(function (e) {
            e.preventDefault(); 
            var bookId = $(this).data('book-id');
            var userId = $(this).data('user-id');
            var bookName = $(this).data('book-name');
            var bookImage = $(this).data('book-image');
            var bookPrice = $(this).data('book-price');
            var bookQuantity = 1;

            $.ajax({
                url: 'add_to_cart.php', // PHP script to handle add-to-cart logic
                type: 'POST',
                data: {
                    book_id: bookId,
                    user_id: userId,
                    book_name: bookName,
                    book_image: bookImage,
                    book_price: bookPrice,
                    book_quantity: bookQuantity
                },
                success: function (response) {
                    $('#messages').html('<span>Book added to cart successfully!</span>');
                    $('#messages').addClass('fade-in');
                },
                error: function () {
                    $('#messages').html('<span>Error adding book to cart.</span>');
                    $('#messages').addClass('fade-in');
                }
            });
        });
    });
    </script>


</body>

</html>