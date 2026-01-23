<?php
// db_connect.php
$servername = "localhost";
$username = "root";
$password = ""; // Default XAMPP password is empty
$dbname = "hostel_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
session_start();

function clean($data) {
    global $conn;
    // 1. Remove extra spaces
    $data = trim($data);
    // 2. Convert special characters to HTML entities (Prevents <script> tags)
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}
?>