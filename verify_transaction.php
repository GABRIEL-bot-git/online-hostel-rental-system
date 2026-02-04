<?php
include 'includes/header.php';

$ref = isset($_GET['reference']) ? $conn->real_escape_string($_GET['reference']) : '';
$prop_id = isset($_GET['prop_id']) ? (int)$_GET['prop_id'] : 0;
$student_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

$message = "";
$status_icon = "";

if ($ref && $prop_id && $student_id) {
    
    // 1. FIX: Fetch the actual price from the properties table first
    $price_query = $conn->query("SELECT price FROM properties WHERE property_id = '$prop_id'");
    $price_row = $price_query->fetch_assoc();
    $actual_amount = $price_row['price']; // Get the real price

    // 2. Insert Booking Record with the ACTUAL AMOUNT
    $sql = "INSERT INTO bookings (property_id, student_id, payment_reference, amount, payment_status) 
            VALUES ('$prop_id', '$student_id', '$ref', '$actual_amount', 'success')";

    if ($conn->query($sql)) {
        // 3. Update Property Status to 'taken'
        $conn->query("UPDATE properties SET status='taken' WHERE property_id='$prop_id'");

        // Success UI
        $status_icon = "<div class='mb-4'><i class='fa fa-check-circle text-success' style='font-size: 80px;'></i></div>";
        $message = "<h2 class='fw-bold text-success'>Payment Successful!</h2>
                    <p class='lead text-muted'>Your accommodation has been secured.</p>
                    <div class='bg-light p-3 rounded mt-3 text-start d-inline-block'>
                        <p class='mb-1'><strong>Ref ID:</strong> $ref</p>
                        <p class='mb-1'><strong>Amount:</strong> ₦" . number_format($actual_amount) . "</p>
                        <p class='mb-0'><strong>Date:</strong> " . date('d M Y, h:i A') . "</p>
                    </div>";
    } else {
        $status_icon = "<div class='mb-4'><i class='fa fa-times-circle text-danger' style='font-size: 80px;'></i></div>";
        $message = "<h2 class='fw-bold text-danger'>Booking Failed</h2>
                    <p class='text-muted'>Error saving booking.</p>";
    }
} else {
    $status_icon = "<div class='mb-4'><i class='fa fa-exclamation-triangle text-warning' style='font-size: 80px;'></i></div>";
    $message = "<h2 class='fw-bold text-warning'>Invalid Request</h2>";
}
?>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 60vh;">
    <div class="card shadow-lg text-center p-5" style="max-width: 500px; border-radius: 20px;">
        <div class="card-body">
            <?php echo $status_icon; ?>
            <?php echo $message; ?>
            <div class="mt-5 d-grid gap-2">
                <a href="student_dashboard.php" class="btn btn-primary btn-lg">View My Bookings</a>
                <a href="index.php" class="btn btn-outline-secondary">Return Home</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>