<?php
require_once 'includes/header.php';

$question_id = $_GET['id'] ?? 0;
$quiz_id = $_GET['quiz_id'] ?? 0;

if (!$question_id) {
    redirect("admin/questions.php?quiz_id=$quiz_id");
}

$stmt = $pdo->prepare("SELECT * FROM questions WHERE id = ?");
$stmt->execute([$question_id]);
$question = $stmt->fetch();

if (!$question) die("Question not found.");

// Fetch Options
$optStmt = $pdo->prepare("SELECT * FROM options WHERE question_id = ?");
$optStmt->execute([$question_id]);
$options = $optStmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCSRFToken($_POST['csrf_token']);

    $q_text = sanitize($_POST['question_text']);
    $q_type = $_POST['question_type'];
    $a_type = $_POST['answer_type'];
    $marks = $_POST['marks'];
    
    // Image Upload (Update if new file)
    $q_image = $question['question_image'];
    if (!empty($_FILES['question_image']['name'])) {
        $target = "../uploads/" . basename($_FILES['question_image']['name']);
        if(move_uploaded_file($_FILES['question_image']['tmp_name'], $target)) {
            $q_image = basename($_FILES['question_image']['name']);
        }
    }

    $pdo->beginTransaction();
    try {
        $updStmt = $pdo->prepare("UPDATE questions SET question_text=?, question_image=?, question_type=?, answer_type=?, marks=? WHERE id=?");
        $updStmt->execute([$q_text, $q_image, $q_type, $a_type, $marks, $question_id]);

        // Process Options (Update existing, Insert new if any - but we keep 4 static here for simplicity)
        $option_ids = $_POST['option_id'] ?? [];
        $option_texts = $_POST['option_text'];
        $correct_options = $_POST['is_correct'] ?? [];

        foreach ($option_texts as $index => $opt_text) {
            $opt_id = $option_ids[$index] ?? 0;
            $is_correct = in_array($index, $correct_options) ? 1 : 0;
            
            // Handle Option Image
            $opt_image = ''; 
            // Fetch existing if updating
            if ($opt_id) {
                $currOptStmt = $pdo->prepare("SELECT option_image FROM options WHERE id=?");
                $currOptStmt->execute([$opt_id]);
                $opt_image = $currOptStmt->fetchColumn();
            }

            if (!empty($_FILES['option_image']['name'][$index])) {
                $opt_target = "../uploads/" . basename($_FILES['option_image']['name'][$index]);
                if(move_uploaded_file($_FILES['option_image']['tmp_name'][$index], $opt_target)) {
                    $opt_image = basename($_FILES['option_image']['name'][$index]);
                }
            }

            if ($opt_id) {
                // Update
                $updOpt = $pdo->prepare("UPDATE options SET option_text=?, option_image=?, is_correct=? WHERE id=?");
                $updOpt->execute([$opt_text, $opt_image, $is_correct, $opt_id]);
            } else {
                // Insert (if we allowed adding more options dynamically)
                 $insOpt = $pdo->prepare("INSERT INTO options (question_id, option_text, option_image, is_correct) VALUES (?, ?, ?, ?)");
                 $insOpt->execute([$question_id, $opt_text, $opt_image, $is_correct]);
            }
        }

        $pdo->commit();
        $message = "Question updated successfully!";
        // Refresh data
        $stmt->execute([$question_id]);
        $question = $stmt->fetch();
        $optStmt->execute([$question_id]);
        $options = $optStmt->fetchAll();

    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Failed to update: " . $e->getMessage();
    }
}
?>

<h3>Edit Question</h3>
<a href="questions.php?quiz_id=<?php echo $quiz_id; ?>" class="btn btn-secondary mb-3"><i class="fas fa-arrow-left"></i> Back to Questions</a>

<?php if(isset($message)): ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <?php csrf_field(); ?>
            
            <div class="mb-3">
                <label>Question Text</label>
                <textarea name="question_text" class="form-control" rows="2" required><?php echo htmlspecialchars($question['question_text']); ?></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Question Image</label>
                    <input type="file" name="question_image" class="form-control">
                    <?php if($question['question_image']): ?>
                        <small>Current: <?php echo $question['question_image']; ?></small>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Question Type</label>
                    <select name="question_type" class="form-select">
                        <option value="text" <?php echo $question['question_type'] == 'text' ? 'selected' : ''; ?>>Text Only</option>
                        <option value="image" <?php echo $question['question_type'] == 'image' ? 'selected' : ''; ?>>Image Only</option>
                        <option value="both" <?php echo $question['question_type'] == 'both' ? 'selected' : ''; ?>>Text + Image</option>
                    </select>
                </div>
                 <div class="col-md-4 mb-3">
                    <label>Answer Type</label>
                    <select name="answer_type" class="form-select">
                        <option value="text" <?php echo $question['answer_type'] == 'text' ? 'selected' : ''; ?>>Text Only</option>
                        <option value="image" <?php echo $question['answer_type'] == 'image' ? 'selected' : ''; ?>>Image Only</option>
                        <option value="both" <?php echo $question['answer_type'] == 'both' ? 'selected' : ''; ?>>Text + Image</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                 <label>Marks</label>
                 <input type="number" name="marks" class="form-control" value="<?php echo $question['marks']; ?>">
            </div>

            <hr>
            <h5>Edit Options</h5>
            <?php foreach($options as $index => $opt): ?>
            <input type="hidden" name="option_id[]" value="<?php echo $opt['id']; ?>">
            <div class="input-group mb-2">
                <div class="input-group-text">
                    <input class="form-check-input mt-0" type="radio" name="is_correct[]" value="<?php echo $index; ?>" <?php echo $opt['is_correct'] ? 'checked' : ''; ?>>
                </div>
                <input type="text" name="option_text[]" class="form-control" value="<?php echo htmlspecialchars($opt['option_text']); ?>" required>
                <input type="file" name="option_image[]" class="form-control">
                <?php if($opt['option_image']): ?>
                    <span class="input-group-text"><i class="fas fa-image" title="<?php echo $opt['option_image']; ?>"></i></span>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-primary mt-3">Update Question</button>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
