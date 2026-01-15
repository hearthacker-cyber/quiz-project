<?php
require_once 'includes/header.php';

$sql = "SELECT p.*, u.name as user_name, q.title as quiz_title 
        FROM payments p 
        JOIN users u ON p.user_id = u.id 
        LEFT JOIN quizzes q ON p.quiz_id = q.id 
        ORDER BY p.created_at DESC";
$payments = $pdo->query($sql)->fetchAll();
?>

<h3>Transaction History</h3>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Quiz</th>
                        <th>Transaction ID</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($payments as $p): ?>
                    <tr>
                        <td><?php echo $p['id']; ?></td>
                        <td><?php echo htmlspecialchars($p['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($p['quiz_title']); ?></td>
                        <td><?php echo htmlspecialchars($p['transaction_id']); ?></td>
                        <td>$<?php echo $p['amount']; ?></td>
                        <td><?php echo $p['created_at']; ?></td>
                        <td>
                            <span class="badge bg-<?php echo $p['payment_status'] == 'COMPLETED' ? 'success' : 'warning'; ?>">
                                <?php echo $p['payment_status']; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
