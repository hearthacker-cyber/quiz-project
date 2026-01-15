<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../config/security.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['auto'])) {
    
    // If not auto submit, valid CSRF
    if (!isset($_GET['auto'])) verifyCSRFToken($_POST['csrf_token']);

    $quiz_id = $_POST['quiz_id'] ?? 0; // Or fetch from attempt
    $attempt_id = $_POST['attempt_id'] ?? $_GET['attempt_id'];
    $answers = $_POST['answers'] ?? [];

    // Verify Attempt Owner
    $stmt = $pdo->prepare("SELECT * FROM quiz_attempts WHERE id = ? AND user_id = ? AND status = 'ongoing'");
    $stmt->execute([$attempt_id, $_SESSION['user_id']]);
    $attempt = $stmt->fetch();

    if (!$attempt) {
        die("Invalid attempt or already completed.");
    }
    
    // If quiz ID missing (auto submit case might need it), fetch from attempt
    if (!$quiz_id) $quiz_id = $attempt['quiz_id'];

    // Calculate Score
    $score = 0;
    
    // Get all correct answers for this quiz
    // This query gets question ID and the CORRECT option ID
    $qSql = "SELECT q.id as q_id, q.marks, o.id as correct_opt_id 
             FROM questions q 
             JOIN options o ON q.id = o.question_id 
             WHERE q.quiz_id = ? AND o.is_correct = 1";
    $qStmt = $pdo->prepare($qSql);
    $qStmt->execute([$quiz_id]);
    $correct_answers = $qStmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_UNIQUE); 
    // Format: [q_id => ['marks' => X, 'correct_opt_id' => Y]]
    
    $total_questions = count($correct_answers);
    
    foreach ($answers as $q_id => $user_opt_id) {
        if (isset($correct_answers[$q_id])) {
            if ($correct_answers[$q_id]['correct_opt_id'] == $user_opt_id) {
                $score += $correct_answers[$q_id]['marks'];
            }
        }
    }
    
    // Calculate Percentage (based on total marks, or total questions? Requirements say "Percentage")
    // Let's sum total possible marks
    $totalMarks = 0;
    foreach($correct_answers as $ca) $totalMarks += $ca['marks'];
    
    $percentage = ($totalMarks > 0) ? ($score / $totalMarks) * 100 : 0;
    
    // Update Attempt
    $update = $pdo->prepare("UPDATE quiz_attempts SET end_time = NOW(), score = ?, total_questions = ?, status = 'completed' WHERE id = ?");
    $update->execute([$percentage, $total_questions, $attempt_id]);
    
    // Generate Certificate if Passed and Enabled
    $quizStmt = $pdo->prepare("SELECT enable_certificate FROM quizzes WHERE id = ?");
    $quizStmt->execute([$quiz_id]);
    $quizInfo = $quizStmt->fetch();

    if ($percentage >= $quizInfo['enable_certificate'] && $quizInfo['enable_certificate'] == 1) { // Wait, enable_cert is bool. Pass check is separate.
        // Actually we need to check pass percentage from quiz settings too, but we calculated $percentage only here.
        // Re-fetching full quiz info to be safe
         $qFull = $pdo->prepare("SELECT enable_certificate, pass_percentage FROM quizzes WHERE id = ?");
         $qFull->execute([$quiz_id]);
         $qData = $qFull->fetch();
         
         if ($percentage >= $qData['pass_percentage'] && $qData['enable_certificate']) {
             // Create unique Certificate Code
             $certCode = strtoupper(uniqid('CERT-'));
             
             // Check if already exists for this quiz/user? 
             // Requirement says "Certificate generation feature... User Flow: If eligible -> show Download".
             // We can issue a NEW certificate for every pass, or just distinct.
             // Let's issue one if not exists for this attempt? Certificates usually map to user-quiz.
             // We will insert if not exists.
             
             $checkCert = $pdo->prepare("SELECT id FROM certificates WHERE user_id = ? AND quiz_id = ?");
             $checkCert->execute([$_SESSION['user_id'], $quiz_id]);
             if (!$checkCert->fetch()) {
                 $insCert = $pdo->prepare("INSERT INTO certificates (user_id, quiz_id, certificate_code) VALUES (?, ?, ?)");
                 $insCert->execute([$_SESSION['user_id'], $quiz_id, $certCode]);
             }
         }
    }
    
    redirect('user/result.php?quiz_id=' . $quiz_id);

} else {
    redirect('user/dashboard.php');
}
?>
