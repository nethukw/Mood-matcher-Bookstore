<?php
include 'config.php';
session_start();

class User {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function login($email, $password) {
        // Escape inputs to prevent SQL injection
        $email = mysqli_real_escape_string($this->conn, $email);
        $password = mysqli_real_escape_string($this->conn, $password);

        // Query the database
        $query = "SELECT * FROM users_info WHERE email = '$email' AND password = '$password'";
        $result = $this->conn->query($query);

        // Check if a user was found
        if ($result && mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);

            // Set session variables based on user type
            if ($row['user_type'] == 'User') {
                $_SESSION['user_name'] = $row['name'];
                $_SESSION['user_email'] = $row['email'];
                $_SESSION['user_id'] = $row['Id'];
                return true; // Login successful
            }
        }

        return false; // Login failed
    }
}

// Check if the login form has been submitted
if (isset($_POST['login'])) {
    $user = new User($conn);
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($user->login($email, $password)) {
        header('location: index.php'); // Redirect to index.php on successful login
        exit(); // Ensure no further code is executed after the redirect
    } else {
        $message[] = 'Incorrect Email Id or Password!'; // Error message
    }
}
?>