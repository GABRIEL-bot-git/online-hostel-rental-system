<?php 
include 'includes/header.php'; 

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $sql = "INSERT INTO users (full_name, email, password, role) VALUES ('$name', '$email', '$pass', '$role')";
    if ($conn->query($sql)) {
        echo "<div class='alert alert-success'>Registration Successful. <a href='login.php'>Login here</a></div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">Register</div>
            <div class="card-body">
                <form method="POST">
                    <input type="text" name="name" class="form-control mb-3" placeholder="Full Name" required>
                    <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
                    <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
                    <select name="role" class="form-select mb-3">
                        <option value="student">Student</option>
                        <option value="landlord">Landlord</option>
                    </select>
                    <button type="submit" name="register" class="btn btn-primary w-100">Register</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>