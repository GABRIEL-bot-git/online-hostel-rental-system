<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    die("Unauthorized Access");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_SESSION['user_id'];
    $booking_id = (int) $_POST['booking_id'];
    $reason = $conn->real_escape_string($_POST['reason']);

    $check_query = $conn->query("SELECT * FROM bookings WHERE booking_id = '$booking_id' AND student_id = '$student_id'");
    
    if ($check_query->num_rows > 0) {
        $sql = "UPDATE bookings SET booking_status = 'refund_requested', refund_reason = '$reason' WHERE booking_id = '$booking_id'";
        
        if ($conn->query($sql)) {
            echo "<script>alert('Refund request submitted successfully to the Admin.'); window.location.href = 'student_dashboard.php';</script>";
        }
    }
}
?>