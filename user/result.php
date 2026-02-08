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

// Fetch Questions
$questionsStmt = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ?");
$questionsStmt->execute([$quiz_id]);
$questions = $questionsStmt->fetchAll();

// Fetch Options
$optionsStmt = $pdo->prepare("SELECT * FROM options WHERE question_id IN (SELECT id FROM questions WHERE quiz_id = ?)");
$optionsStmt->execute([$quiz_id]);
$allOptions = $optionsStmt->fetchAll(PDO::FETCH_GROUP); // Group by question_id? No, FETCH_GROUP groups by first column if distinct. 
// Actually FETCH_GROUP groups by the first column. So if I select question_id as first col, it works.
$optionsStmt = $pdo->prepare("SELECT question_id, id, option_text, is_correct FROM options WHERE question_id IN (SELECT id FROM questions WHERE quiz_id = ?)");
$optionsStmt->execute([$quiz_id]);
$optionsByQuestion = $optionsStmt->fetchAll(PDO::FETCH_GROUP);

// Fetch User Answers
$ansStmt = $pdo->prepare("SELECT question_id, selected_option_id FROM user_answers WHERE attempt_id = ?");
$ansStmt->execute([$attempt['id']]);
$userAnswers = $ansStmt->fetchAll(PDO::FETCH_KEY_PAIR); // [question_id => selected_option_id]

?>

<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <h1 class="dashboard-title">Quiz Result</h1>
        <p class="text-secondary">View your performance and certificate details</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Main Result Card -->
        <div class="card elevation-3 mb-4">
            <div class="card-header bg-gradient-primary text-white">
                <div class="d-flex align-items-center">
                    <i class="material-icons-round me-2">assignment_turned_in</i>
                    <h4 class="mb-0">Quiz Completion</h4>
                </div>
            </div>
            
            <div class="card-body text-center py-5">
                <!-- Result Icon -->
                <div class="mb-4">
                    <?php if($pass): ?>
                        <div class="result-icon success mx-auto mb-3">
                            <i class="material-icons-round">check_circle</i>
                        </div>
                        <h2 class="text-success fw-bold">Congratulations!</h2>
                        <p class="text-success mb-0">You have successfully passed the quiz</p>
                    <?php else: ?>
                        <div class="result-icon failed mx-auto mb-3">
                            <i class="material-icons-round">cancel</i>
                        </div>
                        <h2 class="text-danger fw-bold">Better Luck Next Time</h2>
                        <p class="text-danger mb-0">You did not meet the passing criteria</p>
                    <?php endif; ?>
                </div>
                
                <!-- Quiz Title -->
                <h3 class="mb-4"><?php echo htmlspecialchars($quiz['title']); ?></h3>
                
                <!-- Score Display -->
                <div class="score-display mb-5">
                    <div class="score-circle mx-auto mb-3" data-score="<?php echo $attempt['score']; ?>">
                        <span class="score-value"><?php echo number_format($attempt['score'], 1); ?>%</span>
                    </div>
                    <div class="score-label">
                        <?php if($pass): ?>
                            <span class="badge bg-success px-3 py-2">
                                <i class="material-icons-round me-1">emoji_events</i>
                                PASSED
                            </span>
                        <?php else: ?>
                            <span class="badge bg-danger px-3 py-2">
                                <i class="material-icons-round me-1">refresh</i>
                                FAILED
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Details Grid -->
                <!-- [Previous details grid code kept as is for brevity, but included in full file] -->
                 <div class="row g-4 mb-5">
                    <div class="col-md-6">
                        <div class="detail-card">
                            <div class="detail-icon bg-primary-light">
                                <i class="material-icons-round">score</i>
                            </div>
                            <div class="detail-content">
                                <h6 class="detail-label">Your Score</h6>
                                <p class="detail-value"><?php echo number_format($attempt['score'], 1); ?>%</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="detail-card">
                            <div class="detail-icon bg-warning-light">
                                <i class="material-icons-round">flag</i>
                            </div>
                            <div class="detail-content">
                                <h6 class="detail-label">Passing Score</h6>
                                <p class="detail-value"><?php echo $quiz['pass_percentage']; ?>%</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="detail-card">
                            <div class="detail-icon bg-info-light">
                                <i class="material-icons-round">timer</i>
                            </div>
                            <div class="detail-content">
                                <h6 class="detail-label">Time Taken</h6>
                                <p class="detail-value">
                                    <?php 
                                    $start = new DateTime($attempt['start_time']);
                                    $end = new DateTime($attempt['end_time']);
                                    $diff = $start->diff($end);
                                    echo $diff->format('%i min %s sec');
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="detail-card">
                            <div class="detail-icon bg-success-light">
                                <i class="material-icons-round">calendar_today</i>
                            </div>
                            <div class="detail-content">
                                <h6 class="detail-label">Completed On</h6>
                                <p class="detail-value"><?php echo date('F d, Y h:i A', strtotime($attempt['end_time'])); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="action-buttons">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="dashboard.php" class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center">
                                <i class="material-icons-round me-2">dashboard</i>
                                Dashboard
                            </a>
                        </div>
                        
                        <div class="col-md-4">
                            <a href="quizzes.php" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center">
                                <i class="material-icons-round me-2">explore</i>
                                Browse More
                            </a>
                        </div>
                        
                        <?php 
                        // Check for certificate
                        $certStmt = $pdo->prepare("SELECT certificate_code FROM certificates WHERE user_id = ? AND quiz_id = ?");
                        $certStmt->execute([$user_id, $quiz_id]);
                        $cert = $certStmt->fetch();
                        
                        if ($cert): 
                        ?>
                        <div class="col-md-4">
                            <a href="certificate.php?code=<?php echo $cert['certificate_code']; ?>" target="_blank" class="btn btn-success w-100 d-flex align-items-center justify-content-center">
                                <i class="material-icons-round me-2">download</i>
                                Certificate
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Question Analysis -->
        <h4 class="mb-3">Question Breakdown</h4>
        <?php foreach ($questions as $index => $q): 
            $qId = $q['id'];
            $myAnswerId = $userAnswers[$qId] ?? null;
            $options = $optionsByQuestion[$qId] ?? [];
            
            // Find correct option and my option text
            $correctOption = null;
            $myOption = null;
            
            foreach ($options as $opt) {
                if ($opt['is_correct']) $correctOption = $opt;
                if ($opt['id'] == $myAnswerId) $myOption = $opt;
            }
            
            $isCorrect = ($myAnswerId == $correctOption['id']);
            $cardClass = $isCorrect ? 'border-success' : 'border-danger';
            $headerClass = $isCorrect ? 'bg-success text-white' : 'bg-danger text-white';
            $headerIcon = $isCorrect ? 'check_circle' : 'cancel';
            $headerText = $isCorrect ? 'Correct' : 'Wrong';
            
            // If question wasn't answered
            if ($myAnswerId === null) {
                $isCorrect = false;
                $cardClass = 'border-warning';
                $headerClass = 'bg-warning text-dark';
                $headerIcon = 'help';
                $headerText = 'Not Answered';
            }
        ?>
        <div class="card mb-3 <?php echo $cardClass; ?>" style="border-width: 2px;">
            <div class="card-header <?php echo $headerClass; ?> d-flex justify-content-between align-items-center">
                <span>Question <?php echo $index + 1; ?></span>
                <div class="d-flex align-items-center">
                    <i class="material-icons-round me-1"><?php echo $headerIcon; ?></i>
                    <span class="fw-bold"><?php echo $headerText; ?></span>
                </div>
            </div>
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($q['question_text']); ?></h5>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <small class="text-muted d-block uppercase fw-bold">Your Answer</small>
                        <div class="p-2 rounded <?php echo $isCorrect ? 'bg-success-light text-success' : ($myAnswerId ? 'bg-danger-light text-danger' : 'bg-light'); ?>">
                            <?php if ($myAnswerId): ?>
                                <?php echo htmlspecialchars($myOption['option_text']); ?> 
                                <i class="material-icons-round fs-6 align-middle ms-1"><?php echo $isCorrect ? 'check' : 'close'; ?></i>
                            <?php else: ?>
                                <span class="text-muted fst-italic">Not answered</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-2">
                        <small class="text-muted d-block uppercase fw-bold">Correct Answer</small>
                        <div class="p-2 rounded bg-success-light text-success">
                            <?php echo htmlspecialchars($correctOption['option_text']); ?>
                            <i class="material-icons-round fs-6 align-middle ms-1">check</i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        
        <!-- Performance Tips -->
        <?php if(!$pass): ?>
        <div class="card elevation-2 mt-4">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <i class="material-icons-round me-2 text-warning">lightbulb</i>
                    <h5 class="mb-0">Tips for Improvement</h5>
                </div>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="material-icons-round text-primary me-2">done</i>
                        Review the quiz questions and your answers above
                    </li>
                    <li class="mb-2">
                        <i class="material-icons-round text-primary me-2">done</i>
                        Focus on areas where you scored lower
                    </li>
                </ul>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* Result Page Specific Styles */
.result-icon {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
}

.result-icon.success {
    background: linear-gradient(135deg, rgba(0, 200, 83, 0.1), rgba(100, 221, 23, 0.1));
    color: #00c853;
}

.result-icon.failed {
    background: linear-gradient(135deg, rgba(221, 44, 0, 0.1), rgba(255, 110, 64, 0.1));
    color: #dd2c00;
}

.score-circle {
    width: 140px;
    height: 140px;
    border-radius: 50%;
    background: conic-gradient(#6200ee <?php echo $attempt['score']; ?>%, #f5f5f5 0);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.score-circle::before {
    content: '';
    position: absolute;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: white;
}

.score-value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
    position: relative;
    z-index: 1;
}

.detail-card {
    display: flex;
    align-items: center;
    padding: 1.25rem;
    border-radius: var(--border-radius);
    background: var(--surface-color);
    border: 1px solid rgba(0,0,0,0.08);
    transition: var(--transition);
}

.detail-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-2);
}

.detail-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--border-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    color: white;
}

.detail-icon.bg-primary-light { background: linear-gradient(135deg, #6200ee, #9d46ff); }
.detail-icon.bg-warning-light { background: linear-gradient(135deg, #ffab00, #ffd600); }
.detail-icon.bg-info-light { background: linear-gradient(135deg, #03dac6, #66fff9); }
.detail-icon.bg-success-light { background: linear-gradient(135deg, #00c853, #64dd17); }

.detail-content {
    flex-grow: 1;
}

.detail-label {
    color: var(--text-secondary);
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-value {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0;
    color: var(--text-primary);
}

/* New Analysis Styles */
.bg-success-light { background-color: rgba(0, 200, 83, 0.1); }
.bg-danger-light { background-color: rgba(221, 44, 0, 0.1); }
.border-success { border-color: #00c853 !important; }
.border-danger { border-color: #dd2c00 !important; }
.border-warning { border-color: #ffab00 !important; }
.text-success { color: #00c853 !important; }
.text-danger { color: #dd2c00 !important; }

.action-buttons .btn {
    padding: 0.875rem 1rem;
    border-radius: var(--border-radius);
    font-weight: 500;
}

.action-buttons .btn-success {
    background: linear-gradient(135deg, #00c853, #64dd17);
    border: none;
    color: white;
}
</style>

<script>
// Animate the score circle on page load
document.addEventListener('DOMContentLoaded', function() {
    const scoreCircle = document.querySelector('.score-circle');
    if (scoreCircle) {
        const score = parseFloat(scoreCircle.dataset.score);
        scoreCircle.style.background = `conic-gradient(#6200ee 0%, #f5f5f5 0)`;
        
        setTimeout(() => {
            scoreCircle.style.background = `conic-gradient(#6200ee ${score}%, #f5f5f5 0)`;
            scoreCircle.style.transition = 'background 1.5s ease-in-out';
        }, 300);
    }
    
    // Add fade-in animation to detail cards
    const detailCards = document.querySelectorAll('.detail-card');
    detailCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 300 + (index * 100));
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
