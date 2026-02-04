<?php 
include 'includes/header.php'; 
if ($_SESSION['role'] != 'landlord') header("Location: login.php");

// Logic for Adding Property (Keep existing logic)
if (isset($_POST['add_property'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $price = $conn->real_escape_string($_POST['price']);
    $desc = $conn->real_escape_string($_POST['desc']);
    $address = $conn->real_escape_string($_POST['address']);
    $landlord_id = $_SESSION['user_id'];
    
    $target_dir = "uploads/";
    $filename = time() . "_" . basename($_FILES['image']['name']); 
    $target_file = $target_dir . $filename;
    
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        $sql = "INSERT INTO properties (landlord_id, title, description, address, price, image_url) 
                VALUES ('$landlord_id', '$title', '$desc', '$address', '$price', '$target_file')";
        if($conn->query($sql)){
            echo "<div class='alert alert-success'>Property Added.</div>";
        }
    }
}
?>

<div class="container mt-4">
    <h2 class="mb-4">Landlord Dashboard</h2>
    
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="listings-tab" data-bs-toggle="tab" data-bs-target="#listings" type="button">My Listings</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="bookings-tab" data-bs-toggle="tab" data-bs-target="#bookings" type="button">Received Bookings</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="add-tab" data-bs-toggle="tab" data-bs-target="#add" type="button">Post New Property</button>
        </li>
    </ul>

    <div class="tab-content p-4 border border-top-0 bg-white shadow-sm" id="myTabContent">
        
        <div class="tab-pane fade show active" id="listings">
            <h4>Managed Properties</h4>
            <table class="table table-bordered">
                <thead><tr><th>Title</th><th>Price</th><th>Status</th><th>Approval</th></tr></thead>
                <tbody>
                    <?php
                    $lid = $_SESSION['user_id'];
                    $res = $conn->query("SELECT * FROM properties WHERE landlord_id='$lid'");
                    while($row = $res->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td>₦<?php echo number_format($row['price']); ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td><?php echo ($row['is_approved']) ? '<span class="badge bg-success">Approved</span>' : '<span class="badge bg-warning text-dark">Pending</span>'; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="tab-pane fade" id="bookings">
            <h4>Transaction History</h4>
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Student Name</th>
                        <th>Property</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // JOIN bookings with properties AND users (to get student name)
                    // WHERE the property belongs to THIS landlord
                    $sql_bookings = "SELECT bookings.*, properties.title, users.full_name 
                                     FROM bookings 
                                     JOIN properties ON bookings.property_id = properties.property_id 
                                     JOIN users ON bookings.student_id = users.user_id 
                                     WHERE properties.landlord_id = '$lid' 
                                     ORDER BY booking_date DESC";
                    
                    $res_bookings = $conn->query($sql_bookings);
                    
                    if ($res_bookings->num_rows > 0) {
                        while($book = $res_bookings->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($book['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($book['title']); ?></td>
                            <td class="text-success fw-bold">₦<?php echo number_format( ($book['amount'] > 0) ? $book['amount'] : $book['price'] ); ?></td>
                            <td><?php echo date('d M Y', strtotime($book['booking_date'])); ?></td>
                            <td>
                                <a href="receipt.php?ref=<?php echo $book['payment_reference']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fa fa-eye"></i> View Receipt
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; 
                    } else {
                        echo "<tr><td colspan='5' class='text-center'>No bookings received yet.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="tab-pane fade" id="add">
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Address</label>
                        <input type="text" name="address" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Price (₦)</label>
                        <input type="number" name="price" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Image</label>
                        <input type="file" name="image" class="form-control" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label>Description</label>
                        <textarea name="desc" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <button type="submit" name="add_property" class="btn btn-primary">Post Property</button>
            </form>
        </div>

    </div>
</div>
<?php include 'includes/footer.php'; ?>