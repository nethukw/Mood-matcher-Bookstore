Checkout Page with Item Removal

<?php include 'config.php';

session_start();

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Check if the user is logged in
if (!isset($user_id)) {
  // If not, redirect to the login page
  header('location:login.php');
}

// Handle item removal from cart
if(isset($_GET['remove_item'])) {
  $remove_id = mysqli_real_escape_string($conn, $_GET['remove_item']);
  mysqli_query($conn, "DELETE FROM `cart` WHERE id = '$remove_id' AND user_id = '$user_id'") or die('query failed');
  header('location: checkout.php');
  exit();
}

// Check if the checkout button is clicked
if (isset($_POST['checkout'])) {
// Get the user's details from the form
  $name = mysqli_real_escape_string($conn, $_POST['firstname']);
  $number = $_POST['number'];
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $method = mysqli_real_escape_string($conn, $_POST['method']);
  $address = mysqli_real_escape_string($conn, $_POST['address']);
  $city = mysqli_real_escape_string($conn, $_POST['city']);
  $state = mysqli_real_escape_string($conn, $_POST['state']);
  $country = mysqli_real_escape_string($conn, $_POST['country']);
  $pincode = mysqli_real_escape_string($conn, $_POST['pincode']);
  $full_address = mysqli_real_escape_string($conn, $_POST['address'] . ', ' . $_POST['city'] . ', ' . $_POST['state'] . ', ' . $_POST['country'] . ' - ' . $_POST['pincode']);
  $placed_on = date('d-M-Y');

  $cart_total = 0;
  // Initialize the cart products array
  $cart_products[] = '';
  // Check if all fields are filled
  if (empty($name)) {
    $message[] = 'Please Enter Your Name';
  } elseif (empty($email)) {
    $message[] = 'Please Enter Email Id';
  } elseif (empty($number)) {
    $message[] = 'Please Enter Mobile Number';
  } elseif (empty($address)) {
    $message[] = 'Please Enter Address';
  } elseif (empty($city)) {
    $message[] = 'Please Enter city';
  } elseif (empty($state)) {
    $message[] = 'Please Enter state';
  } elseif (empty($country)) {
    $message[] = 'Please Enter country';
  } elseif (empty($pincode)) {
    $message[] = 'Please Enter your area pincode';
  } else {
// Get the cart items for the user
    $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
   // Check if there are any cart items
    if (mysqli_num_rows($cart_query) > 0) {
      // Loop through each cart item
      while ($cart_item = mysqli_fetch_assoc($cart_query)) {
        $cart_products[] = $cart_item['name'] . ' #' . $cart_item['book_id'] . ',(' . $cart_item['quantity'] . ') ';
        $quantity=$cart_item['quantity'];
        $unit_price=$cart_item['price'];
        $cart_books = $cart_item['name'];
        $sub_total = ($cart_item['price'] * $cart_item['quantity']);
        $cart_total += $sub_total;
      
      }
    }
  

  $total_books = implode(' ', $cart_products);

  // Check if the order already exists
  $order_query = mysqli_query($conn, "SELECT * FROM `confirm_order` WHERE name = '$name' AND number = '$number' AND email = '$email' AND payment_method = '$method' AND address = '$address' AND total_books = '$total_books' AND total_price = '$cart_total'") or die('query failed');

// Check if the order already exists
if (mysqli_num_rows($order_query) > 0) {
  $message[] = 'Order already placed!';
} else {
  mysqli_query($conn, "INSERT INTO `confirm_order`(user_id, name, number, email, payment_method, address,total_books, total_price, order_date) VALUES('$user_id','$name', '$number', '$email','$method', '$full_address', '$total_books', '$cart_total', '$placed_on')") or die('query failed');
      // Get the order ID
      $conn_oid= $conn->insert_id;
      $_SESSION['id'] = $conn_oid;

      // Get the cart items for the user again
        $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
        // Check if there are any cart items
        if (mysqli_num_rows($cart_query) > 0) {
          while ($cart_item = mysqli_fetch_assoc($cart_query)) {
            $cart_products[] = $cart_item['name'] . ' #' . $cart_item['book_id'] . ',(' . $cart_item['quantity'] . ') ';
            $quantity=$cart_item['quantity'];
            $unit_price=$cart_item['price'];
            $cart_books = $cart_item['name'];
            $sub_total = ($cart_item['price'] * $cart_item['quantity']);
            $cart_total += $sub_total;
          
            mysqli_query($conn, "INSERT INTO `orders`(user_id,id,address,city,state,country,pincode,book,quantity,unit_price,sub_total) VALUES('$user_id','$conn_oid','$address','$city','$state','$country','$pincode','$cart_books','$quantity','$unit_price','$sub_total')") or die('query failed');
          }
        }

      $message[] = 'order placed successfully!';

      // Clear the cart after placing the order
      mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');

       // Set a success message with order details
 $_SESSION['order_success'] = true;
 $_SESSION['order_id'] = $conn_oid;
 $_SESSION['order_total'] = $cart_total;

 // Redirect to avoid form resubmission
 header('Location: ' . $_SERVER['PHP_SELF']);
 exit();
}
    }
  }




?>



<!DOCTYPE html>
<html>

<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <title>VibeReads-Checkout</title>
  <style>
    body {
      font-family: Arial;
      font-size: 17px;
      padding: 8px;
      overflow-x: hidden;
    }

    * {
      box-sizing: border-box;
    }

    .row {
      display: -ms-flexbox;
      display: flex;
      -ms-flex-wrap: wrap;
      flex-wrap: wrap;
      margin: 0 -16px;
      padding: 30px;
    }

    .col-25 {
      -ms-flex: 25%;
      flex: 25%;
    }

    .col-50 {
      -ms-flex: 50%;
      flex: 50%;
    }

    .col-75 {
      -ms-flex: 75%;
      flex: 75%;
    }

    .col-25,
    .col-50,
    .col-75 {
      padding: 0 16px;
    }

    .container {
      background-color: #f2f2f2;
      padding: 5px 20px 15px 20px;
      border: 1px solid lightgrey;
      border-radius: 3px;
    }

    input[type=text],
    select {
      width: 100%;
      margin-bottom: 20px;
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 3px;
    }

    label {
      margin-bottom: 10px;
      display: block;
      color: black;
    }

    .icon-container {
      margin-bottom: 20px;
      padding: 7px 0;
      font-size: 24px;
    }

    .btn {
      background-color: rgb(28 146 197);
      color: white;
      padding: 12px;
      margin: 10px 0;
      border: none;
      width: 100%;
      border-radius: 3px;
      cursor: pointer;
      font-size: 17px;
    }

    .btn:hover {
      background-color: rgb(6 157 21);
      letter-spacing: 1px;
      font-weight: 600;
    }

    a {
      color: #rgb(28 146 197);
    }

    hr {
      border: 1px solid lightgrey;
    }

    span.price {
      float: right;
      color: grey;
    }

    .remove-item {
      color: red;
      text-decoration: none;
      margin-left: 10px;
      font-weight: bold;
    }
    .remove-item:hover {
      text-decoration: underline;
    }

    @media (max-width: 800px) {
      .row {
        flex-direction: column-reverse;
        padding: 0;
      }

      .col-25 {
        margin-bottom: 20px;
      }
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

.success-message {
            background-color: #dff0d8;
            border: 1px solid #d6e9c6;
            border-radius: 8px;
            color: #3c763d;
            margin: 20px auto;
            max-width: 600px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .success-message h2 {
            color: #2d662e;
            margin-bottom: 15px;
        }

        .success-message i {
            font-size: 48px;
            color: #5cb85c;
            margin-bottom: 15px;
        }

        .success-message .order-details {
            background-color: white;
            border-radius: 5px;
            padding: 15px;
            margin-top: 15px;
        }

        .success-message .order-id {
            font-weight: bold;
            color: #31708f;
        }

        .track-order-btn {
            background-color: #5cb85c;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
            transition: background-color 0.3s;
        }

        .track-order-btn:hover {
            background-color: #449d44;
            color: white;
        }

  </style>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://kit.fontawesome.com/493af71c35.js" crossorigin="anonymous"></script>
  
</head>

<body>
<?php if(isset($_SESSION['order_success']) && $_SESSION['order_success']): ?>
        <div class="success-message">
            <i class="fas fa-check-circle"></i>
            <h2>Thank You for Your Order!</h2>
            <p>Your order has been successfully placed and will be processed soon.</p>
            
            <div class="order-details">
                <p>Order ID: <span class="order-id">#<?php echo $_SESSION['order_id']; ?></span></p>
                <p>Total Amount: RS <?php echo $_SESSION['order_total']; ?>/-</p>
            </div>
            
            <p>A confirmation email will be sent to your registered email address.</p>
            <a href="orders.php" class="track-order-btn">Track Your Order</a>
        </div>
        <?php
        // Clear the success message after displaying
        unset($_SESSION['order_success']);
        unset($_SESSION['order_id']);
        unset($_SESSION['order_total']);
        ?>
    <?php endif; ?>

  <?php
  // Display messages if any
  if (isset($message)) {
    foreach ($message as $message) {
      echo '
        <div class="message" id= "messages"><span>' . $message . '</span>
        </div>
        ';
    }
  }
  ?>
<?php include 'index_header.php' ?>
  <h1 style="text-align: center; margin-top:15px;  color:rgb(9, 152, 248);">Place Your Order Here</h1>
  <p style="text-align: center; ">Just One Step away from getting your books</p>
  <div class="row">
    <div class="col-75">
      <div class="container">
        <form action="" method="POST">

          <div class="row">
            <div class="col-50">
              <h3>Billing Address</h3>
              <label for="fname"><i class="fa fa-user"></i> Full Name</label>
              <input type="text" id="fname" name="firstname" placeholder="Nethmi Wijekoon">
              <label for="email"><i class="fa fa-envelope"></i> Email</label>
              <input type="text" id="email" name="email" placeholder="example@gmail.com">
              <label for="email"><i class="fa fa-envelope"></i> Number</label>
              <input type="text" id="email" name="number" placeholder="+94787456123">
              <label for="adr"><i class="fa fa-address-card-o"></i> Address</label>
              <input type="text" id="adr" name="address" placeholder="35/3,Peradeniya,Kandy">
              <label for="city"><i class="fa fa-institution"></i> City</label>
              <input type="text" id="city" name="city" placeholder="Kandy">
              <label for="city"><i class="fa fa-institution"></i> State</label>
              <input type="text" id="city" name="state" placeholder="Kandy">

              <div style="padding: 0px;" class="row">
                <div class="col-50">
                  <label for="state">Country</label>
                  <input type="text" id="state" name="country" placeholder="Sri Lanka">
                </div>
                <div class="col-50">
                  <label for="zip">Pincode</label>
                  <input type="text" id="zip" name="pincode" placeholder="20526">
                </div>
              </div>
            </div>

            <div class="col-50">
            <div class="col-25">
    <div class="container">
      <h4>Books In Cart</h4>
      <?php
      $grand_total = 0;
      $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
      if (mysqli_num_rows($select_cart) > 0) {
        while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
          $total_price = ($fetch_cart['price'] * $fetch_cart['quantity']);
          $grand_total += $total_price;
      ?>
          <p> 
            <a href="book_details.php?details=<?php echo $fetch_cart['book_id']; ?>">
              <?php echo $fetch_cart['name']; ?>
            </a>
            <span class="price">(<?php echo 'RS ' . $fetch_cart['price'] . '/-' . ' x ' . $fetch_cart['quantity']; ?>)</span> 
            <a href="checkout.php?remove_item=<?php echo $fetch_cart['id']; ?>" class="remove-item" onclick="return confirm('Are you sure you want to remove this item from cart?');">Remove</a>
          </p>
      <?php
        }
      } else {
        echo '<p class="empty">your cart is empty</p>';
      }
      ?>

      <hr>
      <p>Grand total : <span class="price" style="color:black">RS <b><?php echo $grand_total; ?>/-</b></span></p>
    </div>
  </div>
              <div style="margin: 20px;">
                <h3>Payment </h3>
                <label for="fname">Accepted Payment Gateways</label>
                <div class="icon-container">
                  <i class="fa fa-cc-visa" style="color:navy;"></i>
                  <i class="fa-brands fa-cc-amazon-pay"></i>
                  <i class="fa-brands fa-google-pay" style="color:red;"></i>
                  <i class="fa fa-cc-paypal" style="color:#3b7bbf;"></i>
                </div>
                <div class="inputBox">
                  <label for="method">Choose Payment Method :</label>
                  <select name="method" id="method">
                    <option value="cash on delivery">Cash on delivery</option>
                    <option value="Debit card">Debit card</option>
                    <option value="Amazon Pay">Amazon Pay</option>
                    <option value="Paypal">Paypal</option>
                    <option value="Google Pay">Google Pay</option>
                  </select>
                </div>
              </div>
            </div>

          </div>
          <label>
            <input type="checkbox" checked="checked" name="sameadr"> Shipping address same as billing
          </label>
          <input type="submit" name="checkout" value="Continue to checkout" class="btn">
        </form>
      </div>
    </div>
  </div>
  <?php include 'index_footer.php'; ?>
  <script>
    // Automatically hide the message after 5 seconds
    setTimeout(() => {
      const box = document.getElementById('messages');

      // 👇️ hides element (still takes up space on page)
      box.style.display = 'none';
    }, 5000);
  </script>
</body>

</html>