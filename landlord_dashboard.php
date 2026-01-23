<?php 
include 'includes/header.php'; 
if ($_SESSION['role'] != 'landlord') header("Location: login.php");

if (isset($_POST['add_property'])) {
    // FIX: We use real_escape_string() to handle apostrophes and special characters
    $title = $conn->real_escape_string($_POST['title']);
    $price = $conn->real_escape_string($_POST['price']);
    $desc = $conn->real_escape_string($_POST['desc']);
    $address = $conn->real_escape_string($_POST['address']);
    $landlord_id = $_SESSION['user_id'];
    
    // Image Upload Logic
    $target_dir = "uploads/";
    // Ensure unique filename to prevent overwriting
    $filename = time() . "_" . basename($_FILES['image']['name']); 
    $target_file = $target_dir . $filename;
    
    // Check if image upload is successful
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        
        $sql = "INSERT INTO properties (landlord_id, title, description, address, price, image_url) 
                VALUES ('$landlord_id', '$title', '$desc', '$address', '$price', '$target_file')";
        
        if($conn->query($sql)){
            echo "<div class='alert alert-success'>Property Added. Waiting for Admin Approval.</div>";
        } else {
            echo "<div class='alert alert-danger'>Database Error: " . $conn->error . "</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Failed to upload image. Make sure the 'uploads' folder exists.</div>";
    }
}
?>

<div class="container mt-3">
    <h2>Landlord Dashboard</h2>
    
    <div class="card p-4 mb-5 shadow-sm">
        <h4>Post New Accommodation</h4>
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <input type="text" name="title" class="form-control" placeholder="Property Title (e.g. Self Con at Osara market)" required>
                </div>
                <div class="col-md-6 mb-2">
                    <input type="text" name="address" class="form-control" placeholder="Full Address" required>
                </div>
                <div class="col-md-6 mb-2">
                    <input type="number" name="price" class="form-control" placeholder="Price per year (NGN)" required>
                </div>
                <div class="col-md-6 mb-2">
                    <input type="file" name="image" class="form-control" required>
                </div>
                <div class="col-12 mb-2">
                    <textarea name="desc" class="form-control" placeholder="Description (e.g. It's fully furnished...)" required></textarea>
                </div>
            </div>
            <button type="submit" name="add_property" class="btn btn-primary">Post Property</button>
        </form>
    </div>

    <h4>My Listings</h4>
    <table class="table table-bordered bg-white">
        <thead>
            <tr><th>Title</th><th>Price</th><th>Status</th><th>Approval</th></tr>
        </thead>
        <tbody>
            <?php
            $lid = $_SESSION['user_id'];
            $res = $conn->query("SELECT * FROM properties WHERE landlord_id='$lid'");
            while($row = $res->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['title']; ?></td>
                <td>₦<?php echo number_format($row['price']); ?></td>
                <td><?php echo $row['status']; ?></td>
                <td>
                    <?php echo ($row['is_approved']) ? '<span class="badge bg-success">Approved</span>' : '<span class="badge bg-warning text-dark">Pending</span>'; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php include 'includes/footer.php'; ?>