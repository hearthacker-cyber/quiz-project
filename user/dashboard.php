
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

<!-- Welcome Header -->
<div class="row mb-5">
    <div class="col-12">
        <div class="d-flex align-items-center mb-4">
            <div class="user-avatar-large me-4" style="width: 64px; height: 64px; border-radius: 50%; background: linear-gradient(135deg, #6200ee, #9d46ff); display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; font-weight: 500; box-shadow: 0 4px 12px rgba(98, 0, 238, 0.3);">
                <?php echo strtoupper(substr($_SESSION['username'] ?? 'L', 0, 1)); ?>
            </div>
            <div>
                <h1 class="mb-1">Welcome back, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Learner'); ?>!</h1>
                <p class="text-secondary mb-0">Continue your learning journey with these quizzes</p>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-5">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card elevation-2">
            <div class="card-body">
                <div class="icon-wrapper mb-3">
                    <i class="material-icons-round">book</i>
                </div>
                <h3 class="mb-1"><?php echo count($myQuizzes); ?></h3>
                <p class="text-secondary">Available Quizzes</p>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card elevation-2">
            <div class="card-body">
                <div class="icon-wrapper mb-3" style="background: linear-gradient(135deg, #00c853, #64dd17);">
                    <i class="material-icons-round">trending_up</i>
                </div>
                <h3 class="mb-1"><?php echo count($results); ?></h3>
                <p class="text-secondary">Quiz Attempts</p>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card elevation-2">
            <div class="card-body">
                <div class="icon-wrapper mb-3" style="background: linear-gradient(135deg, #ffab00, #ffd600);">
                    <i class="material-icons-round">star</i>
                </div>
                <?php
                $totalScore = 0;
                $completedCount = 0;
                foreach($results as $res) {
                    if($res['status'] == 'completed') {
                        $totalScore += $res['score'];
                        $completedCount++;
                    }
                }
                $avgScore = $completedCount > 0 ? round($totalScore / $completedCount, 1) : 0;
                ?>
                <h3 class="mb-1"><?php echo $avgScore; ?>%</h3>
                <p class="text-secondary">Average Score</p>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card elevation-2">
            <div class="card-body">
                <div class="icon-wrapper mb-3" style="background: linear-gradient(135deg, #dd2c00, #ff6e40);">
                    <i class="material-icons-round">emoji_events</i>
                </div>
                <?php
                $bestScore = 0;
                foreach($results as $res) {
                    if($res['status'] == 'completed' && $res['score'] > $bestScore) {
                        $bestScore = $res['score'];
                    }
                }
                ?>
                <h3 class="mb-1"><?php echo $bestScore; ?>%</h3>
                <p class="text-secondary">Best Score</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- My Quizzes Section -->
    <div class="col-lg-8 mb-4">
        <div class="card elevation-2">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <i class="material-icons-round me-2">quiz</i>
                    <span>My Quizzes (Purchased / Free)</span>
                </div>
                <a href="quizzes.php" class="btn btn-outline-primary btn-sm">
                    <i class="material-icons-round me-1" style="font-size: 18px;">add</i>
                    Browse More
                </a>
            </div>
            <div class="card-body">
                <?php if(count($myQuizzes) > 0): ?>
                    <div class="row">
                        <?php foreach($myQuizzes as $quiz): ?>
                        <div class="col-lg-6 col-md-12 mb-4">
                            <div class="card quiz-card elevation-1">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5 class="quiz-title mb-0"><?php echo htmlspecialchars($quiz['title']); ?></h5>
                                        <?php if($quiz['price'] > 0): ?>
                                            <span class="badge bg-success">
                                                <i class="material-icons-round me-1" style="font-size: 14px;">workspace_premium</i>
                                                Premium
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-info">
                                                <i class="material-icons-round me-1" style="font-size: 14px;">free_breakfast</i>
                                                Free
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="quiz-meta mb-3">
                                        <span class="badge" style="background: rgba(98, 0, 238, 0.1); color: #6200ee;">
                                            <i class="material-icons-round me-1" style="font-size: 14px;">category</i>
                                            <?php echo htmlspecialchars($quiz['category_name']); ?>
                                        </span>
                                    </div>
                                    
                                    <p class="quiz-description small text-secondary">
                                        <?php echo htmlspecialchars(substr($quiz['description'] ?? 'Test your knowledge with this quiz', 0, 100)); ?>...
                                    </p>
                                    
                                    <div class="quiz-footer">
                                        <div class="text-secondary small">
                                            <i class="material-icons-round me-1" style="font-size: 14px;">timer</i>
                                            <?php echo $quiz['time_limit']; ?> min
                                        </div>
                                        <a href="attempt.php?quiz_id=<?php echo $quiz['id']; ?>" class="btn btn-primary btn-sm">
                                            Start Quiz
                                            <i class="material-icons-round ms-1" style="font-size: 18px;">arrow_forward</i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="material-icons-round" style="font-size: 64px;">quiz</i>
                        </div>
                        <h4 class="mb-3">No Quizzes Yet</h4>
                        <p class="text-secondary mb-4">You haven't enrolled in any quizzes yet. Start your learning journey now!</p>
                        <a href="quizzes.php" class="btn btn-primary btn-lg">
                            <i class="material-icons-round me-2">explore</i>
                            Browse Quizzes
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Recent Results & Quick Actions -->
    <div class="col-lg-4">
        <!-- Recent Results -->
        <div class="card elevation-2 mb-4">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <i class="material-icons-round me-2">history</i>
                    <span>Recent Results</span>
                </div>
                <?php if(count($results) > 0): ?>
                <a href="results.php" class="btn btn-outline-primary btn-sm">View All</a>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <?php if(count($results) > 0): ?>
                <ul class="results-list">
                    <?php foreach($results as $res): 
                        $score_class = $res['score'] >= 70 ? 'text-success' : ($res['score'] >= 40 ? 'text-warning' : 'text-danger');
                    ?>
                    <li class="list-group-item d-flex align-items-center">
                        <div class="result-info flex-grow-1">
                            <div class="result-title"><?php echo htmlspecialchars($res['title']); ?></div>
                            <div class="result-date">
                                <i class="material-icons-round me-1" style="font-size: 14px;">calendar_today</i>
                                <?php echo date('M d, Y', strtotime($res['start_time'])); ?>
                            </div>
                            <div class="progress mt-2">
                                <div class="progress-bar" style="width: <?php echo $res['score']; ?>%;"></div>
                            </div>
                        </div>
                        <div class="score-badge ms-3">
                            <div class="<?php echo $score_class; ?>" style="font-weight: 600; font-size: 1.125rem;">
                                <?php echo $res['score']; ?>%
                            </div>
                            <div class="text-secondary small text-center">
                                <?php echo $res['status'] == 'completed' ? 'Completed' : 'Ongoing'; ?>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="material-icons-round text-secondary mb-3" style="font-size: 48px;">bar_chart</i>
                    <p class="text-secondary mb-0">No quiz attempts yet</p>
                    <p class="text-secondary small">Complete a quiz to see results here</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card elevation-2">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <i class="material-icons-round me-2">bolt</i>
                    <span>Quick Actions</span>
                </div>
            </div>
            <div class="card-body">
                <div class="quick-actions">
                    <a href="quizzes.php" class="btn">
                        <i class="material-icons-round">explore</i>
                        Browse Quizzes
                    </a>
                    <?php if(count($results) > 0): ?>
                    <a href="results.php" class="btn">
                        <i class="material-icons-round">assessment</i>
                        View All Results
                    </a>
                    <?php endif; ?>
                    <a href="leaderboard.php" class="btn">
                        <i class="material-icons-round">emoji_events</i>
                        Leaderboard
                    </a>
                    <a href="profile.php" class="btn">
                        <i class="material-icons-round">person</i>
                        My Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
