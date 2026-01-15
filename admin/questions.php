<?php
require_once 'includes/header.php';

$quiz_id = $_GET['quiz_id'] ?? 0;
if (!$quiz_id) {
    redirect('admin/quizzes.php');
}

$quiz = $pdo->prepare("SELECT * FROM quizzes WHERE id = ?");
$quiz->execute([$quiz_id]);
$quizData = $quiz->fetch();
if (!$quizData) redirect('admin/quizzes.php');

// Handle Question Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_question'])) {
    verifyCSRFToken($_POST['csrf_token']);
    
    $q_text = sanitize($_POST['question_text']);
    $q_type = $_POST['question_type'];
    $marks = $_POST['marks'];
    
    // Image Upload
    $q_image = '';
    if (!empty($_FILES['question_image']['name'])) {
        $target = "../uploads/" . basename($_FILES['question_image']['name']);
        if(move_uploaded_file($_FILES['question_image']['tmp_name'], $target)) {
            $q_image = basename($_FILES['question_image']['name']);
        }
    }

    $pdo->beginTransaction();
    try {
        $a_type = $_POST['answer_type'] ?? 'text';
        $stmt = $pdo->prepare("INSERT INTO questions (quiz_id, question_text, question_image, question_type, answer_type, marks) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$quiz_id, $q_text, $q_image, $q_type, $a_type, $marks]);
        $question_id = $pdo->lastInsertId();

        // Process Options
        $option_texts = $_POST['option_text'];
        $correct_options = $_POST['is_correct'] ?? []; // Array of indices (0, 1, 2...)

        foreach ($option_texts as $index => $opt_text) {
            $is_correct = in_array($index, $correct_options) ? 1 : 0;
            $opt_image = '';

            // Handle Option Image Upload
            if (!empty($_FILES['option_image']['name'][$index])) {
                $opt_target = "../uploads/" . basename($_FILES['option_image']['name'][$index]);
                if(move_uploaded_file($_FILES['option_image']['tmp_name'][$index], $opt_target)) {
                    $opt_image = basename($_FILES['option_image']['name'][$index]);
                }
            }

            $opt_stmt = $pdo->prepare("INSERT INTO options (question_id, option_text, option_image, is_correct) VALUES (?, ?, ?, ?)");
            $opt_stmt->execute([$question_id, $opt_text, $opt_image, $is_correct]);
        }
        $pdo->commit();
        $message = "Question added successfully!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Failed to add question: " . $e->getMessage();
    }
}

// Delete Question
if (isset($_GET['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM questions WHERE id = ?");
    $stmt->execute([$_GET['delete_id']]);
    redirect("admin/questions.php?quiz_id=$quiz_id");
}

$questions = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ? ORDER BY id DESC");
$questions->execute([$quiz_id]);
$questionsList = $questions->fetchAll();
?>

<h3>Manage Questions for: <?php echo htmlspecialchars($quizData['title']); ?></h3>
<a href="quizzes.php" class="btn btn-secondary mb-3"><i class="fas fa-arrow-left"></i> Back to Quizzes</a>

<div class="card mb-4">
    <div class="card-header bg-primary text-white">Add New Question</div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <?php csrf_field(); ?>
            <input type="hidden" name="add_question" value="1">
            
            <div class="mb-3">
                <label>Question Text</label>
                <textarea name="question_text" class="form-control" rows="2" required></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Question Image (Optional)</label>
                    <input type="file" name="question_image" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Type</label>
                    <select name="question_type" class="form-select">
                        <option value="text">Text Only</option>
                        <option value="image">Image Only</option>
                        <option value="both">Text + Image</option>
                    </select>
                </div>
                 <div class="col-md-4 mb-3">
                    <label>Answer Type</label>
                    <select name="answer_type" class="form-select">
                        <option value="text">Text Only</option>
                        <option value="image">Image Only</option>
                        <option value="both">Text + Image</option>
                    </select>
                </div>
                 <div class="col-md-4 mb-3">
                    <label>Marks</label>
                    <input type="number" name="marks" class="form-control" value="1">
                </div>
            </div>

            <hr>
            <h5>Answer Options (Check the correct one)</h5>
            <div id="options-container">
                <?php for($i=0; $i<4; $i++): ?>
                <div class="input-group mb-2">
                    <div class="input-group-text">
                        <input class="form-check-input mt-0" type="radio" name="is_correct[]" value="<?php echo $i; ?>" <?php echo $i==0 ? 'checked' : ''; ?>>
                    </div>
                    <input type="text" name="option_text[]" class="form-control" placeholder="Option <?php echo $i+1; ?>" required>
                    <input type="file" name="option_image[]" class="form-control">
                </div>
                <?php endfor; ?>
            </div>
            
            <button type="submit" class="btn btn-success mt-3">Save Question</button>
        </form>
    </div>
</div>

<div class="list-group">
    <?php foreach($questionsList as $q): ?>
    <div class="list-group-item">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1"><?php echo htmlspecialchars($q['question_text']); ?></h5>
                <?php if($q['question_image']): ?>
                    <img src="../uploads/<?php echo $q['question_image']; ?>" style="max-height: 100px;">
                <?php endif; ?>
                <span class="badge bg-secondary"><?php echo $q['marks']; ?> Marks</span>
            </div>
            <a href="edit_question.php?id=<?php echo $q['id']; ?>&quiz_id=<?php echo $quiz_id; ?>" class="btn btn-sm btn-warning">Edit</a>
            <a href="?quiz_id=<?php echo $quiz_id; ?>&delete_id=<?php echo $q['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</a>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
