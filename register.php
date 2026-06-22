<?php
// 1. THE SESSION FIX: Only start a session if one doesn't exist yet

include 'includes/db_connect.php'; 

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $conn->real_escape_string($_POST['role']);
    $phone = $conn->real_escape_string($_POST['phone']);
    
    // 2. NIN is now captured from everyone
    $nin = $conn->real_escape_string($_POST['nin']);

    $check_email = $conn->query("SELECT * FROM users WHERE email = '$email'");
    
    if ($check_email->num_rows > 0) {
        $message = "<div class='alert alert-danger'>Email already exists. Please login.</div>";
    } else {
        // Insert new user into the database
        $sql = "INSERT INTO users (full_name, email, password, role, phone, nin) 
                VALUES ('$full_name', '$email', '$password', '$role', '$phone', '$nin')";

        if ($conn->query($sql)) {
            $message = "<div class='alert alert-success'>Registration successful! You can now <a href='login.php'>Login here</a>.</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - CUSTECH Hostel Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow-lg p-4" style="width: 100%; max-width: 500px; border-radius: 15px;">
        <div class="card-body">
            <div class="text-center mb-4">
                <h2 class="fw-bold text-primary">Create Account</h2>
                <p class="text-muted">Join the CUSTECH Off-Campus Housing Portal</p>
            </div>

            <?php echo $message; ?>

            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control" required placeholder="John Doe">
                </div>

                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" required placeholder="name@example.com">
                </div>

                <div class="mb-3">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-control" required placeholder="08012345678">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">National Identity Number (NIN)</label>
                    <input type="text" name="nin" class="form-control" required placeholder="11-digit NIN" maxlength="11">
                    <small class="text-muted">Required for identity verification and fraud prevention.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="********">
                </div>

                <div class="mb-3">
                    <label class="form-label">I am a...</label>
                    <select name="role" class="form-select" required>
                        <option value="" disabled selected>Select Role</option>
                        <option value="student">Student</option>
                        <option value="landlord">Landlord / Agent</option>
                    </select>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">Register</button>
                </div>
            </form>

            <div class="text-center mt-3">
                <p>Already have an account? <a href="login.php" class="text-decoration-none">Login here</a></p>
            </div>
        </div>
    </div>
</div>

</body>
</html>