
<?php
require_once 'includes/header.php';

// Get current user access if logged in
$user_access_ids = [];
if (isLoggedIn()) {
    $stmt = $pdo->prepare("SELECT quiz_id FROM user_quiz_access WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user_access_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Fetch all active quizzes
$quizzes = $pdo->query("SELECT q.*, c.name as category_name FROM quizzes q JOIN categories c ON q.category_id = c.id WHERE q.status = 'active' ORDER BY q.id DESC")->fetchAll();
?>

<!-- Page Header -->
<div class="row mb-5">
    <div class="col-12">
        <h1 class="dashboard-title">All Quizzes</h1>
        <p class="text-secondary">Browse and select quizzes to test your knowledge across various categories</p>
    </div>
</div>

<!-- Search and Filter Bar (Optional - can be implemented later) -->
<div class="card elevation-2 mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0">
                        <i class="material-icons-round text-secondary">search</i>
                    </span>
                    <input type="text" class="form-control border-start-0" placeholder="Search quizzes..." id="searchInput">
                    <button class="btn btn-outline-primary" type="button" onclick="filterQuizzes()">
                        Search
                    </button>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown">
                        <i class="material-icons-round me-1">filter_list</i>
                        Filter
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" onclick="sortBy('newest')">Newest First</a></li>
                        <li><a class="dropdown-item" href="#" onclick="sortBy('oldest')">Oldest First</a></li>
                        <li><a class="dropdown-item" href="#" onclick="sortBy('price_low')">Price: Low to High</a></li>
                        <li><a class="dropdown-item" href="#" onclick="sortBy('price_high')">Price: High to Low</a></li>
                        <li><a class="dropdown-item" href="#" onclick="sortBy('free')">Free Quizzes</a></li>
                        <li><a class="dropdown-item" href="#" onclick="sortBy('premium')">Premium Quizzes</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quizzes Grid -->
<div class="row" id="quizzesContainer">
    <?php if(count($quizzes) > 0): ?>
        <?php foreach($quizzes as $quiz): ?>
        <div class="col-xl-4 col-lg-6 col-md-6 mb-4 quiz-card-item">
            <div class="card quiz-card elevation-2 h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="badge" style="background: rgba(98, 0, 238, 0.1); color: #6200ee;">
                            <i class="material-icons-round me-1" style="font-size: 14px;">category</i>
                            <?php echo htmlspecialchars($quiz['category_name']); ?>
                        </span>
                        <?php if($quiz['price'] > 0): ?>
                            <span class="badge bg-success">
                                <i class="material-icons-round me-1" style="font-size: 14px;">workspace_premium</i>
                                $<?php echo number_format($quiz['price'], 2); ?>
                            </span>
                        <?php else: ?>
                            <span class="badge bg-info">
                                <i class="material-icons-round me-1" style="font-size: 14px;">free_breakfast</i>
                                Free
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <h5 class="quiz-title mb-3"><?php echo htmlspecialchars($quiz['title']); ?></h5>
                    
                    <p class="quiz-description text-secondary flex-grow-1 mb-4">
                        <?php echo htmlspecialchars(substr($quiz['description'], 0, 120)); ?>...
                    </p>
                    
                    <div class="quiz-meta d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <i class="material-icons-round me-2 text-secondary" style="font-size: 18px;">timer</i>
                            <span class="text-secondary"><?php echo $quiz['time_limit']; ?> min</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="material-icons-round me-2 text-secondary" style="font-size: 18px;">school</i>
                            <span class="text-secondary"><?php echo $quiz['pass_percentage']; ?>% to pass</span>
                        </div>
                    </div>
                    
                    <div class="mt-auto pt-3 border-top">
                        <?php if(!isLoggedIn()): ?>
                            <a href="login.php" class="btn btn-primary w-100 d-flex align-items-center justify-content-center">
                                <i class="material-icons-round me-2">login</i>
                                Login to Access
                            </a>
                        <?php elseif($quiz['price'] == 0 || in_array($quiz['id'], $user_access_ids)): ?>
                            <a href="attempt.php?quiz_id=<?php echo $quiz['id']; ?>" class="btn btn-primary w-100 d-flex align-items-center justify-content-center">
                                <i class="material-icons-round me-2">play_arrow</i>
                                Start Quiz
                            </a>
                        <?php else: ?>
                            <a href="buy.php?quiz_id=<?php echo $quiz['id']; ?>" class="btn btn-warning w-100 d-flex align-items-center justify-content-center">
                                <i class="material-icons-round me-2">shopping_cart</i>
                                Buy Now - $<?php echo number_format($quiz['price'], 2); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="material-icons-round" style="font-size: 64px;">quiz</i>
                </div>
                <h4 class="mb-3">No Quizzes Available</h4>
                <p class="text-secondary mb-4">Check back later for new quizzes or contact the administrator.</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// Basic filtering and sorting functionality
function filterQuizzes() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const cards = document.querySelectorAll('.quiz-card-item');
    
    cards.forEach(card => {
        const title = card.querySelector('.quiz-title').textContent.toLowerCase();
        const description = card.querySelector('.quiz-description').textContent.toLowerCase();
        const category = card.querySelector('.badge').textContent.toLowerCase();
        
        if (title.includes(searchTerm) || description.includes(searchTerm) || category.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

function sortBy(type) {
    const container = document.getElementById('quizzesContainer');
    const cards = Array.from(container.querySelectorAll('.quiz-card-item'));
    
    cards.sort((a, b) => {
        const priceA = parseFloat(a.querySelector('.badge.bg-success')?.textContent.replace('$', '') || 0);
        const priceB = parseFloat(b.querySelector('.badge.bg-success')?.textContent.replace('$', '') || 0);
        const titleA = a.querySelector('.quiz-title').textContent.toLowerCase();
        const titleB = b.querySelector('.quiz-title').textContent.toLowerCase();
        
        switch(type) {
            case 'newest':
                return cards.indexOf(b) - cards.indexOf(a);
            case 'oldest':
                return cards.indexOf(a) - cards.indexOf(b);
            case 'price_low':
                return priceA - priceB;
            case 'price_high':
                return priceB - priceA;
            case 'free':
                return priceB === 0 ? -1 : priceA === 0 ? 1 : 0;
            case 'premium':
                return priceA === 0 ? 1 : priceB === 0 ? -1 : 0;
            default:
                return titleA.localeCompare(titleB);
        }
    });
    
    // Reorder cards
    cards.forEach(card => container.appendChild(card));
}

// Add search on Enter key
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        filterQuizzes();
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
