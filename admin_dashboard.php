<?php 
include 'includes/header.php';
if ($_SESSION['role'] != 'admin') header("Location: login.php");

// Handle Approval
if (isset($_GET['approve'])) {
    $id = $_GET['approve'];
    $conn->query("UPDATE properties SET is_approved=1 WHERE property_id=$id");
    echo "<script>window.location='admin_dashboard.php';</script>";
}

// Handle Deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM properties WHERE property_id=$id");
    echo "<script>window.location='admin_dashboard.php';</script>";
}
?>

<h2 class="mb-4">Admin Dashboard</h2>

<div class="card mb-4">
    <div class="card-header bg-warning text-dark">Pending Property Approvals</div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Price</th>
                    <th>Landlord</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT properties.*, users.full_name FROM properties 
                        JOIN users ON properties.landlord_id = users.user_id 
                        WHERE is_approved=0";
                $result = $conn->query($sql);
                while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['title']; ?></td>
                    <td>₦<?php echo number_format($row['price']); ?></td>
                    <td><?php echo $row['full_name']; ?></td>
                    <td>
                        <a href="?approve=<?php echo $row['property_id']; ?>" class="btn btn-success btn-sm">Approve</a>
                        <a href="?delete=<?php echo $row['property_id']; ?>" class="btn btn-danger btn-sm">Reject</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>