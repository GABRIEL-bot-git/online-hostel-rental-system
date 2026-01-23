<?php include __DIR__ . '/db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Lodge Finder</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; min-height: 100vh; display: flex; flex-direction: column; }
        .content { flex: 1; }
        .card-img-top { height: 200px; object-fit: cover; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php"><i class="fa fa-home"></i> UniLodge</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        
        <?php if(isset($_SESSION['user_id'])): ?>
            <?php if($_SESSION['role'] == 'student'): ?>
                <li class="nav-item"><a class="nav-link" href="student_dashboard.php">My Bookings</a></li>
            <?php elseif($_SESSION['role'] == 'landlord'): ?>
                <li class="nav-item"><a class="nav-link" href="landlord_dashboard.php">My Properties</a></li>
            <?php elseif($_SESSION['role'] == 'admin'): ?>
                <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Admin Panel</a></li>
            <?php endif; ?>
            <li class="nav-item"><a class="nav-link btn btn-danger btn-sm text-white ms-2" href="logout.php">Logout</a></li>
        <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
            <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<div class="container mt-4 content">