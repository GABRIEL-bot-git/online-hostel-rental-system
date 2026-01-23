<?php include 'includes/header.php'; ?>

<style>
    /* Hero Section with Background Image */
    .hero-section {
        background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1555854877-bab0e564b8d5?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80');
        background-size: cover;
        background-position: center;
        color: white;
        padding: 100px 0;
        margin-bottom: 40px;
        border-radius: 0 0 20px 20px;

    /* Search Input Styling */
    .search-input {
        height: 55px;
        border-radius: 30px 0 0 30px;
        border: none;
        padding-left: 25px;
        font-size: 1.1rem;
    }

    .search-btn {
        height: 55px;
        border-radius: 0 30px 30px 0;
        padding-left: 25px;
        padding-right: 25px;
        font-weight: bold;
        text-transform: uppercase;
    }

    /* Property Card Hover Effect */
    .property-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        border-radius: 15px;
        overflow: hidden;
    }
    
    .property-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important;
    }

    .card-img-top {
        height: 220px;
        object-fit: cover;
    }
    
    .price-badge {
        background-color: #28a745;
        color: white;
        padding: 5px 10px;
        border-radius: 5px;
        font-weight: bold;
    }
</style>

<div class="hero-section text-center">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3">Find Your Perfect Student Home</h1>
        <p class="lead mb-5">Secure, affordable, and comfortable hostels & off-campus lodges.</p>
        
        <div class="row justify-content-center">
            <div class="col-md-8">
                <form action="" method="GET" class="d-flex">
                    <input type="text" name="search" class="form-control search-input shadow-lg" 
                           placeholder="Search by location (e.g. Savti Lodge, School Gate, Osara Market)..." 
                           value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                    <button type="submit" class="btn btn-warning search-btn shadow-lg">Search</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark border-start border-4 border-primary ps-3">Available Accommodations</h3>
        
        <?php if(isset($_GET['search'])): ?>
            <a href="index.php" class="btn btn-outline-secondary btn-sm">Clear Search</a>
        <?php endif; ?>
    </div>

    <div class="row">
        <?php
        $search_query = "";
        if(isset($_GET['search'])) {
            $key = $conn->real_escape_string($_GET['search']);
            $search_query = " AND (address LIKE '%$key%' OR title LIKE '%$key%' OR description LIKE '%$key%')";
        }

        // Only show Approved and Available houses
        $sql = "SELECT * FROM properties WHERE status='available' AND is_approved=1 $search_query ORDER BY created_at DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
        ?>
        <div class="col-md-4 mb-4">
            <div class="card property-card shadow-sm h-100">
                <div class="position-relative">
                    <img src="<?php echo $row['image_url']; ?>" class="card-img-top" alt="House Image">
                    <div class="position-absolute top-0 end-0 m-3">
                         <span class="badge bg-primary">Verified</span>
                    </div>
                </div>

                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-bold text-dark"><?php echo $row['title']; ?></h5>
                    <p class="card-text text-muted small mb-3">
                        <i class="fa fa-map-marker-alt text-danger me-1"></i> <?php echo $row['address']; ?>
                    </p>
                    
                    <div class="mt-auto d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Price</span>
                            <h5 class="text-success mb-0 fw-bold">₦<?php echo number_format($row['price']); ?></h5>
                        </div>
                        <a href="property_details.php?id=<?php echo $row['property_id']; ?>" class="btn btn-outline-primary rounded-pill px-4">View</a>
                    </div>
                </div>
            </div>
        </div>
        <?php 
            } 
        } else {
            // Empty State UI
            echo "
            <div class='col-12 text-center py-5'>
                <div class='mb-3'>
                    <i class='fa fa-home fa-4x text-muted opacity-25'></i>
                </div>
                <h4 class='text-muted'>No accommodations found.</h4>
                <p class='text-muted'>Try adjusting your search terms or check back later.</p>
                <a href='index.php' class='btn btn-primary mt-2'>View All Listings</a>
            </div>";
        }
        ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>