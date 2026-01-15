<?php
require_once 'includes/header.php';
requireLogin();

$quiz_id = $_GET['quiz_id'] ?? 0;
$user_id = $_SESSION['user_id'];

// 1. Verify Quiz Existence and Access
$quizStmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ? AND status = 'active'");
$quizStmt->execute([$quiz_id]);
$quiz = $quizStmt->fetch();

if (!$quiz) die("Quiz not found.");

// Check if paid and bought
if ($quiz['price'] > 0) {
    $accStmt = $pdo->prepare("SELECT * FROM user_quiz_access WHERE user_id = ? AND quiz_id = ?");
    $accStmt->execute([$user_id, $quiz_id]);
    if (!$accStmt->fetch()) {
        redirect('user/buy.php?quiz_id=' . $quiz_id);
    }
}

// 2. Manage Attempt Session
// Count previous completed attempts
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM quiz_attempts WHERE user_id = ? AND quiz_id = ? AND status = 'completed'");
$countStmt->execute([$user_id, $quiz_id]);
$attemptCount = $countStmt->fetchColumn();

// Check Limit
if ($quiz['max_attempts'] > 0 && $attemptCount >= $quiz['max_attempts']) {
    $_SESSION['error'] = "Maximum attempts reached for this quiz.";
    redirect('user/dashboard.php');
}

// Check for ongoing attempt
$attStmt = $pdo->prepare("SELECT * FROM quiz_attempts WHERE user_id = ? AND quiz_id = ? AND status = 'ongoing'");
$attStmt->execute([$user_id, $quiz_id]);
$attempt = $attStmt->fetch();

if (!$attempt) {
    // Start new attempt
    $start_time = date('Y-m-d H:i:s');
    $stmt = $pdo->prepare("INSERT INTO quiz_attempts (user_id, quiz_id, start_time, status, attempt_number) VALUES (?, ?, ?, 'ongoing', ?)");
    $stmt->execute([$user_id, $quiz_id, $start_time, $attemptCount + 1]);
    $attempt_id = $pdo->lastInsertId();
    $attempt = ['id' => $attempt_id, 'start_time' => $start_time];
}

// 3. Fetch Questions
$qStmt = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ?");
$qStmt->execute([$quiz_id]);
$questions = $qStmt->fetchAll();

// Fetch Options
$optionsMap = [];
$optStmt = $pdo->prepare("SELECT * FROM options WHERE question_id IN (SELECT id FROM questions WHERE quiz_id = ?)");
$optStmt->execute([$quiz_id]);
while($row = $optStmt->fetch()) {
    $optionsMap[$row['question_id']][] = $row;
}

// Calculate Time Remaining
$start_ts = strtotime($attempt['start_time']);
$limit_sec = $quiz['time_limit'] * 60;
$now = time();
$elapsed = $now - $start_ts;
$remaining = $limit_sec - $elapsed;

if ($remaining <= 0) {
    // Force Submit if time over
    echo "<script>window.location.href = 'submit_quiz.php?attempt_id=" . $attempt['id'] . "&auto=1';</script>";
    exit;
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4><?php echo htmlspecialchars($quiz['title']); ?></h4>
            <div class="alert alert-warning py-1 mb-0">
                <i class="far fa-clock"></i> Time Left: <span id="timer">00:00</span>
            </div>
        </div>

        <form action="submit_quiz.php" method="POST" id="quizForm">
            <input type="hidden" name="attempt_id" value="<?php echo $attempt['id']; ?>">
            <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">
            <?php csrf_field(); ?>

            <?php foreach($questions as $index => $q): ?>
            <div class="card mb-3">
                <div class="card-header">
                    <strong>Q<?php echo $index + 1; ?>.</strong> <?php echo htmlspecialchars($q['question_text']); ?>
                    <span class="float-end text-muted small"><?php echo $q['marks']; ?> Marks</span>
                </div>
                <div class="card-body">
                    <?php if($q['question_image']): ?>
                        <div class="mb-3">
                            <img src="../uploads/<?php echo $q['question_image']; ?>" class="img-fluid" style="max-height: 200px;">
                        </div>
                    <?php endif; ?>

                    <?php if(isset($optionsMap[$q['id']])): ?>
                        <?php foreach($optionsMap[$q['id']] as $opt): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" 
                                   name="answers[<?php echo $q['id']; ?>]" 
                                   value="<?php echo $opt['id']; ?>" 
                                   id="opt_<?php echo $opt['id']; ?>">
                            <label class="form-check-label" for="opt_<?php echo $opt['id']; ?>">
                                <?php if($opt['option_image']): ?>
                                    <div class="mb-2">
                                        <img src="../uploads/<?php echo $opt['option_image']; ?>" class="img-thumbnail" style="max-height: 100px;">
                                    </div>
                                <?php endif; ?>
                                <?php echo htmlspecialchars($opt['option_text']); ?>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>

            <div class="d-grid mb-5">
                <button type="submit" class="btn btn-primary btn-lg">Submit Quiz</button>
            </div>
        </form>
    </div>
</div>

<script>
    let timeLeft = <?php echo $remaining; ?>;
    const timerDisplay = document.getElementById('timer');
    
    const interval = setInterval(() => {
        if (timeLeft <= 0) {
            clearInterval(interval);
            alert("Time is up! Submitting your quiz.");
            document.getElementById('quizForm').submit();
        } else {
            let m = Math.floor(timeLeft / 60);
            let s = timeLeft % 60;
            timerDisplay.textContent = `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
            timeLeft--;
        }
    }, 1000);
</script>

<?php require_once 'includes/footer.php'; ?>
