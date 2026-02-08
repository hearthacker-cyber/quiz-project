<?php
require_once 'includes/header.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Change Password
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if (password_verify($current_password, $user['password'])) {
            if ($new_password === $confirm_password) {
                if (strlen($new_password) >= 6) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $update->execute([$hashed_password, $user_id]);
                    $success = "Password changed successfully.";
                } else {
                    $error = "New password must be at least 6 characters.";
                }
            } else {
                $error = "New passwords do not match.";
            }
        } else {
            $error = "Incorrect current password.";
        }
    }
    
    // Delete Account
    if (isset($_POST['delete_account'])) {
        $confirm_password_del = $_POST['confirm_password_del'];
        
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        
        if (password_verify($confirm_password_del, $user['password'])) {
            // Delete user - Cascading FKs should handle related data if set up correctly.
            // But let's be safe and manually delete if needed, though Requirements said "Use FK cascade if possible"
            // Assuming FKs are set to CASCADE as per typical setup, but if not:
            // DELETE FROM quiz_attempts WHERE user_id = $user_id
            // DELETE FROM user_answers WHERE user_id = $user_id
            
            $del = $pdo->prepare("DELETE FROM users WHERE id = ?");
            if ($del->execute([$user_id])) {
                session_destroy();
                redirect('user/login.php');
            } else {
                $error = "Failed to delete account.";
            }
        } else {
            $error = "Incorrect password. Account deletion cancelled.";
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <h2 class="mb-4 dashboard-title">Settings</h2>

        <?php if($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Change Password Card -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
            <div class="card-header bg-white border-bottom-0 py-3 px-4">
                <div class="d-flex align-items-center">
                    <i class="material-icons-round text-primary me-2">lock</i>
                    <h5 class="mb-0 fw-bold">Change Password</h5>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <form method="POST">
                    <input type="hidden" name="change_password" value="1">
                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-control" required minlength="6">
                        <div class="form-text">Minimum 6 characters</div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control" required minlength="6">
                    </div>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Update Password</button>
                </form>
            </div>
        </div>

        <!-- Preferences Card -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
             <div class="card-header bg-white border-bottom-0 py-3 px-4">
                <div class="d-flex align-items-center">
                    <i class="material-icons-round text-info me-2">notifications</i>
                    <h5 class="mb-0 fw-bold">Email Preferences</h5>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="updateSwitch" checked>
                    <label class="form-check-label" for="updateSwitch">Receive quiz updates and new features</label>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="offerSwitch" checked>
                    <label class="form-check-label" for="offerSwitch">Receive promotional offers</label>
                </div>
            </div>
        </div>

        <!-- Delete Account Card -->
        <div class="card border-0 shadow-sm border-danger" style="border-radius: 15px; border-left: 5px solid #dc3545 !important;">
             <div class="card-header bg-white border-bottom-0 py-3 px-4">
                <div class="d-flex align-items-center">
                    <i class="material-icons-round text-danger me-2">warning</i>
                    <h5 class="mb-0 fw-bold text-danger">Delete Account</h5>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <p class="text-secondary">This action is permanent and cannot be undone. All your progress, quiz attempts, and certificates will be permanently removed.</p>
                <button type="button" class="btn btn-outline-danger rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    DELETE MY ACCOUNT
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold text-danger">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete your account? Please enter your password to confirm.</p>
                <form method="POST" id="deleteForm">
                    <input type="hidden" name="delete_account" value="1">
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="confirm_password_del" class="form-control" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="deleteForm" class="btn btn-danger rounded-pill px-4">Delete Permanently</button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
