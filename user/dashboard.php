<?php
require_once 'includes/header.php';
requireLogin();

$user_id = $_SESSION['user_id'];

// Get Purchased/Free Quizzes (Available to User)
// Free quizzes are always available. Paid quizzes need an entry in user_quiz_access
$sql = "
    SELECT q.*, c.name as category_name
    FROM quizzes q
    JOIN categories c ON q.category_id = c.id
    WHERE q.status = 'active'
    AND (
        q.price = 0 
        OR q.id IN (SELECT quiz_id FROM user_quiz_access WHERE user_id = ?)
    )
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$myQuizzes = $stmt->fetchAll();

// Get Recent Results
$resStmt = $pdo->prepare("SELECT qa.*, q.title FROM quiz_attempts qa JOIN quizzes q ON qa.quiz_id = q.id WHERE qa.user_id = ? ORDER BY qa.start_time DESC LIMIT 5");
$resStmt->execute([$user_id]);
$results = $resStmt->fetchAll();
?>

<h2 class="mb-4">My Dashboard</h2>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">My Quizzes (Purchased / Free)</div>
            <div class="card-body">
                <?php if(count($myQuizzes) > 0): ?>
                    <div class="row">
                        <?php foreach($myQuizzes as $quiz): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($quiz['title']); ?></h5>
                                    <p class="card-text text-muted"><?php echo htmlspecialchars($quiz['category_name']); ?></p>
                                    <p class="card-text small"><?php echo htmlspecialchars(substr($quiz['description'], 0, 100)); ?>...</p>
                                    <a href="attempt.php?quiz_id=<?php echo $quiz['id']; ?>" class="btn btn-primary btn-sm">Start Quiz</a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>You haven't enrolled in any quizzes yet.</p>
                    <a href="quizzes.php" class="btn btn-outline-primary">Browse Quizzes</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Recent Results</div>
            <ul class="list-group list-group-flush">
                <?php foreach($results as $res): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong><?php echo htmlspecialchars($res['title']); ?></strong><br>
                        <small class="text-muted"><?php echo $res['start_time']; ?></small>
                    </div>
                    <span class="badge bg-<?php echo $res['status'] == 'completed' ? 'success' : 'warning'; ?>">
                        <?php echo $res['score']; ?>%
                    </span>
                </li>
                <?php endforeach; ?>
                <?php if(count($results) == 0): ?>
                    <li class="list-group-item">No attempts yet.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
