<?php 
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) header("Location: login.php");

$id = $_GET['id'];
$prop = $conn->query("SELECT * FROM properties WHERE property_id='$id'")->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Complete Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <div class="card p-5 mx-auto" style="max-width: 500px;">
        <h3 class="text-center">Confirm Booking</h3>
        <p><b>Property:</b> <?php echo $prop['title']; ?></p>
        <p><b>Price:</b> ₦<?php echo number_format($prop['price']); ?></p>
        
        <form id="paymentForm">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email-address" class="form-control" value="<?php echo $_SESSION['email'] ?? ''; ?>" required />
            </div>
            <div class="form-submit mt-3">
                <button type="submit" class="btn btn-success w-100" onclick="payWithPaystack()"> Pay ₦<?php echo number_format($prop['price']); ?> </button>
            </div>
        </form>
    </div>

    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script>
        const paymentForm = document.getElementById('paymentForm');
        paymentForm.addEventListener("submit", payWithPaystack, false);

        function payWithPaystack(e) {
            e.preventDefault();
            let handler = PaystackPop.setup({
                key: 'pk_test_xxxxxxxxxxxxxxxxxxxxxxxx', // REPLACE WITH YOUR PUBLIC KEY
                email: document.getElementById("email-address").value,
                amount: <?php echo $prop['price']; ?> * 100, // Amount in kobo
                currency: 'NGN',
                ref: ''+Math.floor((Math.random() * 1000000000) + 1),
                onClose: function(){
                    alert('Window closed.');
                },
                callback: function(response){
                    // Payment successful! Send reference to verify_transaction.php
                    window.location.href = "verify_transaction.php?reference=" + response.reference + "&prop_id=<?php echo $id; ?>";
                }
            });
            handler.openIframe();
        }
    </script>
</body>
</html>