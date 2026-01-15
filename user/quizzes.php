<?php
require_once 'includes/header.php';

// Get current user access if logged in
$user_access_ids = [];
if (isLoggedIn()) {
    $stmt = $pdo->prepare("SELECT quiz_id FROM user_quiz_access WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user_access_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Fetch all active quizzes
$quizzes = $pdo->query("SELECT q.*, c.name as category_name FROM quizzes q JOIN categories c ON q.category_id = c.id WHERE q.status = 'active' ORDER BY q.id DESC")->fetchAll();
?>

<h2 class="mb-4">All Quizzes</h2>

<div class="row">
    <?php foreach($quizzes as $quiz): ?>
    <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body d-flex flex-column">
                <div class="mb-2">
                    <span class="badge bg-info text-dark"><?php echo htmlspecialchars($quiz['category_name']); ?></span>
                    <?php if($quiz['price'] > 0): ?>
                        <span class="badge bg-warning text-dark float-end">$<?php echo $quiz['price']; ?></span>
                    <?php else: ?>
                        <span class="badge bg-success float-end">Free</span>
                    <?php endif; ?>
                </div>
                <h5 class="card-title"><?php echo htmlspecialchars($quiz['title']); ?></h5>
                <p class="card-text text-muted flex-grow-1"><?php echo htmlspecialchars(substr($quiz['description'], 0, 120)); ?></p>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small class="text-muted"><i class="far fa-clock"></i> <?php echo $quiz['time_limit']; ?> mins</small>
                    
                    <?php if(!isLoggedIn()): ?>
                        <a href="login.php" class="btn btn-primary">Login to Access</a>
                    <?php elseif($quiz['price'] == 0 || in_array($quiz['id'], $user_access_ids)): ?>
                        <a href="attempt.php?quiz_id=<?php echo $quiz['id']; ?>" class="btn btn-success">Start Quiz</a>
                    <?php else: ?>
                        <!-- PayPal Button Container -->
                         <a href="buy.php?quiz_id=<?php echo $quiz['id']; ?>" class="btn btn-warning">Buy Now</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
