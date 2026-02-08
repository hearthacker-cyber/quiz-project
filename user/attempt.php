
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

<!-- Quiz Header -->
<div class="quiz-header mb-5">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="mb-2"><?php echo htmlspecialchars($quiz['title']); ?></h1>
            <p class="text-secondary mb-0">Attempt <?php echo $attemptCount + 1; ?> - <?php echo $quiz['time_limit']; ?> minutes</p>
        </div>
        <div class="col-md-4">
            <div class="card timer-card elevation-3">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center">
                        <i class="material-icons-round me-3 text-warning" style="font-size: 2rem;">timer</i>
                        <div>
                            <div class="timer-label text-secondary small">Time Remaining</div>
                            <div class="timer-display" id="timer"><?php echo gmdate("i:s", $remaining); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Progress Bar -->
    <div class="progress mt-4" style="height: 6px;">
        <div class="progress-bar" id="progressBar" role="progressbar" style="width: 0%"></div>
    </div>
</div>

<!-- Quiz Form -->
<form action="submit_quiz.php" method="POST" id="quizForm">
    <input type="hidden" name="attempt_id" value="<?php echo $attempt['id']; ?>">
    <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">
    <?php csrf_field(); ?>
    
    <!-- Question Navigation -->
    <div class="card elevation-2 mb-4">
        <div class="card-body">
            <h6 class="mb-3"><i class="material-icons-round me-2">list</i> Question Navigation</h6>
            <div class="question-nav">
                <?php foreach($questions as $index => $q): ?>
                <button type="button" class="question-nav-btn" data-question="<?php echo $index; ?>" onclick="scrollToQuestion(<?php echo $index; ?>)">
                    <?php echo $index + 1; ?>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <!-- Questions -->
    <?php foreach($questions as $index => $q): ?>
    <div class="card question-card elevation-2 mb-4" id="question-<?php echo $index; ?>">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <span class="question-number me-3">Q<?php echo $index + 1; ?></span>
                <h5 class="mb-0"><?php echo htmlspecialchars($q['question_text']); ?></h5>
            </div>
            <span class="badge bg-primary">
                <i class="material-icons-round me-1" style="font-size: 14px;">star</i>
                <?php echo $q['marks']; ?> mark<?php echo $q['marks'] > 1 ? 's' : ''; ?>
            </span>
        </div>
        
        <div class="card-body">
            <?php if($q['question_image']): ?>
                <div class="question-image mb-4 text-center">
                    <img src="../uploads/<?php echo $q['question_image']; ?>" class="img-fluid rounded elevation-1" style="max-height: 300px;">
                </div>
            <?php endif; ?>
            
            <?php if(isset($optionsMap[$q['id']])): ?>
                <div class="options-grid">
                    <?php foreach($optionsMap[$q['id']] as $optIndex => $opt): ?>
                    <div class="option-item">
                        <input class="option-input" type="radio" 
                               name="answers[<?php echo $q['id']; ?>]" 
                               value="<?php echo $opt['id']; ?>" 
                               id="opt_<?php echo $opt['id']; ?>"
                               onchange="updateProgress()">
                        <label class="option-label" for="opt_<?php echo $opt['id']; ?>">
                            <span class="option-letter"><?php echo chr(65 + $optIndex); ?></span>
                            <div class="option-content">
                                <?php if($opt['option_image']): ?>
                                    <div class="option-image mb-2">
                                        <img src="../uploads/<?php echo $opt['option_image']; ?>" class="img-thumbnail" style="max-height: 120px;">
                                    </div>
                                <?php endif; ?>
                                <?php if($opt['option_text']): ?>
                                    <div class="option-text"><?php echo htmlspecialchars($opt['option_text']); ?></div>
                                <?php endif; ?>
                            </div>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
    
    <!-- Submit Section -->
    <div class="sticky-submit">
        <div class="card elevation-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="progress-info">
                            <span id="answeredCount">0</span> of <?php echo count($questions); ?> questions answered
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <button type="button" class="btn btn-outline-secondary me-2" onclick="confirmSubmit()">
                            <i class="material-icons-round me-1">close</i>
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="material-icons-round me-2">send</i>
                            Submit Quiz
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
// Timer functionality
let timeLeft = <?php echo $remaining; ?>;
const timerDisplay = document.getElementById('timer');
const progressBar = document.getElementById('progressBar');
const totalTime = <?php echo $quiz['time_limit'] * 60; ?>;

function updateTimer() {
    if (timeLeft <= 0) {
        clearInterval(timerInterval);
        showTimeUpAlert();
        return;
    }
    
    let m = Math.floor(timeLeft / 60);
    let s = timeLeft % 60;
    timerDisplay.textContent = `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
    
    // Update progress bar
    const percentage = ((totalTime - timeLeft) / totalTime) * 100;
    progressBar.style.width = percentage + '%';
    
    // Change color based on time remaining
    if (timeLeft < 60) {
        progressBar.style.backgroundColor = '#dd2c00';
        timerDisplay.style.color = '#dd2c00';
    } else if (timeLeft < 300) {
        progressBar.style.backgroundColor = '#ffab00';
        timerDisplay.style.color = '#ffab00';
    }
    
    timeLeft--;
}

const timerInterval = setInterval(updateTimer, 1000);

// Progress tracking
function updateProgress() {
    const answered = document.querySelectorAll('input[type="radio"]:checked').length;
    const total = <?php echo count($questions); ?>;
    const percentage = (answered / total) * 100;
    
    document.getElementById('answeredCount').textContent = answered;
    
    // Update question navigation
    document.querySelectorAll('.question-nav-btn').forEach((btn, index) => {
        const questionId = <?php echo json_encode(array_column($questions, 'id')); ?>[index];
        const hasAnswer = document.querySelector(`input[name="answers[${questionId}]"]:checked`);
        btn.classList.toggle('answered', hasAnswer);
    });
}

// Question navigation
function scrollToQuestion(index) {
    const question = document.getElementById(`question-${index}`);
    question.scrollIntoView({ behavior: 'smooth', block: 'start' });
    question.classList.add('highlighted');
    setTimeout(() => question.classList.remove('highlighted'), 2000);
}

// Confirmation dialog
function confirmSubmit() {
    const answered = document.querySelectorAll('input[type="radio"]:checked').length;
    const total = <?php echo count($questions); ?>;
    
    Swal.fire({
        title: 'Are you sure?',
        html: `You have answered ${answered} of ${total} questions.<br>Do you want to submit your quiz?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#6200ee',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, submit!',
        cancelButtonText: 'Continue Quiz',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('quizForm').submit();
        }
    });
}

// Time up alert
function showTimeUpAlert() {
    Swal.fire({
        title: 'Time\'s Up!',
        text: 'Your time has expired. Submitting your quiz now.',
        icon: 'warning',
        confirmButtonColor: '#6200ee',
        allowOutsideClick: false,
        allowEscapeKey: false
    }).then(() => {
        document.getElementById('quizForm').submit();
    });
}

// Initialize progress on page load
document.addEventListener('DOMContentLoaded', updateProgress);

// Add keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 's' && (e.ctrlKey || e.metaKey)) {
        e.preventDefault();
        confirmSubmit();
    }
});

// Form submission confirmation
document.getElementById('quizForm').addEventListener('submit', function(e) {
    const answered = document.querySelectorAll('input[type="radio"]:checked').length;
    const total = <?php echo count($questions); ?>;
    
    if (answered < total) {
        e.preventDefault();
        Swal.fire({
            title: 'Submit Anyway?',
            html: `You have answered ${answered} of ${total} questions.<br>Are you sure you want to submit?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6200ee',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, submit!',
            cancelButtonText: 'Continue Quiz'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('quizForm').submit();
            }
        });
    }
});

// Add SweetAlert for better alerts
if (typeof Swal === 'undefined') {
    const script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
    document.head.appendChild(script);
}
</script>

<style>
/* Quiz Page Specific Styles */
.quiz-header {
    position: sticky;
    top: 0;
    background: var(--surface-color);
    z-index: 100;
    padding: 1.5rem 0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.timer-card {
    background: linear-gradient(135deg, #1a1e2e, #2d3436);
    color: white;
    border: none;
}

.timer-display {
    font-size: 2rem;
    font-weight: 700;
    font-family: 'Courier New', monospace;
}

.question-nav {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.question-nav-btn {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    border: 2px solid rgba(98, 0, 238, 0.2);
    background: white;
    color: var(--text-primary);
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.question-nav-btn:hover {
    border-color: var(--primary-color);
    background: rgba(98, 0, 238, 0.1);
    transform: translateY(-2px);
}

.question-nav-btn.answered {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.question-card {
    transition: all 0.3s ease;
}

.question-card.highlighted {
    animation: highlight 2s ease;
}

@keyframes highlight {
    0%, 100% { border-color: transparent; }
    50% { border-color: var(--primary-color); box-shadow: 0 0 0 2px rgba(98, 0, 238, 0.1); }
}

.question-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
}

.options-grid {
    display: grid;
    gap: 1rem;
}

.option-item {
    position: relative;
}

.option-input {
    display: none;
}

.option-label {
    display: flex;
    align-items: center;
    padding: 1rem;
    border: 2px solid rgba(0,0,0,0.1);
    border-radius: var(--border-radius);
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
}

.option-input:checked + .option-label {
    border-color: var(--primary-color);
    background: rgba(98, 0, 238, 0.05);
    box-shadow: 0 4px 12px rgba(98, 0, 238, 0.1);
}

.option-label:hover {
    border-color: var(--primary-color);
    transform: translateY(-2px);
}

.option-letter {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    margin-right: 1rem;
    flex-shrink: 0;
}

.option-input:checked + .option-label .option-letter {
    background: var(--primary-color);
    color: white;
}

.option-content {
    flex-grow: 1;
}

.option-image img {
    max-width: 100%;
    border-radius: 4px;
}

.sticky-submit {
    position: sticky;
    bottom: 0;
    background: var(--surface-color);
    padding: 1rem 0;
    box-shadow: 0 -4px 20px rgba(0,0,0,0.1);
    z-index: 100;
}

.progress-info {
    font-size: 1rem;
    font-weight: 500;
    color: var(--text-primary);
}

.progress-info span {
    color: var(--primary-color);
    font-weight: 700;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .quiz-header {
        position: relative;
        padding: 1rem 0;
    }
    
    .sticky-submit {
        position: fixed;
        left: 0;
        right: 0;
        bottom: 0;
        padding: 0.5rem;
    }
    
    .sticky-submit .card-body {
        padding: 1rem;
    }
    
    .timer-display {
        font-size: 1.5rem;
    }
    
    .question-nav-btn {
        width: 36px;
        height: 36px;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>
