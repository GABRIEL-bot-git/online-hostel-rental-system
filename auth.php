<?php
// auth.php
include 'db_connect.php';

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $sql = "INSERT INTO users (full_name, email, password, role) VALUES ('$name', '$email', '$pass', '$role')";
    if ($conn->query($sql)) {
        echo "<script>alert('Registration Successful'); window.location='login.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($pass, $row['password'])) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['name'] = $row['full_name'];
            
            // Redirect based on role
            if($row['role'] == 'landlord') header("Location: landlord_dashboard.php");
            else header("Location: index.php"); 
        } else {
            echo "Invalid Password";
        }
    } else {
        echo "User not found";
    }
}
?>