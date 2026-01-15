<?php
require_once 'includes/header.php';

// Fetch Statistics
$usersCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$quizzesCount = $pdo->query("SELECT COUNT(*) FROM quizzes")->fetchColumn();
$salesSum = $pdo->query("SELECT SUM(amount) FROM payments WHERE payment_status = 'COMPLETED'")->fetchColumn();
$attemptCount = $pdo->query("SELECT COUNT(*) FROM quiz_attempts")->fetchColumn();
?>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Total Users</h5>
                <p class="card-text display-6"><?php echo $usersCount; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Total Sales</h5>
                <p class="card-text display-6">$<?php echo number_format($salesSum ?? 0, 2); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h5 class="card-title">Total Quizzes</h5>
                <p class="card-text display-6"><?php echo $quizzesCount; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title">Attempts</h5>
                <p class="card-text display-6"><?php echo $attemptCount; ?></p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">Recent Payments</div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Transaction ID</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmParam = $pdo->query("SELECT p.*, u.name as user_name FROM payments p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC LIMIT 5");
                        while($row = $stmParam->fetch()):
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['transaction_id']); ?></td>
                            <td>$<?php echo htmlspecialchars($row['amount']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td><span class="badge bg-<?php echo $row['payment_status'] == 'COMPLETED' ? 'success' : 'secondary'; ?>"><?php echo htmlspecialchars($row['payment_status']); ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
