<?php
session_start();
include 'includes/db_connect.php';

// Ensure only logged-in landlords can access this script
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'landlord') {
    die("Unauthorized Access");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    
    // Sanitize user inputs
    $phone = $conn->real_escape_string($_POST['phone']);
    $nin = $conn->real_escape_string($_POST['nin']);
    $bank_name = $conn->real_escape_string($_POST['bank_name']);
    $account_number = $conn->real_escape_string($_POST['account_number']);
    $account_name = $conn->real_escape_string($_POST['account_name']);

    // Update the database
    $sql = "UPDATE users SET 
            phone = '$phone', 
            nin = '$nin', 
            bank_name = '$bank_name', 
            account_number = '$account_number', 
            account_name = '$account_name' 
            WHERE user_id = '$user_id'";

    if ($conn->query($sql)) {
        // UI/UX: Show a clean success alert and redirect
        echo "<script>
                alert('Profile and Bank details updated successfully.');
                window.location.href = 'landlord_dashboard.php';
              </script>";
    } else {
        echo "<script>
                alert('Error updating profile. Please try again.');
                window.history.back();
              </script>";
    }
}
?>