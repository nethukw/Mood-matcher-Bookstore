<?php
include 'config.php';
session_start();

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Check if the user ID is set
if(!isset($user_id)){
     // If not, redirect to the login page
   header('location:login.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Orders - VibeReads Bookstore</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="./css/hello.css">

   <style>
       body {
           font-family: 'Inter', sans-serif;
           background-color: #f7f7f7;
       }

       .title {
           text-align: center;
           margin-bottom: 20px;
           text-transform: uppercase;
           color: #3b3b3b;
           font-size: 36px;
           letter-spacing: 1px;
       }

       .box-container {
           max-width: 1200px;
           margin: 0 auto;
           display: flex;
           flex-wrap: wrap;
           gap: 20px;
           justify-content: center;
       }

       .box {
           flex: 1 1 300px;
           background-color: #fff;
           border-radius: 8px;
           padding: 15px;
           border: 1px solid #e0e0e0;
           box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
           transition: transform 0.3s ease;
       }

       .box:hover {
           transform: translateY(-5px);
       }

       .box p {
           font-size: 18px;
           color: #555;
           line-height: 1.6;
       }

       .box p span {
           font-weight: 600;
           color: #333;
       }

       .payment-status {
           font-weight: bold;
           padding: 4px 10px;
           border-radius: 4px;
       }

       .pending {
           background-color: #f9a825;
           color: white;
       }

       .paid {
           background-color: #43a047;
           color: white;
       }

       .box a {
           display: inline-block;
           margin-top: 10px;
           color: #007bff;
           font-weight: bold;
           text-decoration: none;
       }

       .box a:hover {
           text-decoration: underline;
       }

       @media (max-width: 768px) {
           .box-container {
               flex-direction: column;
               align-items: center;
           }

           .box {
               width: 100%;
               margin-bottom: 20px;
           }
       }

       .modal {
           position: fixed;
           top: 0;
           left: 0;
           width: 100%;
           height: 100%;
           background-color: rgba(0, 0, 0, 0.5);
           display: none;
           justify-content: center;
           align-items: center;
       }

       .modal-content {
           background-color: #fff;
           padding: 30px;
           border-radius: 10px;
           max-width: 600px;
           width: 100%;
           box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
       }

       .modal h2 {
           font-size: 24px;
           font-weight: bold;
       }

       .modal button {
           background-color: #ff5252;
           color: white;
           padding: 8px 15px;
           border: none;
           border-radius: 5px;
           cursor: pointer;
       }

       .modal button:hover {
           background-color: #d32f2f;
       }
   </style>
</head>
<body>
 <!-- Include the header file -->
<?php include 'index_header.php'; ?>

 <!-- Placed orders section -->
<section class="placed-orders">
   <h1 class="title">Placed Orders</h1>
   
    <!-- Box container -->
   <div class="box-container">
      <?php
      // Query to select orders from the database
      $select_book = mysqli_query($conn, "SELECT * FROM `confirm_order` WHERE user_id = '$user_id' ORDER BY order_date DESC") or die('query failed');
      // Check if there are any orders
      if(mysqli_num_rows($select_book) > 0){
        // Loop through each order
          while($fetch_book = mysqli_fetch_assoc($select_book)){
      ?>

      <!-- Box for each order -->
      <div class="box">
         <p>Order Date: <span><?php echo $fetch_book['order_date']; ?></span></p>
         <p>Order Id: <span>#<?php echo $fetch_book['order_id']; ?></span></p>
         <p>Name: <span><?php echo $fetch_book['name']; ?></span></p>
         <p>Mobile Number: <span><?php echo $fetch_book['number']; ?></span></p>
         <p>Email Id: <span><?php echo $fetch_book['email']; ?></span></p>
         <p>Address: <span><?php echo $fetch_book['address']; ?></span></p>
         <p>Payment Method: <span><?php echo $fetch_book['payment_method']; ?></span></p>
         <p>Your Orders: <span><?php echo $fetch_book['total_books']; ?></span></p>
         <p>Total Price: <span>RS <?php echo $fetch_book['total_price']; ?>/-</span></p>
         <p>Payment Status: <span class="payment-status <?php echo $fetch_book['payment_status'] == 'pending' ? 'pending' : 'paid'; ?>">
            <?php echo ucfirst($fetch_book['payment_status']); ?>
         </span></p>

         <!-- Print receipt link -->
         <p><a href="invoice.php?order_id=<?php echo $fetch_book['order_id']; ?>" target="_blank">Print Receipt</a></p>
      </div>
      <?php
       }
      } else {
         // If no orders found, display a message to the user
          echo '<p class="empty">You have not placed any order yet!!!!</p>';
      }
      ?>
   </div>
</section>

<!-- Include the footer file -->
<?php include 'index_footer.php'; ?>

<script src="js/script.js"></script>
</body>
</html>
