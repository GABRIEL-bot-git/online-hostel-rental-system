<?php 
include 'includes/header.php'; // This should include your db_connect and session_start

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'landlord') {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}

$lid = $_SESSION['user_id'];

// Handle Adding Property
if (isset($_POST['add_property'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $price = (float)$_POST['price'];
    $desc = $conn->real_escape_string($_POST['desc']);
    $address = $conn->real_escape_string($_POST['address']);
    
    // Create uploads directory if it doesn't exist
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $filename = time() . "_" . basename($_FILES['image']['name']); 
    $target_file = $target_dir . $filename;
    
    // Get Coordinates
    $lat = $conn->real_escape_string($_POST['latitude']);
    $lng = $conn->real_escape_string($_POST['longitude']);
    
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        $sql = "INSERT INTO properties (landlord_id, title, description, address, price, image_url, latitude, longitude) 
                VALUES ('$lid', '$title', '$desc', '$address', '$price', '$target_file', '$lat', '$lng')";
        // ... rest of the code
        if($conn->query($sql)){
            echo "<script>alert('Property Added Successfully!'); window.location.href='landlord_dashboard.php';</script>";
        }
    }
}

// Fetch Landlord Info for Wallet/KYC
$l_info = $conn->query("SELECT * FROM users WHERE user_id='$lid'")->fetch_assoc();
?>

<div class="container mt-4 mb-5">
    <h2 class="mb-4">Landlord Dashboard</h2>
    
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#wallet" type="button">Wallet & Profile</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#listings" type="button">My Listings</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#bookings" type="button">Transaction History</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#add" type="button">Post New Property</button>
        </li>
    </ul>

    <div class="tab-content p-4 border border-top-0 bg-white shadow-sm" id="myTabContent">
        
        <div class="tab-pane fade show active" id="wallet">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card bg-success text-white shadow h-100">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <h5>Total Balance</h5>
                            <h2 class="fw-bold">₦<?php echo number_format($l_info['wallet_balance'], 2); ?></h2>
                            <div class="mt-3">
                                <button class="btn btn-light w-100 fw-bold text-success" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                                    <i class="fa fa-money-bill-wave"></i> Request Withdrawal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title border-bottom pb-2 mb-3">Update KYC & Bank Details</h5>
                            <form action="update_landlord_profile.php" method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted small">Phone Number</label>
                                        <input type="text" name="phone" value="<?php echo htmlspecialchars($l_info['phone'] ?? ''); ?>" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted small">NIN</label>
                                        <input type="text" name="nin" value="<?php echo htmlspecialchars($l_info['nin'] ?? ''); ?>" class="form-control bg-light" maxlength="11" readonly>
                                        <small class="text-danger" style="font-size: 0.75rem;">NIN cannot be changed after registration.</small>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label text-muted small">Bank Name</label>
                                        <input type="text" name="bank_name" value="<?php echo htmlspecialchars($l_info['bank_name'] ?? ''); ?>" placeholder="e.g. First Bank, GTB" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted small">Account Number</label>
                                        <input type="text" name="account_number" value="<?php echo htmlspecialchars($l_info['account_number'] ?? ''); ?>" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted small">Account Name</label>
                                        <input type="text" name="account_name" value="<?php echo htmlspecialchars($l_info['account_name'] ?? ''); ?>" class="form-control" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-dark">Save Details</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="listings">
            <h4 class="mb-3">Managed Properties</h4>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light"><tr><th>Title</th><th>Price</th><th>Status</th><th>Approval</th></tr></thead>
                    <tbody>
                        <?php
                        $res = $conn->query("SELECT * FROM properties WHERE landlord_id='$lid' ORDER BY date_listed DESC");
                        if($res->num_rows > 0) {
                            while($row = $res->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td>₦<?php echo number_format($row['price']); ?></td>
                                <td>
                                    <?php if($row['status'] == 'available'): ?>
                                        <span class="badge bg-success">Available</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Taken</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo ($row['is_approved']) ? '<span class="badge bg-primary">Approved</span>' : '<span class="badge bg-warning text-dark">Pending Admin</span>'; ?>
                                </td>
                            </tr>
                            <?php endwhile; 
                        } else {
                            echo "<tr><td colspan='4' class='text-center py-4'>You haven't posted any properties yet.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="tab-pane fade" id="bookings">
            <h4 class="mb-3">Received Bookings</h4>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Student Name</th>
                            <th>Property</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
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
                                <td class="text-success fw-bold">₦<?php echo number_format($book['amount']); ?></td>
                                <td><?php echo date('d M Y', strtotime($book['booking_date'])); ?></td>
                                <td>
                                    <?php 
                                        if($book['booking_status'] == 'refund_requested') echo '<span class="badge bg-warning text-dark">Refund Pending</span>';
                                        elseif($book['booking_status'] == 'refunded') echo '<span class="badge bg-danger">Refunded</span>';
                                        else echo '<span class="badge bg-success">Confirmed</span>';
                                    ?>
                                </td>
                                <td>
                                    <a href="receipt.php?ref=<?php echo $book['payment_reference']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fa fa-receipt"></i> Receipt
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; 
                        } else {
                            echo "<tr><td colspan='6' class='text-center py-4'>No bookings received yet.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <h4 class="mt-5 mb-3 border-bottom pb-2">My Withdrawal Requests</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light"><tr><th>Date</th><th>Amount</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php
                            $w_res = $conn->query("SELECT * FROM withdrawals WHERE landlord_id='$lid' ORDER BY request_date DESC");
                            if($w_res->num_rows > 0) {
                                while($w = $w_res->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo date('d M Y, h:i A', strtotime($w['request_date'])); ?></td>
                                    <td class="fw-bold">₦<?php echo number_format($w['amount']); ?></td>
                                    <td>
                                        <?php 
                                        if($w['status'] == 'pending') echo '<span class="badge bg-warning text-dark">Pending</span>';
                                        elseif($w['status'] == 'approved' || $w['status'] == 'paid') echo '<span class="badge bg-success">Paid</span>';
                                        ?>
                                    </td>
                                </tr>
                                <?php endwhile; 
                            } else { echo "<tr><td colspan='3' class='text-center'>No withdrawals requested yet.</td></tr>"; }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="add">
            <h4 class="mb-3">Post a New Property</h4>
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Spacious Self-Contain in Osara" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Full Address</label>
                        <input type="text" name="address" class="form-control" placeholder="e.g. 12 University Road, Lokoja" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Price (₦) per session</label>
                        <input type="number" name="price" class="form-control" placeholder="e.g. 150000" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Property Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="desc" class="form-control" rows="3" placeholder="Describe the facilities (e.g., Water, Security)..." required></textarea>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Pinpoint Location on Map</label>
                    <p class="text-muted small mb-1">Click on the map to set the exact location of your property.</p>
                    <div id="map" style="height: 300px; width: 100%; border-radius: 8px; border: 1px solid #ccc;"></div>
                    <input type="hidden" name="latitude" id="lat" required>
                    <input type="hidden" name="longitude" id="lng" required>
                </div>

                <script>
                    // Initialize Leaflet Map (Centered on Lokoja/Osara region)
                    var map = L.map('map').setView([7.7969, 6.7333], 12); 
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(map);

                    var marker;
                    map.on('click', function(e) {
                        var lat = e.latlng.lat;
                        var lng = e.latlng.lng;
                        
                        if (marker) {
                            map.removeLayer(marker);
                        }
                        marker = L.marker([lat, lng]).addTo(map);
                        
                        document.getElementById('lat').value = lat;
                        document.getElementById('lng').value = lng;
                    });
                </script>
                <button type="submit" name="add_property" class="btn btn-primary">Post Property</button>
            </form>
        </div>

    </div>
</div>

<div class="modal fade" id="withdrawModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="process_withdrawal.php" method="POST">
          <div class="modal-header">
              <h5 class="modal-title">Request Withdrawal</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
              <?php if(empty($l_info['bank_name']) || empty($l_info['account_number'])): ?>
                  <div class="alert alert-warning">
                      You must update your Bank Details before you can withdraw funds.
                  </div>
              <?php else: ?>
                  <p class="mb-2">Available Balance: <strong>₦<?php echo number_format($l_info['wallet_balance'], 2); ?></strong></p>
                  <div class="bg-light p-3 rounded border mb-3">
                      <small class="text-muted d-block">Sending to:</small>
                      <strong><?php echo htmlspecialchars($l_info['bank_name']); ?></strong><br>
                      <?php echo htmlspecialchars($l_info['account_number']); ?> - <?php echo htmlspecialchars($l_info['account_name']); ?>
                  </div>
                  <label class="form-label fw-bold">Amount to Withdraw (₦)</label>
                  <input type="number" name="amount" class="form-control" max="<?php echo $l_info['wallet_balance']; ?>" min="1000" placeholder="Min: 1,000" required>
              <?php endif; ?>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <?php if(!empty($l_info['bank_name']) && !empty($l_info['account_number'])): ?>
                  <button type="submit" class="btn btn-success">Submit Request</button>
              <?php endif; ?>
          </div>
      </form>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>