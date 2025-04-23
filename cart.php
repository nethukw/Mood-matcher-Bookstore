<?php
//include database
include 'config.php';

//start session
session_start();

// Get the user ID and username from the session
$user_id = $_SESSION['user_id'];
$user_name =$_SESSION['user_name'];

// Get the user's data from the database
if(!isset($_SESSION['user_id'])){
   header('location:login.php');
   exit;
}

// Check if the remove button is clicked
if(isset($_GET['remove'])){
     // Get the ID of the item to be removed
    $remove_id=$_GET['remove'];
    // Delete the item from the cart
    mysqli_query($conn, "DELETE FROM `cart` WHERE id='$remove_id'") or die('query failed');
    $message[]='Removed Successfully';
    header('location:cart.php');
}

// Check if the update button is clicked
if(isset($_POST['update'])){
    $update_cart_id =$_POST['cart_id'];
    $book_price=$_POST['book_price'];
    $update_quantity =$_POST['update_quantity'];
    $total_price =$book_price * $update_quantity;
    mysqli_query($conn, "UPDATE `cart` SET `quantity`='$update_quantity', `total`='$total_price' WHERE `id`='$update_cart_id'") or die('query failed');
    
    $message[]=''.$user_name.' your cart updated successfully';
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="css/hello.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VibeReads-Cart</title>
    <style>
        .cart-btn1,.cart-btn2{
            
            display: inline-block;
   margin: auto;
   padding:0.8rem 1.2rem;
   cursor: pointer;
   color:white;
   font-size: 15px;
   border-radius: .5rem;
   text-transform: capitalize;
        }
        .cart-btn1{
            margin-left: 40%;
            background-color: #ffa41c;
            color: black;
        }
        .cart-btn2{
            background-color: rgb(0, 167, 245);
            color: black;
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

.pdf-message {
    position: fixed;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    background-color: #f8f9fa;
    padding: 15px;
    border-left: 4px solid #ffa41c;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    max-width: 200px;
    border-radius: 8px;
    animation: slideIn 0.5s ease-out;
}

.pdf-message p {
    margin: 0;
    color: #333;
    font-size: 16px;
    line-height: 1.4;
}

.pdf-message a {
    color: #0066cc;
    text-decoration: none;
    font-weight: bold;
    transition: color 0.3s ease;
}

.pdf-message a:hover {
    color: #ffa41c;
}

@keyframes slideIn {
    from {
        right: -200px;
        opacity: 0;
    }
    to {
        right: 20px;
        opacity: 1;
    }
}
    </style>
</head>

<body>
    <?php
    //Include the header file
    include 'index_header.php';
    ?>
    <div class="cart_form">
    <?php
     // Check if there are any messages to display
    if(isset($message)){
        // Loop through the messages and display them
      foreach($message as $message){
        echo '
        <div class="message" id="messages"><span>'.$message.'</span>
        </div>
        ';
      }
    }
    ?>
        <table style="width: 70%; align-items:center; margin:10px auto;" >
            <thead>
                <th>Image</th>
                <th>Name</th>
                <th>price</th>
                <th>Quatity</th>
                <th>Total (RS)</th>
            </thead>
            <tbody>
                
                <?php
                 // Initialize the total variable
                $total = 0;
                $select_book = $conn->query("SELECT id, name,price, image ,quantity,total  FROM cart Where user_id= $user_id");
                // Check if there are any items in the cart
                if ($select_book->num_rows  > 0) {
                     // Loop through the cart items and display them
                    while ($row = $select_book->fetch_assoc()) {
                ?>
                        <tr>
                            <td><img style="height: 90px;" src="./added_books/<?php echo $row['image']; ?>" alt=""></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['price']; ?></td>
                            <td>
                                <form action="" method="POST">
                                    <input type="number" name="update_quantity" min="1" max="10" value="<?php echo $row['quantity']; ?>">
                                    <input type="hidden" name="cart_id" value="<?php echo $row['id']; ?>">
                                    <input class="hidden_input" type="hidden" name="book_price" value="<?php echo $row['price'] ?>">
                                
                                <button style="background:transparent ;" name="update"><img style="height: 26px; cursor:pointer;" src="./images/update1.png" alt="update"></button> | 
                                <a style="color: red;" href="cart.php?remove=<?php echo $row['id'];?>"> Remove</a>
                                </form>
                           
                            
                        </td>
                            <td><?php $sub_total=$row['price']*$row['quantity']; echo $subtotal=number_format($row['price']*$row['quantity']); ?></td>
                            </tr>

                <?php
                 // Add the subtotal to the total
                $total += $sub_total;
                    }
                } else {
                    echo '<p class="empty">There is nothing in cart yet !!!!!!!!</p>';
                }
                ?>
                <tr>
                    <th style="text-align:center;" colspan="3">Total</th>
                    <th colspan="2">RS <?php echo $total; ?>/- </th>

                </tr>
                
                
            </tbody>
        </table>
        <a href="checkout.php" class="btn cart-btn1" style="display:<?php if($total>1){ echo 'inline-block'; }else{ echo 'none'; };?>" > &nbsp; Proceed to Checkout</a> <a class="cart-btn2" href="index.php">Continue Shoping</a>
    </div>
    <div class="pdf-message">
         <!-- Display the PDF message box -->
    <p>📚 Looking for digital versions? Get PDF copies of your favorite books! <a href="contact-us.php">Contact us</a> for more details.</p>
</div>
    <?php include'index_footer.php'; ?>
    
    <script>
setTimeout(() => {
  const box = document.getElementById('messages');

  // 👇️ hides element (still takes up space on page)
  box.style.display = 'none';
}, 5000);
</script>

</body>

</html>