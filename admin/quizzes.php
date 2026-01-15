<?php
require_once 'includes/header.php';

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? 0;

if ($action === 'delete' && $id) {
    $stmt = $pdo->prepare("DELETE FROM quizzes WHERE id = ?");
    $stmt->execute([$id]);
    redirect('admin/quizzes.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCSRFToken($_POST['csrf_token']);
    
    $category_id = $_POST['category_id'];
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $price = $_POST['price'];
    $time_limit = $_POST['time_limit'];
    $total_marks = $_POST['total_marks'];
    $pass_percentage = $_POST['pass_percentage'];
    $max_attempts = $_POST['max_attempts'];
    $enable_certificate = isset($_POST['enable_certificate']) ? 1 : 0;
    
    $stmt = $pdo->prepare("INSERT INTO quizzes (category_id, title, description, price, time_limit, total_marks, pass_percentage, max_attempts, enable_certificate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$category_id, $title, $description, $price, $time_limit, $total_marks, $pass_percentage, $max_attempts, $enable_certificate]);
    $message = "Quiz added successfully!";
}

$quizzes = $pdo->query("SELECT q.*, c.name as category_name FROM quizzes q JOIN categories c ON q.category_id = c.id ORDER BY q.id DESC")->fetchAll();
$categories = $pdo->query("SELECT * FROM categories WHERE status = 'active'")->fetchAll();
?>

<h3>Manage Quizzes</h3>

<!-- Add Quiz Modal Trigger -->
<button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addQuizModal">
    Add New Quiz
</button>

<div class="table-responsive">
    <table class="table table-bordered bg-white">
        <thead>
            <tr>
                <th>ID</th>
                <th>Category</th>
                <th>Title</th>
                <th>Price</th>
                <th>Time (Min)</th>
                <th>Att/Cert</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($quizzes as $q): ?>
            <tr>
                <td><?php echo $q['id']; ?></td>
                <td><?php echo htmlspecialchars($q['category_name']); ?></td>
                <td><?php echo htmlspecialchars($q['title']); ?></td>
                <td><?php echo $q['price'] > 0 ? '$'.$q['price'] : 'Free'; ?></td>
                <td><?php echo $q['time_limit']; ?></td>
                <td>
                    <span class="badge bg-secondary"><?php echo $q['max_attempts'] == 0 ? 'Unlimited' : $q['max_attempts']; ?> Att</span>
                    <?php if($q['enable_certificate']): ?>
                        <span class="badge bg-success">Cert</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="questions.php?quiz_id=<?php echo $q['id']; ?>" class="btn btn-sm btn-info text-white">Questions</a>
                    <a href="?action=delete&id=<?php echo $q['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this quiz?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="addQuizModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <?php csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Add New Quiz</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Category</label>
                            <select name="category_id" class="form-select" required>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Description</label>
                            <textarea name="description" class="form-control"></textarea>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Price (0 for Free)</label>
                            <input type="number" step="0.01" name="price" class="form-control" value="0">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Time Limit (Min)</label>
                            <input type="number" name="time_limit" class="form-control" required>
                        </div>
                         <div class="col-md-3 mb-3">
                            <label>Total Marks</label>
                            <input type="number" name="total_marks" class="form-control" required>
                        </div>
                         <div class="col-md-3 mb-3">
                            <label>Pass %</label>
                            <input type="number" name="pass_percentage" class="form-control" value="50">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Max Attempts (0=Unlimited)</label>
                            <input type="number" name="max_attempts" class="form-control" value="0">
                        </div>
                        <div class="col-md-3 mb-3 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="enable_certificate" value="1" id="enableCert">
                                <label class="form-check-label" for="enableCert">Enable Certificate</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Quiz</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
