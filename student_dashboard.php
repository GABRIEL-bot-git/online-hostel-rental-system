<?php 
include 'includes/header.php';
if ($_SESSION['role'] != 'student') header("Location: login.php");
?>

<div class="container mt-4">
    <h2>My Booking History</h2>
    <div class="card mt-3 shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Property</th>
                        <th>Address</th>
                        <th>Reference ID</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Action</th> </tr>
                </thead>
                <tbody>
                    <?php
                    $uid = $_SESSION['user_id'];
                    // Query to get booking + property details
                    $sql = "SELECT bookings.*, properties.title, properties.address 
                            FROM bookings 
                            JOIN properties ON bookings.property_id = properties.property_id 
                            WHERE bookings.student_id = '$uid' 
                            ORDER BY booking_date DESC";
                    $result = $conn->query($sql);
                    
                    if($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['address']); ?></td>
                            <td><span class="badge bg-secondary"><?php echo $row['payment_reference']; ?></span></td>
                            <td><?php echo date('M d, Y', strtotime($row['booking_date'])); ?></td>
                            <td class="fw-bold text-success">₦<?php echo number_format( ($row['amount'] > 0) ? $row['amount'] : $row['price'] ); ?></td>
                            <td>
                                <a href="receipt.php?ref=<?php echo $row['payment_reference']; ?>" target="_blank" class="btn btn-primary btn-sm">
                                    <i class="fa fa-download"></i> Receipt
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; 
                    } else {
                        echo "<tr><td colspan='6' class='text-center py-4'>No bookings found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>