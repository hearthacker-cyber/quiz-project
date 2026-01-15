<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../config/auth.php';

requireLogin();

// In a real production app, verify the transaction with PayPal API first!
// Here we rely on the client-side return for simplicity as requested, 
// BUT we should double check against PayPal normally.

$quiz_id = $_GET['quiz_id'] ?? 0;
$transaction_id = $_GET['transaction_id'] ?? '';
$amount = $_GET['amount'] ?? 0;
$status = $_GET['status'] ?? '';
$user_id = $_SESSION['user_id'];

if ($quiz_id && $transaction_id && $status === 'COMPLETED') {
    // 1. Record Payment
    $stmt = $pdo->prepare("INSERT INTO payments (user_id, quiz_id, transaction_id, amount, payment_status) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $quiz_id, $transaction_id, $amount, $status]);
    
    // 2. Grant Access
    $accessStmt = $pdo->prepare("INSERT INTO user_quiz_access (user_id, quiz_id) VALUES (?, ?)");
    $accessStmt->execute([$user_id, $quiz_id]);
    
    $_SESSION['success'] = "Purchase successful! You can now start the quiz.";
    redirect('user/dashboard.php');
} else {
    die("Payment Processing Failed or Invalid Request");
}
?>
