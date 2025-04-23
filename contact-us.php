<?php
    include 'config.php';

    session_start();

    // Get user ID and name from session if user is logged in
    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['user_name'];
    
    // Redirect to login page if user is not logged in
    if (!isset($user_id)) {
        header('location:login.php');
        exit();
    }

    // Initialize message array
    $message = array();

    // Check if user has submitted the contact form
    if (isset($_POST['send_msg'])) {
        // Validate user_id exists in users_info table first
        $check_user = mysqli_query($conn, "SELECT Id FROM users_info WHERE Id = '$user_id'");
        
        if (mysqli_num_rows($check_user) > 0) {
            $name = mysqli_real_escape_string($conn, $_POST['name']);
            $msg = mysqli_real_escape_string($conn, $_POST['msg']);
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $phone = mysqli_real_escape_string($conn, $_POST['phone']);

            // Use prepared statement to prevent SQL injection
            $stmt = mysqli_prepare($conn, "INSERT INTO msg (user_id, name, email, number, msg) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "issis", $user_id, $name, $email, $phone, $msg);
            
            if (mysqli_stmt_execute($stmt)) {
                $message[] = "Thank you for reaching out! We'll get back to you soon. 😊";
            } else {
                $message[] = "Database Error: " . mysqli_stmt_error($stmt);
            }
            
            mysqli_stmt_close($stmt);
        } else {
            $message[] = "Error: Invalid user account. Please try logging in again.";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VibeReads-Contact Us</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        .contact-section {
            width: 100%;
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .contact-section h1 {
            text-align: center;
            color: #333;
        }

        .contact-section h3 {
            text-align: center;
            color: #555;
        }

        .border {
            width: 80px;
            height: 3px;
            background: #007BFF;
            margin: 10px auto;
        }

        .contact-form {
            display: flex;
            flex-direction: column;
        }

        .contact-form-text {
            margin-bottom: 15px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
        }

        .contact-form-btn {
            padding: 10px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            background: #007BFF;
            color: #fff;
            cursor: pointer;
            margin-bottom: 10px;
        }

        .contact-form-btn:hover {
            background: #0056b3;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 80%;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #000;
        }
    </style>
</head>
<body>
    <?php include 'index_header.php'; ?>

    <!-- Display success or error message -->
    <?php if (!empty($message)): ?>
        <div class="modal" id="messageModal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <?php foreach ($message as $msg): ?>
                    <p><?php echo $msg; ?></p>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Contact section -->
    <div class="contact-section">
        <h1>Contact Us</h1>
        <h3>Hello, <span><?php echo htmlspecialchars($user_name); ?></span>, how can we help you?</h3>
        <div class="border"></div>

        <form class="contact-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <input type="text" class="contact-form-text" name="name" placeholder="Your name" required>
            <input type="email" class="contact-form-text" name="email" placeholder="Your email" required>
            <input type="tel" class="contact-form-text" name="phone" placeholder="Your phone">
            <textarea class="contact-form-text" name="msg" placeholder="Your message" required></textarea>
            <input type="submit" class="contact-form-btn" name="send_msg" value="Send">
            <a href="index.php" class="contact-form-btn" style="text-decoration: none; text-align: center;">Back</a>
        </form>
    </div>

    <?php include 'index_footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const messageModal = document.getElementById('messageModal');
            if (messageModal) {
                // Show the modal window
                messageModal.style.display = 'block';

                // Hide the modal window after 5 seconds
                setTimeout(() => {
                    messageModal.style.display = 'none';
                }, 5000);
            }

            // Add event listener to close button
            const closeButton = document.querySelector('.close');
            if (closeButton) {
                closeButton.addEventListener('click', function () {
                    messageModal.style.display = 'none';
                });
            }
        });
    </script>
</body>
</html>
