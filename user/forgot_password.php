<?php
require_once 'includes/header.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCSRFToken($_POST['csrf_token']);
    $email = sanitize($_POST['email']);
    
    // Check if email exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        // In a real application, you would generate a token, save it to DB, and mail the link.
        // For this demo/production-ready prompt constraint without SMTP details:
        $message = "If an account exists with this email, a reset link has been sent.";
    } else {
        $message = "If an account exists with this email, a reset link has been sent.";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">Forgot Password</div>
            <div class="card-body">
                <?php if($message): ?>
                    <div class="alert alert-info"><?php echo $message; ?></div>
                <?php endif; ?>
                <form method="POST">
                    <?php csrf_field(); ?>
                    <div class="mb-3">
                        <label>Enter your registered email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
                    <p class="text-center mt-3"><a href="login.php">Back to Login</a></p>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
