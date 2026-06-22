<?php
// Ensure sessions are handled safely

include 'includes/header.php'; // Includes db_connect

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}

$student_id = $_SESSION['user_id'];
?>

<div class="container mt-5 mb-5" style="min-height: 60vh;">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="fw-bold text-primary">Student Dashboard</h2>
            <p class="text-muted">Manage your accommodation bookings and payment receipts.</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="index.php" class="btn btn-outline-primary shadow-sm"><i class="fa fa-search"></i> Find a New Hostel</a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h4 class="card-title border-bottom pb-3 mb-4">My Booking History</h4>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Property</th>
                            <th>Reference ID</th>
                            <th>Amount Paid</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch the student's bookings
                        $sql = "SELECT b.*, p.title 
                                FROM bookings b 
                                JOIN properties p ON b.property_id = p.property_id 
                                WHERE b.student_id = '$student_id' 
                                ORDER BY b.booking_date DESC";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                ?>
                                <tr>
                                    <td class="fw-bold text-dark"><?php echo htmlspecialchars($row['title']); ?></td>
                                    <td><span class="text-muted font-monospace"><?php echo $row['payment_reference']; ?></span></td>
                                    <td class="text-success fw-bold">₦<?php echo number_format($row['amount'], 2); ?></td>
                                    <td><?php echo date('d M Y, h:i A', strtotime($row['booking_date'])); ?></td>
                                    <td>
                                        <?php 
                                            if ($row['booking_status'] == 'confirmed') {
                                                echo '<span class="badge bg-success">Confirmed</span>';
                                            } elseif ($row['booking_status'] == 'refund_requested') {
                                                echo '<span class="badge bg-warning text-dark">Refund Pending</span>';
                                            } elseif ($row['booking_status'] == 'refunded') {
                                                echo '<span class="badge bg-danger">Refunded</span>';
                                            } else {
                                                echo '<span class="badge bg-secondary">' . ucfirst($row['booking_status']) . '</span>';
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="receipt.php?ref=<?php echo $row['payment_reference']; ?>" target="_blank" class="btn btn-sm btn-primary shadow-sm">
                                                <i class="fa fa-print"></i> Receipt
                                            </a>
                                            
                                            <!-- Refund Logic UI -->
                                            <?php if($row['booking_status'] == 'confirmed'): ?>
    <!-- Button triggers the Modal -->
    <button type="button" class="btn btn-sm btn-outline-danger shadow-sm" data-bs-toggle="modal" data-bs-target="#refundModal<?php echo $row['booking_id']; ?>">
        Request Refund
    </button>

                                                <!-- Refund Modal -->
                                                <div class="modal fade" id="refundModal<?php echo $row['booking_id']; ?>" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form action="process_refund.php" method="POST">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Request Refund</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body text-start">
                                                                    <p>Property: <strong><?php echo htmlspecialchars($row['title']); ?></strong></p>
                                                                    <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-bold">Reason for Refund</label>
                                                                        <textarea name="reason" class="form-control" rows="3" required placeholder="State your reason clearly..."></textarea>
                                                                    </div>
                                                                    <small class="text-danger">Note: Refunds are subject to Admin approval.</small>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="submit" class="btn btn-danger">Submit Request</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center py-5 text-muted'>You have no bookings yet. <br><a href='index.php' class='btn btn-primary mt-3'>Browse Hostels</a></td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>