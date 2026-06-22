<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'landlord') {
    die("Unauthorized Access");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $landlord_id = $_SESSION['user_id'];
    $amount = (float) $_POST['amount'];

    // 1. Fetch current wallet balance to verify sufficient funds
    $query = $conn->query("SELECT wallet_balance, bank_name, account_number FROM users WHERE user_id = '$landlord_id'");
    $user = $query->fetch_assoc();

    // Security Check: Ensure they have set up bank details first
    if (empty($user['bank_name']) || empty($user['account_number'])) {
        echo "<script>alert('Please update your Bank Details before requesting a withdrawal.'); window.history.back();</script>";
        exit;
    }

    if ($amount > 0 && $amount <= $user['wallet_balance']) {
        // 2. Deduct amount from wallet
        $new_balance = $user['wallet_balance'] - $amount;
        $conn->query("UPDATE users SET wallet_balance = '$new_balance' WHERE user_id = '$landlord_id'");

        // 3. Record the withdrawal request
        $sql = "INSERT INTO withdrawals (landlord_id, amount, status) VALUES ('$landlord_id', '$amount', 'pending')";
        $conn->query($sql);

        echo "<script>
                alert('Withdrawal request of ₦" . number_format($amount) . " submitted successfully. It is pending admin approval.');
                window.location.href = 'landlord_dashboard.php';
              </script>";
    } else {
        echo "<script>alert('Invalid amount or insufficient funds.'); window.history.back();</script>";
    }
}
?>