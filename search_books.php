<?php
include 'config.php';

session_start();

// Check if the user is logged in
if(isset($_SESSION['user_name'])){
   // Get the user ID from the session
   $user_id = $_SESSION['user_id'];
   
// Check if the add to cart button is clicked
if (isset($_POST['add_to_cart'])) {
   // Get the book details from the form
   $book_name = $_POST['book_name'];
   $book_id= $_POST['book_id'];
   $book_image = $_POST['book_image'];
   $book_price = $_POST['book_price'];
   $book_quantity = '1';

   // Calculate the total price
   $total_price = number_format($book_price * $book_quantity);
   // Check if the book is already in the cart
   $select_book = $conn->query("SELECT * FROM cart WHERE bid= '$book_id' AND user_id='$user_id' ") or die('query failed');


   // If the book is already in the cart, display a message
   if (mysqli_num_rows($select_book) > 0) {
       $message[] = 'This Book is alredy in your cart';
   } else {
      // Add the book to the cart
   $conn->query("INSERT INTO cart (`user_id`,`book_id`,`name`, `price`, `image`,`quantity` ,`total`) VALUES('$user_id','$book_id','$book_name','$book_price','$book_image','$book_quantity', '$total_price')") or die('Add to cart Query failed');
   $message[] = 'Book Added To Successfully';
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
   <title>search page</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="css/style.css">
   <style>
     /* Global Styles */
:root {
  --primary-color: rgb(0, 167, 245);
  --secondary-color: #2c3e50;
  --background-color: #f5f7fa;
  --box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: linear-gradient(135deg, #f5f7fa 0%, #e4e9f2 100%);
  margin: 0;
  padding: 0;
  min-height: 100vh;
}

/* Search Form Styles */
.search-form {
  min-height: 40vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem 1rem;
  background: linear-gradient(rgba(0, 167, 245, 0.05), rgba(0, 167, 245, 0.02));
}

.search-form form {
  width: 100%;
  max-width: 800px;
  display: flex;
  gap: 1rem;
  padding: 2rem;
  background: white;
  border-radius: 20px;
  box-shadow: var(--box-shadow);
  position: relative;
  transition: var(--transition);
}

.search-form form:focus-within {
  transform: translateY(-2px);
  box-shadow: 0 6px 24px rgba(0, 167, 245, 0.15);
}

.search-form .box {
  flex: 1;
  padding: 1.2rem 1.5rem;
  border: 2px solid #e1e8ef;
  border-radius: 12px;
  font-size: 1.1rem;
  transition: var(--transition);
  outline: none;
  background: #f8fafc;
}

.search-form .box:focus {
  border-color: var(--primary-color);
  background: white;
}

.search_btn {
  padding: 1.2rem 2.5rem;
  background: var(--primary-color);
  color: white;
  border: none;
  border-radius: 12px;
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  transition: var(--transition);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.search_btn:hover {
  background: #0086c8;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 167, 245, 0.2);
}

/* Search Results Message */
.msg {
  max-width: 1200px;
  margin: 2rem auto;
  padding: 0 1rem;
  text-align: center;
}

.msg h4 {
  color: var(--secondary-color);
  font-size: 1.4rem;
  margin: 0;
  font-weight: 600;
}

/* Products Grid */
.show-products .box-container {
  max-width: 1400px;
  margin: 3rem auto;
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2.5rem;
  padding: 0 2rem;
}

.show-products .box {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  transition: var(--transition);
  box-shadow: var(--box-shadow);
  display: flex;
  flex-direction: column;
  align-items: center;
  position: relative;
  overflow: hidden;
}

.show-products .box::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, var(--primary-color), #00c6ff);
  opacity: 0;
  transition: var(--transition);
}

.show-products .box:hover::before {
  opacity: 1;
}

.show-products .box:hover {
  transform: translateY(-8px);
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

.show-products .box img.books_images {
  object-fit: cover;
  border-radius: 12px;
  margin-bottom: 1.5rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
  transition: var(--transition);
}

.show-products .box:hover img.books_images {
  transform: scale(1.05);
}

.show-products .box .name {
  color: var(--secondary-color);
  margin: 0.5rem 0;
  line-height: 1.5;
}

.show-products .box .price {
  color: var(--primary-color);
  font-weight: 700;
  font-size: 1.2rem;
  margin: 1rem 0;
  background: rgba(0, 167, 245, 0.1);
  padding: 0.5rem 1rem;
  border-radius: 8px;
}

/* Form Elements */
.hidden_input {
  display: none;
}

/* Add to Cart Button */
.show-products .box form {
  width: 100%;
  display: flex;
  gap: 1.5rem;
  margin-top: auto;
  align-items: center;
  justify-content: center;
}

.show-products .box form button {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.5rem;
  transition: var(--transition);
  position: relative;
}

.show-products .box form button::after {
  content: 'Add to Cart';
  position: absolute;
  bottom: -20px;
  left: 50%;
  transform: translateX(-50%);
  font-size: 0.75rem;
  color: #666;
  opacity: 0;
  transition: var(--transition);
}

.show-products .box form button:hover::after {
  opacity: 1;
  bottom: -25px;
}

.show-products .box form button:hover {
  transform: scale(1.15);
}

.show-products .box form img {
  width: 28px;
  height: 28px;
}

/* Know More Button */
.update_btn {
  display: inline-block;
  padding: 0.8rem 1.5rem;
  background: var(--primary-color);
  color: white;
  text-decoration: none;
  border-radius: 10px;
  transition: var(--transition);
  font-weight: 600;
  letter-spacing: 0.5px;
}

.update_btn:hover {
  background: #0086c8;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 167, 245, 0.2);
}

/* Empty Results Message */
.empty {
  text-align: center;
  color: #666;
  font-size: 1.4rem;
  margin: 4rem 0;
  padding: 2rem;
  background: white;
  border-radius: 16px;
  box-shadow: var(--box-shadow);
}

/* Responsive Design */
@media (max-width: 768px) {
  .search-form {
    min-height: 30vh;
  }
  
  .search-form form {
    flex-direction: column;
    padding: 1.5rem;
  }
  
  .search_btn {
    width: 100%;
  }
  
  .show-products .box-container {
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    padding: 0 1rem;
  }
}
   </style>
</head>

<body>
<!-- Include the header file -->
   <?php include 'index_header.php'; ?>

   <!-- Search form section -->
   <section class="search-form">

      <form action="" method="POST">
         <input type="text" class="box" name="search_box" placeholder="search products...">
         <input type="submit" name="search_btn" value="search" class="search_btn">
      </form>

   </section>

   <!-- Display search results message -->
   <div class="msg">
      <?php
      // Check if the search button is clicked
      if (isset($_POST['search_btn'])) {
         // Get the search query from the form
         $search_box = $_POST['search_box'];
         // Display the search results message
         echo '<h4>Search Result for "'. $search_box.'"is:</h4>';
      };
      ?>
   </div>

   <!-- Display search results section -->
   <section class="show-products">
      <div class="box-container">

         <?php
         // Check if the search button is clicked
         if (isset($_POST['search_btn'])) {
            // Get the search query from the form
            $search_box = $_POST['search_box'];

            // Sanitize the search query
            $search_box = filter_var($search_box, FILTER_SANITIZE_STRING);
           
            // Query the database for search results
            $select_products = mysqli_query($conn, "SELECT * FROM `book_info` WHERE name LIKE '%{$search_box}%' OR title LIKE '%{$search_box}%' OR category LIKE '%{$search_box}%'");
            
            // Check if there are any search results
            if (mysqli_num_rows($select_products) > 0) {
                // Loop through the search results
               while ($fetch_book = mysqli_fetch_assoc($select_products)) {
         ?>

                  <div class="box" style="width: 255px;height: 342px;">
                     <a href="book_details.php?details=<?php echo $fetch_book['bid'];
                                                         echo '-name=', $fetch_book['name']; ?>"> <img style="height: 200px;width: 125px;margin: auto;" class="books_images" src="added_books/<?php echo $fetch_book['image']; ?>" alt=""></a>
                     <div style="text-align:left ;">
                        <div class="name" style="font-size: 12px;">Aurthor: <?php echo $fetch_book['title']; ?></div>
                        <div style="font-weight: 500; font-size:18px; " class="name">Name: <?php echo $fetch_book['name']; ?></div>
                     </div>
                     <div class="price">Price: RS <?php echo $fetch_book['price']; ?>/-</div>
                     
                     <form action="" method="POST">
                        <input class="hidden_input" type="hidden" name="book_name" value="<?php echo $fetch_book['name'] ?>">
                        <input class="hidden_input" type="hidden" name="book_image" value="<?php echo $fetch_book['image'] ?>">
                        <input class="hidden_input" type="hidden" name="book_price" value="<?php echo $fetch_book['price'] ?>">
                        <button onclick="myFunction()" name="add_to_cart"><img src="./images/cart2.png" alt="Add to cart">
                           <a href="book_details.php?details=<?php echo $fetch_book['bid'];
                                                               echo '-name=', $fetch_book['name']; ?>" id="adventure" class="update_btn">Know More</a>
                     </form>
                  </div>
         <?php
               }
            } else {
               echo '<p class="empty">Could not find "'. $search_box.'"! </p>';
            }
         };
         ?>
      </div>
   </section>




   <?php include 'index_footer.php'; ?>

   <script src="js/script.js"></script>

</body>

</html>