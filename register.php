<?php
include 'includes/db_connect.php';

// 1. Security Check
if (!isset($_SESSION['user_id'])) { die("Access Denied"); }

$ref = isset($_GET['ref']) ? $conn->real_escape_string($_GET['ref']) : '';
$current_user_id = $_SESSION['user_id'];

// 2. Fetch Booking + Property Details
$sql = "SELECT bookings.*, properties.title, properties.address, properties.price, properties.landlord_id, 
        users.full_name, users.email 
        FROM bookings 
        JOIN properties ON bookings.property_id = properties.property_id 
        JOIN users ON bookings.student_id = users.user_id
        WHERE bookings.payment_reference = '$ref'";

$result = $conn->query($sql);
if($result->num_rows == 0) { die("Receipt not found."); }
$row = $result->fetch_assoc();

// 3. Authorization Check
$is_owner_student = ($row['student_id'] == $current_user_id);
$is_owner_landlord = ($row['landlord_id'] == $current_user_id);
$is_admin = ($_SESSION['role'] == 'admin');

if (!$is_owner_student && !$is_owner_landlord && !$is_admin) {
    die("Access Denied.");
}

// 4. THE FIX: Logic to handle '0' Amount
// If the booking amount is 0 (due to old error), use the current property price.
$display_amount = ($row['amount'] > 0) ? $row['amount'] : $row['price'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt - <?php echo $ref; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #eee; font-family: 'Courier New', Courier, monospace; }
        .receipt-container { background: #fff; max-width: 700px; margin: 50px auto; padding: 40px; border: 1px solid #ddd; }
        .paid-stamp { border: 2px solid green; color: green; padding: 5px 20px; font-weight: bold; transform: rotate(-5deg); display: inline-block; }
        @media print { .no-print { display: none; } body { background: white; } .receipt-container { border: none; margin: 0; box-shadow: none; } }
    </style>
</head>
<body>

<div class="receipt-container">
    <div class="row mb-4">
        <div class="col-8">
            <h2 class="fw-bold">CUSTECH HOSTEL PORTAL</h2>
            <p class="text-muted">Official Payment Receipt</p>
        </div>
        <div class="col-4 text-end">
            <div class="paid-stamp">PAID</div>
        </div>
    </div>
    <hr>
    <div class="row mb-3">
        <div class="col-6">
            <h5 class="text-primary">Billed To:</h5>
            <p class="mb-0"><strong><?php echo htmlspecialchars($row['full_name']); ?></strong></p>
            <p><?php echo htmlspecialchars($row['email']); ?></p>
        </div>
        <div class="col-6 text-end">
            <h5 class="text-primary">Receipt Info:</h5>
            <p class="mb-0"><strong>Ref ID:</strong> <?php echo $row['payment_reference']; ?></p>
            <p class="mb-0"><strong>Date:</strong> <?php echo date('d M Y', strtotime($row['booking_date'])); ?></p>
        </div>
    </div>

    <table class="table table-bordered mt-4">
        <thead class="table-light"><tr><th>Description</th><th class="text-end">Amount</th></tr></thead>
        <tbody>
            <tr>
                <td>
                    <strong><?php echo htmlspecialchars($row['title']); ?></strong><br>
                    <small class="text-muted"><?php echo htmlspecialchars($row['address']); ?></small>
                </td>
                <td class="text-end">₦<?php echo number_format($display_amount); ?></td>
            </tr>
            <tr>
                <td class="text-end"><strong>Total Paid</strong></td>
                <td class="text-end bg-light"><strong>₦<?php echo number_format($display_amount); ?></strong></td>
            </tr>
        </tbody>
    </table>

    <div class="mt-5 text-center text-muted no-print">
        <button onclick="window.print()" class="btn btn-dark">Print / Save PDF</button>
    </div>
</div>
</body>
</html>