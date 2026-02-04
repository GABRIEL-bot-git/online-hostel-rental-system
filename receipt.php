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

// 4. Smart Price Display
$display_amount = ($row['amount'] > 0) ? $row['amount'] : $row['price'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt - <?php echo $ref; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #eee; font-family: 'Times New Roman', Times, serif; } /* Changed font to match screenshot style */
        
        .receipt-container { 
            background: #fff; 
            max-width: 800px; 
            margin: 50px auto; 
            padding: 40px; 
            border: 1px solid #ddd; 
            position: relative; /* For absolute positioning of stamp */
        }
        
        .paid-stamp { 
            color: #198754; /* Bootstrap Success Green */
            border: 3px solid #198754; 
            padding: 10px 20px; 
            font-weight: bold; 
            font-size: 1.2rem;
            text-transform: uppercase; 
            border-radius: 8px;
            transform: rotate(-10deg);
            display: inline-block;
            opacity: 0.8;
        }

        /* --- PRINT STYLES (The Fix) --- */
        @media print {
            body { 
                background: white; 
            }
            .receipt-container { 
                width: 100%;
                max-width: 800px; 
                margin: 20px auto !important; /* Forces centering on paper */
                border: none; 
                box-shadow: none;
                padding: 20px;
            }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="receipt-container shadow-sm">
    
    <div class="text-center mb-5 position-relative">
        <h1 class="fw-bold text-uppercase mb-2" style="font-size: 2.5rem;">CUSTECH HOSTEL PORTAL</h1>
        <p class="text-muted fst-italic mb-0">Official Payment Receipt</p>
        <div style="width: 100px; height: 3px; background: #333; margin: 10px auto;"></div>

        <div class="position-absolute top-0 end-0 mt-2">
            <div class="paid-stamp">PAID</div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-6">
            <h5 class="text-primary fw-bold text-uppercase">Billed To:</h5>
            <p class="mb-0 fs-5"><strong><?php echo htmlspecialchars($row['full_name']); ?></strong></p>
            <p class="text-muted"><?php echo htmlspecialchars($row['email']); ?></p>
        </div>
        <div class="col-6 text-end">
            <h5 class="text-primary fw-bold text-uppercase">Receipt Info:</h5>
            <p class="mb-1"><strong>Ref ID:</strong> <span class="font-monospace"><?php echo $row['payment_reference']; ?></span></p>
            <p class="mb-0"><strong>Date:</strong> <?php echo date('d M Y', strtotime($row['booking_date'])); ?></p>
        </div>
    </div>

    <table class="table table-bordered border-dark mt-4">
        <thead class="table-light border-dark">
            <tr>
                <th class="text-uppercase py-3">Description</th>
                <th class="text-end text-uppercase py-3">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="py-3">
                    <strong class="fs-5"><?php echo htmlspecialchars($row['title']); ?></strong><br>
                    <span class="text-muted"><?php echo htmlspecialchars($row['address']); ?></span>
                </td>
                <td class="text-end fs-5 py-3">₦<?php echo number_format($display_amount); ?></td>
            </tr>
            <tr>
                <td class="text-end fw-bold py-3">TOTAL PAID</td>
                <td class="text-end fw-bold bg-light py-3 fs-5">₦<?php echo number_format($display_amount); ?></td>
            </tr>
        </tbody>
    </table>

    <div class="mt-5 pt-4 text-center text-muted border-top">
        <p class="mb-1"><em>This receipt was generated electronically and is valid without a signature.</em></p>
        <p class="fw-bold">Confluence University of Science and Technology, Osara</p>
    </div>

    <div class="mt-4 text-center no-print">
        <button onclick="window.print()" class="btn btn-dark btn-lg px-4"><i class="fa fa-print"></i> Print / Save PDF</button>
        <br><br>
        <button onclick="window.history.back()" class="btn btn-outline-secondary btn-sm">Go Back</button>
    </div>

</div>

<script>
    // Slight delay to ensure styles load before print dialog opens
    window.onload = function() { setTimeout(function(){ window.print(); }, 500); }
</script>

</body>
</html>