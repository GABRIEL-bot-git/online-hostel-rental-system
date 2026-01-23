<?php 
include 'includes/header.php';

// 1. SECURITY: Validate Input
// Force the ID to be an integer. If malicious code is sent, it becomes 0.
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 2. SECURITY: Prepared Statement
// We use ? as a placeholder instead of putting the variable directly in the query.
$stmt = $conn->prepare("SELECT * FROM properties WHERE property_id = ?");
$stmt->bind_param("i", $id); // "i" means we expect an Integer
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// 3. Check if property exists
if (!$row) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Property not found or has been removed. <a href='index.php'>Go Back</a></div></div>";
    include 'includes/footer.php';
    exit(); // Stop loading the rest of the page
}
?>

<div class="row mt-4">
    <div class="col-md-6">
        <img src="<?php echo htmlspecialchars($row['image_url']); ?>" class="img-fluid rounded shadow" alt="House">
    </div>
    <div class="col-md-6">
        <h2><?php echo htmlspecialchars($row['title']); ?></h2>
        <h3 class="text-success">₦<?php echo number_format($row['price']); ?></h3>
        
        <p class="mt-3"><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($row['address']); ?></p>
        
        <?php if(isset($_SESSION['user_id'])): ?>
            <form id="paymentForm">
                <input type="hidden" id="email-address" value="<?php echo $_SESSION['email']; ?>">
                <input type="hidden" id="amount" value="<?php echo $row['price']; ?>">
                
                <button type="submit" onclick="payWithPaystack(event)" class="btn btn-success btn-lg w-100 mt-3">
                    <i class="fa fa-lock"></i> Pay Securely Now
                </button>
            </form>
        <?php else: ?>
            <div class="alert alert-warning">
                Please <a href="login.php" class="fw-bold">Login</a> to book this hostel.
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
    function payWithPaystack(e) {
        e.preventDefault();
        
        let email = document.getElementById("email-address").value;
        let amount = document.getElementById("amount").value;
        
        let publicKey = 'pk_test_74c53426fbaa8651cbaf92bb4ed1caa8ad9df1b3'; 

        let handler = PaystackPop.setup({
            key: publicKey, 
            email: email,
            amount: amount * 100, // Paystack counts in Kobo (NGN * 100)
            currency: 'NGN',
            ref: ''+Math.floor((Math.random() * 1000000000) + 1), // Generate random ref
            
            onClose: function(){ 
                alert('Transaction cancelled.'); 
            },
            
            callback: function(response){
                // Redirect to verification page
                // Note: We use the PHP $id variable here, which is safe because we cast it to (int) at the top
                window.location = "verify_transaction.php?reference=" + response.reference + "&prop_id=<?php echo $id; ?>";
            }
        });
        handler.openIframe();
    }
</script>

<?php include 'includes/footer.php'; ?>