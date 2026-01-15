<?php
require_once 'includes/header.php';

// Handle Add/Edit/Delete
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? 0;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCSRFToken($_POST['csrf_token']);
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    $status = $_POST['status'];

    if (isset($_POST['update_id'])) {
        // Update
        $stmt = $pdo->prepare("UPDATE categories SET name=?, description=?, status=? WHERE id=?");
        $stmt->execute([$name, $description, $status, $_POST['update_id']]);
        $message = "Category updated successfully!";
    } else {
        // Create
        $stmt = $pdo->prepare("INSERT INTO categories (name, description, status) VALUES (?, ?, ?)");
        $stmt->execute([$name, $description, $status]);
        $message = "Category created successfully!";
    }
}

if ($action === 'delete' && $id) {
    // Delete - In a real app, check for dependencies (quizzes) first!
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    redirect('admin/categories.php');
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY id DESC")->fetchAll();
?>

<h3>Manage Categories</h3>
<?php if($message): ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-header">Add / Edit Category</div>
    <div class="card-body">
        <form method="POST">
            <?php csrf_field(); ?>
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="name" class="form-control" placeholder="Category Name" required>
                </div>
                <div class="col-md-4">
                    <input type="text" name="description" class="form-control" placeholder="Description">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<table class="table table-bordered bg-white">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($categories as $cat): ?>
        <tr>
            <td><?php echo $cat['id']; ?></td>
            <td><?php echo htmlspecialchars($cat['name']); ?></td>
            <td><?php echo htmlspecialchars($cat['description']); ?></td>
            <td>
                <span class="badge bg-<?php echo $cat['status'] == 'active' ? 'success' : 'secondary'; ?>">
                    <?php echo ucfirst($cat['status']); ?>
                </span>
            </td>
            <td>
                <a href="?action=delete&id=<?php echo $cat['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once 'includes/footer.php'; ?>
