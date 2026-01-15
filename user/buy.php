<?php
require_once 'includes/header.php';
requireLogin();

$quiz_id = $_GET['quiz_id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ?");
$stmt->execute([$quiz_id]);
$quiz = $stmt->fetch();

if (!$quiz || $quiz['price'] <= 0) {
    redirect('user/quizzes.php');
}

// Check if already bought
$accStmt = $pdo->prepare("SELECT * FROM user_quiz_access WHERE user_id = ? AND quiz_id = ?");
$accStmt->execute([$_SESSION['user_id'], $quiz_id]);
if ($accStmt->fetch()) {
    redirect('user/attempt.php?quiz_id=' . $quiz_id);
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card text-center shadow">
                <div class="card-header bg-primary text-white">Purchase Quiz</div>
                <div class="card-body">
                    <h4 class="card-title"><?php echo htmlspecialchars($quiz['title']); ?></h4>
                    <p class="card-text text-muted"><?php echo htmlspecialchars($quiz['description']); ?></p>
                    <hr>
                    <h2 class="text-primary mb-4">$<?php echo number_format($quiz['price'], 2); ?></h2>
                    
                    <!-- Alert for errors -->
                    <div id="payment-error" class="alert alert-danger d-none"></div>

                    <!-- PayPal Button Container -->
                    <div id="paypal-button-container"></div>
                    
                    <a href="quizzes.php" class="btn btn-link mt-3">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 
    PayPal SDK Script 
    - Using Client ID only (Secret is NEVER for frontend)
    - Explicitly setting currency=USD to avoid mismatch errors
-->
<script src="https://www.paypal.com/sdk/js?client-id=<?php echo PAYPAL_CLIENT_ID; ?>&currency=<?php echo CURRENCY; ?>"></script>

<script>
    console.log("Initializing PayPal Button...");

    paypal.Buttons({
        // Sets up the transaction when a payment button is clicked
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: '<?php echo $quiz['price']; ?>', // Amount must be a string or number
                        currency_code: '<?php echo CURRENCY; ?>'
                    },
                    description: 'Purchase Quiz: <?php echo htmlspecialchars($quiz['title']); ?>'
                }]
            });
        },
        
        // Finalize the transaction after payer approval
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(orderData) {
                console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));

                const transaction = orderData.purchase_units[0].payments.captures[0];
                
                if (transaction.status === 'COMPLETED') {
                    // Redirect to backend processing
                    window.location.href = "process_payment.php?quiz_id=<?php echo $quiz_id; ?>" + 
                                           "&transaction_id=" + transaction.id + 
                                           "&amount=" + transaction.amount.value + 
                                           "&status=" + transaction.status;
                } else {
                    document.getElementById('payment-error').innerText = "Payment not completed. Status: " + transaction.status;
                    document.getElementById('payment-error').classList.remove('d-none');
                }
            });
        },

        // Handle errors
        onError: function (err) {
            console.error('PayPal Error:', err);
            document.getElementById('payment-error').innerText = "Payment failed! Please try again or check console.";
            document.getElementById('payment-error').classList.remove('d-none');
        }
    }).render('#paypal-button-container');
</script>

<?php require_once 'includes/footer.php'; ?>
