
<?php
// user/includes/header.php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../config/security.php';

// Set active page for navigation
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Expand Your Knowledge</title>
    <meta name="description" content="Interactive quiz platform to test and enhance your knowledge">
    <meta name="keywords" content="quiz, education, learning, knowledge test">
    
    <!-- Material Design Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo BASE_URL; ?>assets/images/favicon.ico">
    
    <style>
        .material-icons-round {
            font-family: 'Material Icons Round';
            font-weight: normal;
            font-style: normal;
            font-size: 24px;
            line-height: 1;
            letter-spacing: normal;
            text-transform: none;
            display: inline-block;
            white-space: nowrap;
            word-wrap: normal;
            direction: ltr;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-surface elevation-2">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>">
                <i class="material-icons-round" style="color: #6200ee;">quiz</i>
                <span style="margin-left: 8px;"><?php echo APP_NAME; ?></span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'quizzes.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>user/quizzes.php">
                            <i class="material-icons-round me-1">explore</i>
                            Browse Quizzes
                        </a>
                    </li>
                    <?php if(isLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>user/dashboard.php">
                            <i class="material-icons-round me-1">dashboard</i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'leaderboard.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>user/leaderboard.php">
                            <i class="material-icons-round me-1">leaderboard</i>
                            Leaderboard
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if(isLoggedIn()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="user-avatar me-2" style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #6200ee, #9d46ff); display: flex; align-items: center; justify-content: center; color: white; font-weight: 500;">
                                    <?php echo strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)); ?>
                                </div>
                                <span><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item" href="<?php echo BASE_URL; ?>user/profile.php">
                                        <i class="material-icons-round me-2">person</i>
                                        Profile
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo BASE_URL; ?>user/settings.php">
                                        <i class="material-icons-round me-2">settings</i>
                                        Settings
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>user/logout.php">
                                        <i class="material-icons-round me-2">logout</i>
                                        Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page == 'login.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>user/login.php">
                                <i class="material-icons-round me-1">login</i>
                                Login
                            </a>
                        </li>
                        <li class="nav-item ms-2">
                            <a class="btn btn-primary btn-sm" href="<?php echo BASE_URL; ?>user/register.php" style="border-radius: 20px; padding: 8px 20px;">
                                <i class="material-icons-round me-1" style="font-size: 18px;">person_add</i>
                                Sign Up
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container py-4 fade-in">
