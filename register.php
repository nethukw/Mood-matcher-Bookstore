<?php
    include 'config.php';
    // Check if the form has been submitted
    if(isset($_POST['submit'])) {
      // Sanitize user input to prevent SQL injection
      $name = mysqli_real_escape_string($conn, $_POST['Name']);
      $Sname = mysqli_real_escape_string($conn, $_POST['Sname']);
      $email = mysqli_real_escape_string($conn, $_POST['email']);
      $password = mysqli_real_escape_string($conn, ($_POST['password']));
      $cpassword = mysqli_real_escape_string($conn, ($_POST['cpassword']));
      $user_type = $_POST['user_type'];

      // Query the database to check if the email already exists
      $select_users = $conn->query("SELECT * FROM users_info WHERE email = '$email'") or die('query failed');

      // Check if the email already exists in the database
      if(mysqli_num_rows($select_users)!=0){
        // If the email exists, display an error message
        $message[]='User Already exits!';
      }else{
        // Check if the password and confirm password match
        if($password !=$cpassword){
          // If the passwords do not match, display an error message
          $message[] = 'Confirm password not matched.';
        }else{
          // If the passwords match, insert the user data into the database
          mysqli_query($conn, "INSERT INTO users_info(`name`, `surname`, `email`, `password`, `user_type`) VALUES('$name','$Sname','$email','$password','$user_type')") or die('Query failed');
          // Display a success message
          $message[]='Registration Done Successfully';
        }
      }
    }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/register.css  " />

    <title>Register</title>
    <style>
      .container2 {
  display: flex;
  justify-content: center;
  background-image: linear-gradient(45deg,
    rgba(0, 0, 3, 0.1),
    rgba(0, 0, 0, 0.5)), url(../bgimg/2.jpg);
  background-repeat: no-repeat;
  background-position: center;
  background-size: cover;
  height: 98vh;
}
    </style>
    <style>
       .container form .link{
            text-decoration: none; color:white;  border-radius: 17px; padding: 8px 18px; margin: 0px 10px; background: rgb(0, 0, 0); font-size: 20px;
        }
        .container form .link:hover{
            background: rgb(0, 167, 245);
        }
    </style>
  </head>
  <body>
    <!-- Display error or success messages -->
  <?php
    if(isset($message)){
      foreach($message as $message){
        echo '
        <div class="message" id= "messages"><span>'.$message.'</span>
        </div>
        ';
      }
    }
    ?>

    <!-- Container element for the registration form -->
    <div class="container">
      <form action="" method="post">
         <h3 style="color:white">Register to Use <a href="index.php"><span>Vibe</span><span>Reads</span></a></h3>
         <input type="text" name="Name" placeholder="Enter Name" required class="text_field ">
         <input type="text" name="Sname" placeholder="Enter Surname" required class="text_field">
         <input type="email" name="email" placeholder="Enter Email Id" required class="text_field">
         <input type="password" name="password" placeholder="Enter password" required class="text_field">
         <input type="password" name="cpassword" placeholder="Confirm password" required class="text_field">
         <select name="user_type" id="" required class="text_field">
            <option value="User">User</option>
         </select>
         <input type="submit" value="Register" name="submit" class="btn text_field">
         <p>Already have a Account? <br> <a class="link" href="login.php">Login</a><a class="link" href="index.php">Back</a></p>
      </form>
    </div>


    <script>
      //hide the message element after 8 seconds
setTimeout(() => {
  const box = document.getElementById('messages');

  // 👇️ hides element (still takes up space on page)
  box.style.display = 'none';
}, 8000);
</script>
  </body>
</html>
