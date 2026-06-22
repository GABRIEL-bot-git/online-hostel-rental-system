<?php
include 'includes/db_connect.php';
// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("Unauthorized Access");
}

// 1. Handle Property Approvals
if(isset($_GET['approve_prop'])){
    $pid = (int)$_GET['approve_prop'];
    $conn->query("UPDATE properties SET is_approved = 1 WHERE property_id = '$pid'");
    echo "<script>alert('Property Approved.'); window.location.href='admin_dashboard.php';</script>";
}

// 2. Handle Withdrawal Approvals
if(isset($_GET['pay_withdrawal'])){
    $wid = (int)$_GET['pay_withdrawal'];
    $conn->query("UPDATE withdrawals SET status = 'paid' WHERE withdrawal_id = '$wid'");
    echo "<script>alert('Withdrawal marked as Paid.'); window.location.href='admin_dashboard.php';</script>";
}

// 3. Handle Refund Approvals
if(isset($_GET['approve_refund']) && isset($_GET['booking_id'])){
    $bid = (int)$_GET['booking_id'];
    // Get booking details to deduct money from landlord's wallet
    $b_query = $conn->query("SELECT b.amount, p.landlord_id, p.property_id FROM bookings b JOIN properties p ON b.property_id = p.property_id WHERE b.booking_id = '$bid'");
    $b_data = $b_query->fetch_assoc();
    
    $amt = $b_data['amount'];
    $lid = $b_data['landlord_id'];
    $pid = $b_data['property_id'];

    // Deduct from Landlord
    $conn->query("UPDATE users SET wallet_balance = wallet_balance - $amt WHERE user_id = '$lid'");
    // Update Booking
    $conn->query("UPDATE bookings SET booking_status = 'refunded' WHERE booking_id = '$bid'");
    // Make Property Available Again
    $conn->query("UPDATE properties SET status = 'available' WHERE property_id = '$pid'");

    echo "<script>alert('Refund Approved and Property made available.'); window.location.href='admin_dashboard.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4 text-danger fw-bold">System Administration</h2>

    <!-- Refunds Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-danger text-white fw-bold">Pending Student Refunds</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead><tr><th>Student</th><th>Property</th><th>Reason</th><th>Amount</th><th>Action</th></tr></thead>
                <tbody>
                    <?php
                    $refs = $conn->query("SELECT b.*, u.full_name, p.title FROM bookings b JOIN users u ON b.student_id = u.user_id JOIN properties p ON b.property_id = p.property_id WHERE b.booking_status = 'refund_requested'");
                    while($r = $refs->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $r['full_name']; ?></td>
                        <td><?php echo $r['title']; ?></td>
                        <td class="text-danger">"<?php echo $r['refund_reason']; ?>"</td>
                        <td>₦<?php echo number_format($r['amount']); ?></td>
                        <td>
                            <a href="admin_dashboard.php?approve_refund=1&booking_id=<?php echo $r['booking_id']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Deduct from landlord and approve refund?');">Approve Refund</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Withdrawals Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white fw-bold">Landlord Withdrawal Requests</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead><tr><th>Landlord</th><th>Bank Details</th><th>Amount</th><th>Action</th></tr></thead>
                <tbody>
                    <?php
                    $with = $conn->query("SELECT w.*, u.full_name, u.bank_name, u.account_number FROM withdrawals w JOIN users u ON w.landlord_id = u.user_id WHERE w.status = 'pending'");
                    while($w = $with->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $w['full_name']; ?></td>
                        <td><?php echo $w['bank_name'] . ' - ' . $w['account_number']; ?></td>
                        <td class="fw-bold">₦<?php echo number_format($w['amount']); ?></td>
                        <td>
                            <a href="admin_dashboard.php?pay_withdrawal=<?php echo $w['withdrawal_id']; ?>" class="btn btn-sm btn-primary" onclick="return confirm('Have you transferred the funds?');">Mark as Paid</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Properties Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white fw-bold">Pending Property Approvals</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead><tr><th>Landlord</th><th>Title</th><th>Address</th><th>Action</th></tr></thead>
                <tbody>
                    <?php
                    $props = $conn->query("SELECT p.*, u.full_name FROM properties p JOIN users u ON p.landlord_id = u.user_id WHERE p.is_approved = 0");
                    while($p = $props->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $p['full_name']; ?></td>
                        <td><?php echo $p['title']; ?></td>
                        <td><?php echo $p['address']; ?></td>
                        <td>
                            <a href="admin_dashboard.php?approve_prop=<?php echo $p['property_id']; ?>" class="btn btn-sm btn-info text-white">Approve Property</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</body>
</html>