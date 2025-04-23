<?php
class Auth {
    public function login($username, $password) {
        // Assume we have a method to validate user credentials
        // If valid, set session variables
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'user'; // This should come from the database
        setcookie("username", $username, time() + (86400 * 30), "/"); // 30 days
    }

    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        setcookie("username", "", time() - 3600, "/"); // Delete cookie
    }

    public function checkPrivileges($requiredRole) {
        return isset($_SESSION['role']) && $_SESSION['role'] === $requiredRole;
    }
}




?>