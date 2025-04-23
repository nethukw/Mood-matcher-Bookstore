<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<div class="sub-menu-wrap" id="subMenu">
<div class="sub-menu">
  <div class="user-info">
    <img src="images/ds2.png" />
    <div class="user-info" style="display: block;">
    <h3>Hello, <?php echo $_SESSION['user_name']; ?></h3>
    <h6><?php echo $_SESSION['user_email']; ?></h6>
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
</div>
</div>
</body>
</html>