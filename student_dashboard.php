<?php 
include 'includes/header.php';
if ($_SESSION['role'] != 'student') header("Location: login.php");
?>

<h2>My Housing History</h2>
<div class="card mt-3">
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Property</th>
                    <th>Address</th>
                    <th>Reference ID</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $uid = $_SESSION['user_id'];
                $sql = "SELECT bookings.*, properties.title, properties.address 
                        FROM bookings 
                        JOIN properties ON bookings.property_id = properties.property_id 
                        WHERE bookings.student_id = '$uid'";
                $result = $conn->query($sql);
                
                if($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['title']; ?></td>
                        <td><?php echo $row['address']; ?></td>
                        <td><small><?php echo $row['payment_reference']; ?></small></td>
                        <td><?php echo date('M d, Y', strtotime($row['booking_date'])); ?></td>
                        <td><span class="badge bg-success">Paid</span></td>
                    </tr>
                    <?php endwhile; 
                } else {
                    echo "<tr><td colspan='6' class='text-center'>No bookings found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'includes/footer.php'; ?>