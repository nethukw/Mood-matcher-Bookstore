<?php
// functions.php
require_once 'Database.php';

if (!function_exists('getCartItemCount')) {
    function getCartItemCount($conn, $userId) {
        try {
            if (!$conn) {
                throw new Exception("Database connection not available");
            }
            
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            return $row['count'];
        } catch (Exception $e) {
            error_log("Error getting cart count: " . $e->getMessage());
            return 0;
        }
    }
}