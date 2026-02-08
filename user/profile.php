<?php
require_once 'includes/header.php';
requireLogin();

$user_id = $_SESSION['user_id'];

// Fetch User Details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Fetch Stats
// 1. Quizzes Taken (Completed)
$stmt = $pdo->prepare("SELECT COUNT(*) FROM quiz_attempts WHERE user_id = ? AND status = 'completed'");
$stmt->execute([$user_id]);
$quizzes_taken = $stmt->fetchColumn();

// 2. Average Score
$stmt = $pdo->prepare("SELECT AVG(score) FROM quiz_attempts WHERE user_id = ? AND status = 'completed'");
$stmt->execute([$user_id]);
$avg_score = $stmt->fetchColumn() ?: 0;

// 3. Highest Score
$stmt = $pdo->prepare("SELECT MAX(score) FROM quiz_attempts WHERE user_id = ? AND status = 'completed'");
$stmt->execute([$user_id]);
$high_score = $stmt->fetchColumn() ?: 0;

// 4. Total Attempts
$stmt = $pdo->prepare("SELECT COUNT(*) FROM quiz_attempts WHERE user_id = ?");
$stmt->execute([$user_id]);
$total_attempts = $stmt->fetchColumn();

// Fetch Recent Activity (Limit 5)
$stmt = $pdo->prepare("
    SELECT qa.*, q.title as quiz_title 
    FROM quiz_attempts qa 
    JOIN quizzes q ON qa.quiz_id = q.id 
    WHERE qa.user_id = ? 
    ORDER BY qa.end_time DESC 
    LIMIT 5
");
$stmt->execute([$user_id]);
$recent_activity = $stmt->fetchAll();

// Profile Photo Logic
$profile_photo = !empty($user['profile_photo']) 
    ? '../uploads/profiles/' . $user['profile_photo'] 
    : 'https://ui-avatars.com/api/?name=' . urlencode($user['name']) . '&background=random&size=200';

?>

<!-- Profile Header Section -->
<div class="row mb-5">
    <div class="col-12">
        <div class="card border-0 text-white overflow-hidden shadow-lg" style="border-radius: 15px; background: linear-gradient(135deg, #667eea, #764ba2);">
            <div class="card-body p-4 p-md-5">
                <div class="row align-items-center">
                    <div class="col-md-auto text-center text-md-start mb-3 mb-md-0">
                        <img src="<?php echo htmlspecialchars($profile_photo); ?>" alt="Profile" class="rounded-circle border border-4 border-white shadow-sm" style="width: 120px; height: 120px; object-fit: cover;">
                    </div>
                    <div class="col-md text-center text-md-start">
                        <h2 class="fw-bold mb-1"><?php echo htmlspecialchars($user['full_name'] ?: $user['name']); ?></h2>
                        <p class="mb-2 opacity-75"><i class="material-icons-round align-middle fs-6 me-1">email</i> <?php echo htmlspecialchars($user['email']); ?></p>
                        <p class="mb-3 opacity-75"><i class="material-icons-round align-middle fs-6 me-1">calendar_today</i> Joined <?php echo date('M Y', strtotime($user['created_at'])); ?></p>
                        
                        <div class="d-flex gap-2 justify-content-center justify-content-md-start">
                            <a href="edit_profile.php" class="btn btn-light rounded-pill px-4 fw-bold text-primary shadow-sm">
                                <i class="material-icons-round align-middle me-1">edit</i> Edit Profile
                            </a>
                            <a href="settings.php" class="btn btn-outline-light rounded-pill px-4 fw-bold">
                                <i class="material-icons-round align-middle me-1">settings</i> Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Section -->
<div class="row g-4 mb-5">
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
            <div class="card-body text-center p-4">
                <div class="icon-box bg-primary-light text-primary mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="material-icons-round fs-3">assignment</i>
                </div>
                <h3 class="fw-bold mb-0"><?php echo $quizzes_taken; ?></h3>
                <p class="text-secondary small mb-0">Quizzes Taken</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
            <div class="card-body text-center p-4">
                <div class="icon-box bg-success-light text-success mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="material-icons-round fs-3">analytics</i>
                </div>
                <h3 class="fw-bold mb-0"><?php echo number_format($avg_score, 1); ?>%</h3>
                <p class="text-secondary small mb-0">Average Score</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
            <div class="card-body text-center p-4">
                <div class="icon-box bg-warning-light text-warning mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="material-icons-round fs-3">emoji_events</i>
                </div>
                <h3 class="fw-bold mb-0"><?php echo number_format($high_score, 1); ?>%</h3>
                <p class="text-secondary small mb-0">Highest Score</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
            <div class="card-body text-center p-4">
                <div class="icon-box bg-info-light text-info mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="material-icons-round fs-3">history</i>
                </div>
                <h3 class="fw-bold mb-0"><?php echo $total_attempts; ?></h3>
                <p class="text-secondary small mb-0">Total Attempts</p>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity Section -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-header bg-white border-bottom-0 py-3 px-4">
                <h5 class="fw-bold mb-0">Recent Activity</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 border-0">Quiz Name</th>
                                <th class="px-4 py-3 border-0">Score</th>
                                <th class="px-4 py-3 border-0">Date</th>
                                <th class="px-4 py-3 border-0 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($recent_activity) > 0): ?>
                                <?php foreach ($recent_activity as $attempt): ?>
                                    <tr>
                                        <td class="px-4 py-3 fw-medium"><?php echo htmlspecialchars($attempt['quiz_title']); ?></td>
                                        <td class="px-4 py-3">
                                            <?php if ($attempt['status'] == 'completed'): ?>
                                                <span class="badge <?php echo $attempt['score'] >= 50 ? 'bg-success-light text-success' : 'bg-danger-light text-danger'; ?>">
                                                    <?php echo number_format($attempt['score'], 1); ?>%
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-warning-light text-warning">Ongoing</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3 text-secondary"><?php echo date('M d, Y', strtotime($attempt['start_time'])); ?></td>
                                        <td class="px-4 py-3 text-end">
                                            <?php if ($attempt['status'] == 'completed'): ?>
                                                <a href="result.php?quiz_id=<?php echo $attempt['quiz_id']; ?>" class="btn btn-sm btn-outline-primary rounded-pill">View Result</a>
                                            <?php else: ?>
                                                <a href="attempt.php?quiz_id=<?php echo $attempt['quiz_id']; ?>" class="btn btn-sm btn-primary rounded-pill">Continue</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-secondary">
                                        <i class="material-icons-round fs-1 d-block mb-3 opacity-50">history_edu</i>
                                        No recent activity found.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Stats Colors */
.bg-primary-light { background-color: rgba(98, 0, 238, 0.1); }
.bg-success-light { background-color: rgba(0, 200, 83, 0.1); }
.bg-warning-light { background-color: rgba(255, 171, 0, 0.1); }
.bg-info-light { background-color: rgba(3, 218, 198, 0.1); }
.bg-danger-light { background-color: rgba(221, 44, 0, 0.1); }

.text-success { color: #00c853 !important; }
.text-danger { color: #dd2c00 !important; }

/* Table Styling */
.table > :not(caption) > * > * {
    border-bottom-color: rgba(0,0,0,0.05);
}
</style>

<?php require_once 'includes/footer.php'; ?>
