<?php 
include 'includes/header.php'; 

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
            $_SESSION['email'] = $row['email'];
            
            if($row['role'] == 'landlord') echo "<script>window.location='landlord_dashboard.php';</script>";
            elseif($row['role'] == 'admin') echo "<script>window.location='admin_dashboard.php';</script>";
            else echo "<script>window.location='index.php';</script>";
        } else {
            echo "<div class='alert alert-danger'>Invalid Password</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>User not found</div>";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow">
            <div class="card-header bg-success text-white">Login</div>
            <div class="card-body">
                <form method="POST">
                    <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
                    <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
                    <button type="submit" name="login" class="btn btn-success w-100">Login</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>