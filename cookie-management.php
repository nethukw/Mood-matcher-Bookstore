<?php
// Function to save form data to cookies
function saveFormData($data) {
    $expiry = time() + (30 * 60); // Cookie expires in 30 minutes
    foreach ($data as $field => $value) {
        setcookie("checkout_" . $field, $value, $expiry, "/");
    }
}

// Function to get saved form data from cookies
function getSavedFormData() {
    $formData = [];
    $fields = ['firstname', 'email', 'number', 'address', 'city', 'state', 'country', 'pincode'];
    
    foreach ($fields as $field) {
        if (isset($_COOKIE["checkout_" . $field])) {
            $formData[$field] = $_COOKIE["checkout_" . $field];
        }
    }
    return $formData;
}

// Function to clear form cookies after successful order
function clearFormCookies() {
    $fields = ['firstname', 'email', 'number', 'address', 'city', 'state', 'country', 'pincode'];
    foreach ($fields as $field) {
        setcookie("checkout_" . $field, "", time() - 3600, "/");
    }
}

// Function to save order details in cookies
function saveOrderCookie($orderId, $total) {
    $expiry = time() + (24 * 60 * 60); // 24 hours
    setcookie("last_order_id", $orderId, $expiry, "/");
    setcookie("last_order_total", $total, $expiry, "/");
}
?>