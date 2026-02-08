<?php
require_once 'includes/header.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$filter_quiz_id = $_GET['quiz_id'] ?? 0;

// Fetch Quizzes for Filter
$quizzes = $pdo->query("SELECT id, title FROM quizzes WHERE status = 'active'")->fetchAll();

// Build Query
$sql = "SELECT 
            u.id as user_id,
            u.full_name,
            u.name as username,
            u.profile_photo,
            qa.score,
            qa.end_time,
            qa.quiz_id,
            q.title as quiz_title
        FROM quiz_attempts qa
        JOIN users u ON qa.user_id = u.id
        JOIN quizzes q ON qa.quiz_id = q.id
        WHERE qa.status = 'completed'";

$params = [];
if ($filter_quiz_id) {
    $sql .= " AND qa.quiz_id = ?";
    $params[] = $filter_quiz_id;
}

// We want the BEST attempt per user per quiz? Or just all attempts?
// Usually leaderboard shows the user's BEST score.
// If filter is OFF (Global), maybe sum of scores? Or just average?
// User request said: "ORDER BY percentage DESC". This implies individual attempts.
// Let's stick to simple "Top Attempts" list for now as per the query provided in prompt.
// "SELECT ... FROM quiz_attempts qa ..."

$sql .= " ORDER BY qa.score DESC, qa.end_time ASC LIMIT 50";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$leaderboard = $stmt->fetchAll();

// Find User's Rank (if not in top 50)
$user_rank = null;
$user_best_attempt = null;

// Check if user is in fetched leaderboard
foreach ($leaderboard as $k => $row) {
    if ($row['user_id'] == $user_id) {
        $user_rank = $k + 1;
        break;
    }
}

// If not found in top 50, query for it
if (!$user_rank) {
    // Get user's best attempt
    $uSql = "SELECT score, end_time FROM quiz_attempts WHERE user_id = ? AND status = 'completed'";
    if ($filter_quiz_id) {
        $uSql .= " AND quiz_id = ?";
        $uParams = [$user_id, $filter_quiz_id];
    } else {
        $uParams = [$user_id];
    }
    $uSql .= " ORDER BY score DESC, end_time ASC LIMIT 1";
    
    $uStmt = $pdo->prepare($uSql);
    $uStmt->execute($uParams);
    $myAttempt = $uStmt->fetch(); // user's best

    if ($myAttempt) {
        // Count how many people have better score
        $cSql = "SELECT COUNT(*) FROM quiz_attempts qa WHERE qa.status = 'completed' AND (qa.score > ? OR (qa.score = ? AND qa.end_time < ?))";
        $cParams = [$myAttempt['score'], $myAttempt['score'], $myAttempt['end_time']];
        
        if ($filter_quiz_id) {
            $cSql .= " AND qa.quiz_id = ?";
            $cParams[] = $filter_quiz_id;
        }
        
        $cStmt = $pdo->prepare($cSql);
        $cStmt->execute($cParams);
        $better_count = $cStmt->fetchColumn();
        $user_rank = $better_count + 1;
        
        $user_best_attempt = $myAttempt;
    }
}

?>

<!-- Header -->
<div class="row mb-5">
    <div class="col-12">
        <div class="card border-0 text-white overflow-hidden shadow-lg p-5" style="border-radius: 20px; background: linear-gradient(135deg, #4facfe, #00f2fe);">
            <div class="position-relative z-index-1 text-center">
                <h1 class="fw-bold mb-2">Leaderboard</h1>
                <p class="lead opacity-75 mb-4">See who's topping the charts!</p>
                
                <!-- Filter -->
                <form class="d-flex justify-content-center" action="" method="GET">
                    <div class="input-group" style="max-width: 400px;">
                        <span class="input-group-text bg-white border-0 text-primary fw-bold ps-3">
                            <i class="material-icons-round">filter_list</i>
                        </span>
                        <select name="quiz_id" class="form-select border-0 py-3 shadow-none" onchange="this.form.submit()" style="border-radius: 0 30px 30px 0; cursor: pointer;">
                            <option value="0">All Quizzes</option>
                            <?php foreach($quizzes as $q): ?>
                                <option value="<?php echo $q['id']; ?>" <?php echo $filter_quiz_id == $q['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($q['title']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
             <!-- Decorative Circles -->
            <div class="position-absolute top-0 start-0 translate-middle rounded-circle bg-white opacity-10" style="width: 300px; height: 300px;"></div>
            <div class="position-absolute bottom-0 end-0 translate-middle rounded-circle bg-white opacity-10" style="width: 200px; height: 200px;"></div>
        </div>
    </div>
</div>

<!-- Top 3 Podium -->
<?php if (count($leaderboard) > 0): ?>
<div class="row justify-content-center align-items-end mb-5 g-4">
    
    <!-- Rank 2 (Silver) -->
    <?php if (isset($leaderboard[1])): 
        $user2 = $leaderboard[1];
        $photo2 = !empty($user2['profile_photo']) ? '../uploads/profiles/'.$user2['profile_photo'] : 'https://ui-avatars.com/api/?name='.urlencode($user2['username']);
    ?>
    <div class="col-md-3 col-10 order-md-1 order-2">
        <div class="card border-0 shadow-lg text-center transform-hover" style="border-radius: 20px; border-top: 5px solid #C0C0C0;">
            <div class="card-body p-4">
                <div class="position-relative d-inline-block mb-3">
                    <img src="<?php echo htmlspecialchars($photo2); ?>" class="rounded-circle border border-3 border-light shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                    <div class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary border border-2 border-white" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; font-size: 1rem;">2</div>
                </div>
                <h5 class="fw-bold text-truncate mb-1"><?php echo htmlspecialchars($user2['full_name'] ?: $user2['username']); ?></h5>
                <p class="text-secondary small mb-2"><?php echo $filter_quiz_id ? htmlspecialchars($user2['quiz_title']) : 'Global Rank'; ?></p>
                <h3 class="fw-bold text-secondary mb-0"><?php echo number_format($user2['score'], 0); ?>%</h3>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Rank 1 (Gold) -->
    <?php if (isset($leaderboard[0])): 
        $user1 = $leaderboard[0];
        $photo1 = !empty($user1['profile_photo']) ? '../uploads/profiles/'.$user1['profile_photo'] : 'https://ui-avatars.com/api/?name='.urlencode($user1['username']);
    ?>
    <div class="col-md-4 col-11 order-md-2 order-1">
        <div class="card border-0 shadow-lg text-center transform-hover mb-4 mb-md-0" style="border-radius: 20px; border-top: 5px solid #FFD700; background: linear-gradient(to bottom, #fffdf0, #fff);">
            <div class="card-body p-5">
                <div class="mb-2">
                    <i class="material-icons-round text-warning" style="font-size: 3rem;">emoji_events</i>
                </div>
                <div class="position-relative d-inline-block mb-3">
                    <img src="<?php echo htmlspecialchars($photo1); ?>" class="rounded-circle border border-4 border-warning shadow" style="width: 100px; height: 100px; object-fit: cover;">
                    <div class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark border border-2 border-white" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; font-weight: bold;">1</div>
                </div>
                <h4 class="fw-bold text-truncate mb-1"><?php echo htmlspecialchars($user1['full_name'] ?: $user1['username']); ?></h4>
                <p class="text-secondary mb-3"><?php echo $filter_quiz_id ? htmlspecialchars($user1['quiz_title']) : 'Global Rank'; ?></p>
                <h2 class="fw-bold text-dark mb-0 display-6"><?php echo number_format($user1['score'], 0); ?>%</h2>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Rank 3 (Bronze) -->
     <?php if (isset($leaderboard[2])): 
        $user3 = $leaderboard[2];
        $photo3 = !empty($user3['profile_photo']) ? '../uploads/profiles/'.$user3['profile_photo'] : 'https://ui-avatars.com/api/?name='.urlencode($user3['username']);
    ?>
    <div class="col-md-3 col-10 order-md-3 order-3">
        <div class="card border-0 shadow-lg text-center transform-hover" style="border-radius: 20px; border-top: 5px solid #CD7F32;">
            <div class="card-body p-4">
                <div class="position-relative d-inline-block mb-3">
                    <img src="<?php echo htmlspecialchars($photo3); ?>" class="rounded-circle border border-3 border-light shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                    <div class="position-absolute top-0 start-100 translate-middle badge rounded-pill border border-2 border-white" style="background-color: #CD7F32; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; font-size: 1rem; color: white;">3</div>
                </div>
                <h5 class="fw-bold text-truncate mb-1"><?php echo htmlspecialchars($user3['full_name'] ?: $user3['username']); ?></h5>
                <p class="text-secondary small mb-2"><?php echo $filter_quiz_id ? htmlspecialchars($user3['quiz_title']) : 'Global Rank'; ?></p>
                <h3 class="fw-bold text-secondary mb-0" style="color: #CD7F32 !important;"><?php echo number_format($user3['score'], 0); ?>%</h3>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Your Rank Card (If not in top view or just as summary) -->
<?php if ($user_rank): ?>
<div class="row justify-content-center mb-5">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm bg-primary text-white" style="border-radius: 15px;">
            <div class="card-body d-flex align-items-center justify-content-between px-4 py-3">
                <div class="d-flex align-items-center">
                    <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: bold;">
                        #<?php echo $user_rank; ?>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Your Ranking</h6>
                        <small class="opacity-75"><?php echo $filter_quiz_id ? 'In this quiz' : 'Overall'; ?></small>
                    </div>
                </div>
                <div class="text-end">
                    <h4 class="mb-0 fw-bold"><?php echo isset($user_best_attempt) ? number_format($user_best_attempt['score'], 0) : number_format($leaderboard[array_search($user_id, array_column($leaderboard, 'user_id'))]['score'], 0); ?>%</h4>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Full Table -->
<div class="card border-0 shadow-lg mb-5" style="border-radius: 20px;">
    <div class="card-header bg-white border-bottom-0 py-4 px-4">
        <h5 class="fw-bold mb-0">Top Performers</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3 border-0" width="10%">Rank</th>
                        <th class="px-4 py-3 border-0" width="50%">Student</th>
                        <th class="px-4 py-3 border-0" width="20%">Quiz</th>
                        <th class="px-4 py-3 border-0 text-end" width="20%">Score</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leaderboard as $index => $row): 
                        $rank = $index + 1;
                        if ($rank <= 3) continue; // Skip top 3 as they are already shown (optional, but requested "Table for remaining ranks" - wait, actually "Remaining ranks: Show in a table". So yes skip 1-3).
                        
                        $isMe = ($row['user_id'] == $user_id);
                        $photo = !empty($row['profile_photo']) ? '../uploads/profiles/'.$row['profile_photo'] : 'https://ui-avatars.com/api/?name='.urlencode($row['username']);
                    ?>
                    <tr class="<?php echo $isMe ? 'table-success' : ''; ?>">
                        <td class="px-4 py-3 fw-bold text-secondary">#<?php echo $rank; ?></td>
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center">
                                <img src="<?php echo htmlspecialchars($photo); ?>" class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark"><?php echo htmlspecialchars($row['full_name'] ?: $row['username']); ?></h6>
                                    <small class="text-secondary"><?php echo date('M d, Y', strtotime($row['end_time'])); ?></small>
                                </div>
                                <?php if($isMe): ?>
                                    <span class="badge bg-success ms-2 rounded-pill px-2">YOU</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-secondary"><?php echo htmlspecialchars($row['quiz_title']); ?></td>
                        <td class="px-4 py-3 text-end">
                            <span class="badge bg-light text-dark border border-light rounded-pill px-3 py-2 fs-6">
                                <?php echo number_format($row['score'], 0); ?>%
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if (count($leaderboard) <= 3): ?>
                        <tr><td colspan="4" class="text-center py-4 text-secondary">No other rankings to display.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php else: ?>
    <!-- Empty State -->
    <div class="text-center py-5">
        <div class="mb-3">
             <i class="material-icons-round text-secondary opacity-25" style="font-size: 5rem;">emoji_events</i>
        </div>
        <h4 class="text-secondary">No records found yet.</h4>
        <p class="text-muted">Be the first to top the leaderboard!</p>
        <a href="quizzes.php" class="btn btn-primary rounded-pill px-4 mt-2">Take a Quiz</a>
    </div>
<?php endif; ?>

<style>
.transform-hover {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.transform-hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
}
.table-success {
    background-color: rgba(25, 135, 84, 0.1) !important;
}
</style>

<?php require_once 'includes/footer.php'; ?>
