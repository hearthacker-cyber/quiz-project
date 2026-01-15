<?php
require_once 'includes/header.php';
requireLogin();

$quiz_id = $_GET['quiz_id'] ?? 0;
$user_id = $_SESSION['user_id'];

// Get latest attempt
$stmt = $pdo->prepare("SELECT * FROM quiz_attempts WHERE user_id = ? AND quiz_id = ? AND status = 'completed' ORDER BY end_time DESC LIMIT 1");
$stmt->execute([$user_id, $quiz_id]);
$attempt = $stmt->fetch();

if (!$attempt) redirect('user/dashboard.php');

// Get Quiz Info
$qStmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ?");
$qStmt->execute([$quiz_id]);
$quiz = $qStmt->fetch();

$pass = $attempt['score'] >= $quiz['pass_percentage'];
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <div class="card text-center shadow">
            <div class="card-header">Quiz Result</div>
            <div class="card-body">
                <h3 class="card-title"><?php echo htmlspecialchars($quiz['title']); ?></h3>
                
                <div class="my-4">
                    <?php if($pass): ?>
                        <i class="fas fa-check-circle text-success fa-5x mb-3"></i>
                        <h2 class="text-success">Passed</h2>
                    <?php else: ?>
                        <i class="fas fa-times-circle text-danger fa-5x mb-3"></i>
                        <h2 class="text-danger">Failed</h2>
                    <?php endif; ?>
                </div>
                
                <div class="row text-start">
                    <div class="col-6"><strong>Your Score:</strong></div>
                    <div class="col-6 text-end"><?php echo number_format($attempt['score'], 1); ?>%</div>
                    
                    <div class="col-6"><strong>Passing Score:</strong></div>
                    <div class="col-6 text-end"><?php echo $quiz['pass_percentage']; ?>%</div>
                    
                    <div class="col-6"><strong>Time Taken:</strong></div>
                    <div class="col-6 text-end">
                        <?php 
                        $start = new DateTime($attempt['start_time']);
                        $end = new DateTime($attempt['end_time']);
                        $diff = $start->diff($end);
                        echo $diff->format('%i min %s sec');
                        ?>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                    
                    <?php 
                    // Check for certificate
                    $certStmt = $pdo->prepare("SELECT certificate_code FROM certificates WHERE user_id = ? AND quiz_id = ?");
                    $certStmt->execute([$user_id, $quiz_id]);
                    $cert = $certStmt->fetch();
                    
                    if ($cert): 
                    ?>
                        <a href="certificate.php?code=<?php echo $cert['certificate_code']; ?>" target="_blank" class="btn btn-success"><i class="fas fa-certificate"></i> Download Certificate</a>
                    <?php endif; ?>
                    
                    <a href="quizzes.php" class="btn btn-outline-secondary">Browse More</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
