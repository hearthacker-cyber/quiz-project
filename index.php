
<?php
// Start session
session_start();

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'quiz_platform');
define('DB_USER', 'root');
define('DB_PASS', '');
define('APP_NAME', 'Quiz Master');
define('BASE_URL', 'http://localhost/quiz/');

// Connect to Database
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Get current user ID
$user_id = $_SESSION['user_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Test Your Knowledge</title>
    <meta name="description" content="Interactive quiz platform to test and enhance your knowledge across various categories">
    <meta name="keywords" content="quiz, education, learning, knowledge test, online quiz, quiz platform">
    
    <!-- Material Design Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        /* Add this to your existing CSS in the CTA section */

        :root {
            --primary-color: #6200ee;
            --primary-light: #9d46ff;
            --primary-dark: #0a00b6;
            --secondary-color: #03dac6;
            --secondary-light: #66fff9;
            --secondary-dark: #00a896;
            --surface-color: #ffffff;
            --background-color: #f5f5f5;
            --error-color: #b00020;
            --success-color: #00c853;
            --warning-color: #ffab00;
            --text-primary: #212121;
            --text-secondary: #666666;
            --text-disabled: #9e9e9e;
            --border-radius: 8px;
            --border-radius-lg: 16px;
            --border-radius-xl: 24px;
            --shadow-1: 0 2px 4px rgba(0,0,0,0.1);
            --shadow-2: 0 4px 8px rgba(0,0,0,0.12);
            --shadow-3: 0 8px 16px rgba(0,0,0,0.14);
            --shadow-4: 0 16px 32px rgba(0,0,0,0.16);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --hero-gradient: linear-gradient(135deg, #6200ee 0%, #9d46ff 50%, #2575fc 100%);
            --hero-gradient-2: linear-gradient(135deg, #0a00b6 0%, #6200ee 50%, #9d46ff 100%);
            --card-gradient-1: linear-gradient(135deg, #6200ee 0%, #03dac6 100%);
            --card-gradient-2: linear-gradient(135deg, #ff7e5f 0%, #feb47b 100%);
            --card-gradient-3: linear-gradient(135deg, #00c853 0%, #64dd17 100%);
            --card-gradient-4: linear-gradient(135deg, #ffab00 0%, #ffd600 100%);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--background-color);
            color: var(--text-primary);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        /* Navigation */
        .homepage-nav {
            background: transparent;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            transition: all 0.3s ease;
            padding: 1rem 0;
        }
        
        .homepage-nav.scrolled {
            background: var(--surface-color);
            box-shadow: var(--shadow-2);
            position: fixed;
        }
        
        .navbar-brand {
            display: flex;
            align-items: center;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: 500;
            color: white;
        }
        
        .homepage-nav.scrolled .navbar-brand {
            color: var(--primary-color);
        }
        
        .navbar-brand i {
            margin-right: 0.5rem;
            font-size: 1.75rem;
        }
        
        .navbar-toggler {
            border: 2px solid rgba(255, 255, 255, 0.3);
            background: transparent;
        }
        
        .homepage-nav.scrolled .navbar-toggler {
            border-color: rgba(0, 0, 0, 0.1);
        }
        
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.75%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        
        .homepage-nav.scrolled .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%280, 0, 0, 0.55%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        
        .navbar-nav {
            display: flex;
            list-style: none;
            align-items: center;
        }
        
        .nav-item {
            margin: 0 0.25rem;
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            padding: 0.75rem 1rem;
            border-radius: var(--border-radius);
            transition: var(--transition);
            display: flex;
            align-items: center;
            font-weight: 500;
            position: relative;
        }
        
        .homepage-nav.scrolled .nav-link {
            color: var(--text-primary);
        }
        
        .nav-link i {
            margin-right: 0.5rem;
            font-size: 1.1rem;
        }
        
        .nav-link:hover, .nav-link.active {
            background: rgba(255, 255, 255, 0.15);
            color: white;
        }
        
        .homepage-nav.scrolled .nav-link:hover,
        .homepage-nav.scrolled .nav-link.active {
            background: rgba(98, 0, 238, 0.1);
            color: var(--primary-color);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 20px;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(98, 0, 238, 0.3);
        }
        
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
        }
        
        /* Hero Section */
        .hero-section {
            background: var(--hero-gradient);
            color: white;
            padding: 100px 0;
            position: relative;
            overflow: hidden;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.1)" d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,192C672,181,768,139,864,128C960,117,1056,139,1152,149.3C1248,160,1344,160,1392,160L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            background-position: bottom;
            opacity: 0.1;
            animation: waveAnimation 20s linear infinite;
        }
        
        @keyframes waveAnimation {
            0% { background-position-x: 0; }
            100% { background-position-x: 1440px; }
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .hero-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 900;
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        
        .hero-subtitle {
            font-size: 1.25rem;
            opacity: 0.9;
            margin-bottom: 2rem;
            max-width: 600px;
        }
        
        .hero-badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 0.9rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(255,255,255,0.3);
        }
        
        .hero-stats {
            display: flex;
            gap: 2rem;
            margin-top: 3rem;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }
        
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .hero-image {
            position: relative;
            z-index: 2;
            animation: floatAnimation 6s ease-in-out infinite;
        }
        
        @keyframes floatAnimation {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        
        .hero-shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            animation: floatShape 20s linear infinite;
        }
        
        .shape-1 {
            width: 300px;
            height: 300px;
            top: -150px;
            right: -150px;
            animation-delay: 0s;
        }
        
        .shape-2 {
            width: 200px;
            height: 200px;
            bottom: -100px;
            left: -100px;
            animation-delay: -5s;
        }
        
        .shape-3 {
            width: 150px;
            height: 150px;
            top: 50%;
            right: 20%;
            animation-delay: -10s;
        }
        
        @keyframes floatShape {
            0% { transform: translate(0, 0) rotate(0deg); }
            100% { transform: translate(100px, 100px) rotate(360deg); }
        }
        
        .cta-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .btn-light {
            background: white;
            color: var(--primary-color);
            font-weight: 600;
            padding: 0.875rem 2rem;
            border-radius: var(--border-radius);
            border: none;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
        }
        
        .btn-light:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(255,255,255,0.2);
            color: var(--primary-color);
        }
        
        .btn-outline-light {
            border: 2px solid white;
            color: white;
            font-weight: 600;
            padding: 0.875rem 2rem;
            border-radius: var(--border-radius);
            background: transparent;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
        }
        
        .btn-outline-light:hover {
            background: white;
            color: var(--primary-color);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(255,255,255,0.2);
        }
        
        /* Features Section */
        .features-section {
            padding: 100px 0;
            background: var(--background-color);
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .section-title h2 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 1rem;
            position: relative;
            display: inline-block;
        }
        
        .section-title h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: var(--primary-color);
            border-radius: 2px;
        }
        
        .section-subtitle {
            color: var(--text-secondary);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .feature-card {
            background: var(--surface-color);
            border-radius: var(--border-radius-lg);
            padding: 2.5rem 2rem;
            text-align: center;
            height: 100%;
            transition: all 0.4s ease;
            border: 1px solid rgba(0,0,0,0.05);
            position: relative;
            overflow: hidden;
        }
        
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--card-gradient-1);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .feature-card:hover::before {
            opacity: 1;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-4);
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .feature-card:nth-child(1) .feature-icon { background: var(--card-gradient-1); }
        .feature-card:nth-child(2) .feature-icon { background: var(--card-gradient-2); }
        .feature-card:nth-child(3) .feature-icon { background: var(--card-gradient-3); }
        .feature-card:nth-child(4) .feature-icon { background: var(--card-gradient-4); }
        
        .feature-icon::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.1);
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        
        .feature-card:hover .feature-icon::after {
            transform: translateX(0);
        }
        
        .feature-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }
        
        .feature-description {
            color: var(--text-secondary);
            line-height: 1.6;
        }
        
        /* Categories Section */
        .categories-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #f8f9ff 0%, #f1f4ff 100%);
        }
        
        .category-card {
            background: var(--surface-color);
            border-radius: var(--border-radius-lg);
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.05);
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }
        
        .category-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--hero-gradient);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1;
        }
        
        .category-card:hover::before {
            opacity: 0.05;
        }
        
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-3);
        }
        
        .category-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: rgba(98, 0, 238, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 1.75rem;
            color: var(--primary-color);
            position: relative;
            z-index: 2;
        }
        
        .category-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 2;
        }
        
        .category-count {
            color: var(--text-secondary);
            font-size: 0.9rem;
            position: relative;
            z-index: 2;
        }
        
        .btn-primary-lg {
            background: var(--primary-color);
            color: white;
            padding: 1rem 2.5rem;
            border-radius: var(--border-radius);
            border: none;
            font-weight: 600;
            font-size: 1.1rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: var(--transition);
        }
        
        .btn-primary-lg:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(98, 0, 238, 0.3);
            color: white;
        }
        
        /* CTA Section */
        .cta-section {
            padding: 100px 0;
            background: var(--hero-gradient-2);
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.05)" d="M0,288L48,272C96,256,192,224,288,197.3C384,171,480,149,576,165.3C672,181,768,235,864,250.7C960,267,1056,245,1152,250.7C1248,256,1344,288,1392,304L1440,320L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            background-position: bottom;
            animation: waveAnimation 15s linear infinite;
        }
        
        .cta-content {
            position: relative;
            z-index: 2;
            text-align: center;
        }
        
        .cta-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
        }
        
        .cta-description {
            font-size: 1.1rem;
            opacity: 0.9;
            max-width: 700px;
            margin: 0 auto 2.5rem;
        }
        
        /* Testimonials Section */
        .testimonials-section {
            padding: 100px 0;
            background: var(--surface-color);
        }
        
        .testimonial-card {
            background: white;
            border-radius: var(--border-radius-lg);
            padding: 2rem;
            height: 100%;
            box-shadow: var(--shadow-2);
            position: relative;
        }
        
        .testimonial-card::before {
            content: '"';
            position: absolute;
            top: 1rem;
            left: 1.5rem;
            font-size: 4rem;
            color: var(--primary-color);
            opacity: 0.1;
            font-family: Georgia, serif;
            line-height: 1;
        }
        
        .testimonial-content {
            position: relative;
            z-index: 2;
            margin-bottom: 1.5rem;
            font-style: italic;
            color: var(--text-secondary);
            line-height: 1.6;
        }
        
        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .author-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6200ee, #9d46ff);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.25rem;
        }
        
        .author-info h6 {
            margin-bottom: 0.25rem;
            font-weight: 600;
        }
        
        .author-info p {
            color: var(--text-secondary);
            font-size: 0.875rem;
            margin-bottom: 0;
        }
        
        /* Footer */
        .home-footer {
            background: #2d3436;
            color: white;
            padding: 80px 0 0;
        }
        
        .footer-logo {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .footer-logo i {
            font-size: 2rem;
            color: var(--primary-light);
            margin-right: 0.75rem;
        }
        
        .footer-logo h4 {
            margin-bottom: 0;
            font-weight: 500;
            color: white;
        }
        
        .footer-tagline {
            color: rgba(255,255,255,0.7);
            font-size: 0.875rem;
        }
        
        .social-links {
            display: flex;
            gap: 1rem;
            margin: 1.5rem 0;
        }
        
        .social-links a {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255,255,255,0.7);
            transition: var(--transition);
            text-decoration: none;
        }
        
        .social-links a:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }
        
        .home-footer h5 {
            color: white;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        
        .home-footer ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .home-footer ul li {
            margin-bottom: 0.75rem;
        }
        
        .home-footer ul li a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: var(--transition);
        }
        
        .home-footer ul li a:hover {
            color: white;
        }
        
        .newsletter-form .input-group {
            background: rgba(255,255,255,0.1);
            border-radius: var(--border-radius);
            overflow: hidden;
        }
        
        .newsletter-form input {
            background: transparent;
            border: none;
            color: white;
            padding: 0.75rem 1rem;
        }
        
        .newsletter-form input::placeholder {
            color: rgba(255,255,255,0.5);
        }
        
        .newsletter-form button {
            background: var(--primary-color);
            border: none;
            color: white;
            padding: 0 1.5rem;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .newsletter-form button:hover {
            background: var(--primary-dark);
        }
        
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 2rem;
            margin-top: 3rem;
            text-align: center;
            color: rgba(255,255,255,0.5);
            font-size: 0.875rem;
        }
        
        .footer-bottom i.fa-heart {
            color: #dd2c00;
            animation: heartbeat 1.5s ease-in-out infinite;
        }
        
        @keyframes heartbeat {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-stats {
                flex-wrap: wrap;
                gap: 1.5rem;
            }
            
            .stat-number {
                font-size: 2rem;
            }
            
            .section-title h2 {
                font-size: 2rem;
            }
        }
        
        @media (max-width: 768px) {
            .hero-section {
                padding: 60px 0;
                text-align: center;
            }
            
            .hero-title {
                font-size: 2rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .cta-buttons .btn {
                width: 100%;
                max-width: 300px;
            }
            
            .navbar-nav {
                background: var(--surface-color);
                padding: 1rem;
                border-radius: var(--border-radius);
                margin-top: 1rem;
            }
            
            .nav-link {
                color: var(--text-primary) !important;
            }
            
            .nav-link:hover {
                background: rgba(98, 0, 238, 0.1);
                color: var(--primary-color) !important;
            }
        }
        
        @media (max-width: 576px) {
            .hero-stats {
                flex-direction: column;
                gap: 1rem;
            }
            
            .feature-card {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="homepage-nav navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>">
                <i class="material-icons-round">quiz</i>
                <?php echo APP_NAME; ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo BASE_URL; ?>">
                            <i class="material-icons-round me-1">home</i>
                            Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>user/quizzes.php">
                            <i class="material-icons-round me-1">explore</i>
                            Browse Quizzes
                        </a>
                    </li>
                    <?php if(isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>user/dashboard.php">
                                <i class="material-icons-round me-1">dashboard</i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                <div class="user-avatar me-2" style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #6200ee, #9d46ff); display: flex; align-items: center; justify-content: center; color: white; font-weight: 500;">
                                    <?php echo strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)); ?>
                                </div>
                                <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>user/profile.php">Profile</a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>user/settings.php">Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>user/logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>user/login.php">
                                <i class="material-icons-round me-1">login</i>
                                Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary ms-2" href="<?php echo BASE_URL; ?>user/register.php">
                                <i class="material-icons-round me-1" style="font-size: 18px;">person_add</i>
                                Sign Up
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right" data-aos-duration="1000">
                    <div class="hero-content">
                        <span class="hero-badge">
                            <i class="material-icons-round me-2">rocket_launch</i>
                            #1 Quiz Platform
                        </span>
                        
                        <h1 class="hero-title">
                            Test Your Knowledge,<br>
                            <span style="color: #ffd600;">Expand Your Mind</span>
                        </h1>
                        
                        <p class="hero-subtitle">
                            Join thousands of learners who are improving their skills with our interactive quizzes. 
                            From general knowledge to professional certifications, we've got you covered.
                        </p>
                        
                        <div class="cta-buttons">
                            <?php if(isLoggedIn()): ?>
                                <a href="<?php echo BASE_URL; ?>user/dashboard.php" class="btn btn-light btn-lg">
                                    <i class="material-icons-round me-2">dashboard</i>
                                    Go to Dashboard
                                </a>
                                <a href="<?php echo BASE_URL; ?>user/quizzes.php" class="btn btn-outline-light btn-lg">
                                    <i class="material-icons-round me-2">play_arrow</i>
                                    Start Quiz
                                </a>
                            <?php else: ?>
                                <a href="<?php echo BASE_URL; ?>user/register.php" class="btn btn-light btn-lg">
                                    <i class="material-icons-round me-2">person_add</i>
                                    Get Started Free
                                </a>
                                <a href="<?php echo BASE_URL; ?>user/login.php" class="btn btn-outline-light btn-lg">
                                    <i class="material-icons-round me-2">login</i>
                                    Sign In
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="hero-stats" data-aos="fade-up" data-aos-delay="300">
                            <?php
                            // Get stats from database
                            $totalQuizzes = $pdo->query("SELECT COUNT(*) FROM quizzes WHERE status = 'active'")->fetchColumn();
                            $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
                            $totalAttempts = $pdo->query("SELECT COUNT(*) FROM quiz_attempts WHERE status = 'completed'")->fetchColumn();
                            ?>
                            <div class="stat-item">
                                <div class="stat-number" data-count="<?php echo $totalQuizzes; ?>">0</div>
                                <div class="stat-label">Quizzes</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number" data-count="<?php echo $totalUsers; ?>">0</div>
                                <div class="stat-label">Users</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number" data-count="<?php echo $totalAttempts; ?>">0</div>
                                <div class="stat-label">Attempts</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                    <div class="hero-image text-center">
                        <div style="position: relative; display: inline-block;">
                            <div class="hero-shape shape-1"></div>
                            <div class="hero-shape shape-2"></div>
                            <div class="hero-shape shape-3"></div>
                            <div style="background: white; padding: 30px; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); display: inline-block;">
                                <i class="material-icons-round" style="font-size: 200px; color: #6200ee;">quiz</i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>Why Choose Our Platform</h2>
                <p class="section-subtitle">We provide the best learning experience with interactive quizzes and comprehensive analytics</p>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="material-icons-round">quiz</i>
                        </div>
                        <h4 class="feature-title">Interactive Quizzes</h4>
                        <p class="feature-description">Engage with carefully crafted quizzes that test your knowledge and provide instant feedback.</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="material-icons-round">assessment</i>
                        </div>
                        <h4 class="feature-title">Detailed Analytics</h4>
                        <p class="feature-description">Track your progress with comprehensive analytics and performance reports.</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="material-icons-round">workspace_premium</i>
                        </div>
                        <h4 class="feature-title">Certificates</h4>
                        <p class="feature-description">Earn certificates upon successful completion to showcase your achievements.</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="material-icons-round">devices</i>
                        </div>
                        <h4 class="feature-title">Mobile Friendly</h4>
                        <p class="feature-description">Access quizzes anytime, anywhere with our fully responsive design.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Categories Section -->
    <section class="categories-section">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>Popular Categories</h2>
                <p class="section-subtitle">Explore quizzes across various categories to enhance your knowledge</p>
            </div>
            
            <div class="row g-4">
                <?php
                $categories = $pdo->query("SELECT c.*, COUNT(q.id) as quiz_count FROM categories c LEFT JOIN quizzes q ON c.id = q.category_id AND q.status = 'active' WHERE c.status = 'active' GROUP BY c.id ORDER BY quiz_count DESC LIMIT 6")->fetchAll();
                $index = 0;
                
                foreach($categories as $category):
                ?>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                    <div class="category-card" onclick="window.location.href='<?php echo BASE_URL; ?>user/quizzes.php?category=<?php echo $category['id']; ?>'">
                        <div class="category-icon">
                            <i class="material-icons-round">
                                <?php 
                                $icons = ['category', 'school', 'code', 'science', 'history', 'language'];
                                echo $icons[$category['id'] % count($icons)];
                                ?>
                            </i>
                        </div>
                        <h4 class="category-title"><?php echo htmlspecialchars($category['name']); ?></h4>
                        <p class="category-count"><?php echo $category['quiz_count']; ?> Quizzes</p>
                    </div>
                </div>
                <?php $index++; endforeach; ?>
            </div>
            
            <div class="text-center mt-5" data-aos="fade-up" data-aos-delay="500">
                <a href="<?php echo BASE_URL; ?>user/quizzes.php" class="btn-primary-lg">
                    <i class="material-icons-round me-2">explore</i>
                    Browse All Categories
                </a>
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content" data-aos="zoom-in">
                <h2 class="cta-title">Ready to Start Learning?</h2>
                <p class="cta-description">
                    Join our community of learners today. Whether you're preparing for exams, 
                    enhancing your skills, or just having fun, we have the perfect quizzes for you.
                </p>
                
                <div class="cta-buttons d-flex justify-content-center flex-wrap gap-3">
                    <?php if(isLoggedIn()): ?>
                        <a href="<?php echo BASE_URL; ?>user/quizzes.php" class="btn btn-light btn-lg">
                            <i class="material-icons-round me-2">play_arrow</i>
                            Start a Quiz
                        </a>
                        <a href="<?php echo BASE_URL; ?>user/dashboard.php" class="btn btn-outline-light btn-lg">
                            <i class="material-icons-round me-2">dashboard</i>
                            View Dashboard
                        </a>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>user/register.php" class="btn btn-light btn-lg">
                            <i class="material-icons-round me-2">person_add</i>
                            Create Free Account
                        </a>
                        <a href="<?php echo BASE_URL; ?>user/login.php" class="btn btn-outline-light btn-lg">
                            <i class="material-icons-round me-2">login</i>
                            Sign In Now
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <!-- Testimonials Section -->
    <section class="testimonials-section">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>What Our Users Say</h2>
                <p class="section-subtitle">Hear from learners who transformed their knowledge with our platform</p>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            This platform completely changed how I prepare for exams. The quizzes are challenging yet rewarding, and the analytics help me identify my weak areas.
                        </div>
                        <div class="testimonial-author">
                            <div class="author-avatar">JS</div>
                            <div class="author-info">
                                <h6>John Smith</h6>
                                <p>Student</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            As a teacher, I recommend this platform to all my students. The quality of questions is excellent, and the instant feedback helps in rapid learning.
                        </div>
                        <div class="testimonial-author">
                            <div class="author-avatar">SR</div>
                            <div class="author-info">
                                <h6>Sarah Johnson</h6>
                                <p>Educator</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            The certificate feature is amazing! It helped me add valuable credentials to my resume. The interface is intuitive and mobile-friendly.
                        </div>
                        <div class="testimonial-author">
                            <div class="author-avatar">MD</div>
                            <div class="author-info">
                                <h6>Michael Davis</h6>
                                <p>Professional</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="home-footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <div class="footer-logo mb-3">
                        <i class="material-icons-round">quiz</i>
                        <h4><?php echo APP_NAME; ?></h4>
                    </div>
                    <p class="footer-tagline mb-4">
                        Expand your knowledge, one quiz at a time. Learn, test, and grow with our interactive platform.
                    </p>
                    
                    <div class="social-links">
                        <a href="#" title="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" title="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" title="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
                    <h5>Platform</h5>
                    <ul>
                        <li><a href="<?php echo BASE_URL; ?>user/quizzes.php">Browse Quizzes</a></li>
                        <li><a href="<?php echo BASE_URL; ?>user/dashboard.php">Dashboard</a></li>
                        <li><a href="#">Leaderboard</a></li>
                        <li><a href="#">Categories</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
                    <h5>Account</h5>
                    <ul>
                        <?php if(isLoggedIn()): ?>
                            <li><a href="<?php echo BASE_URL; ?>user/profile.php">Profile</a></li>
                            <li><a href="<?php echo BASE_URL; ?>user/settings.php">Settings</a></li>
                            <li><a href="<?php echo BASE_URL; ?>user/logout.php">Logout</a></li>
                        <?php else: ?>
                            <li><a href="<?php echo BASE_URL; ?>user/login.php">Login</a></li>
                            <li><a href="<?php echo BASE_URL; ?>user/register.php">Register</a></li>
                            <li><a href="<?php echo BASE_URL; ?>user/forgot_password.php">Forgot Password</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="col-lg-4 col-md-4">
                    <h5>Stay Updated</h5>
                    <p class="footer-tagline mb-3">Subscribe to get updates on new quizzes and features</p>
                    <div class="newsletter-form">
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Your email address">
                            <button class="btn" type="button">
                                <i class="material-icons-round">send</i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <p class="mb-0">
                            &copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All Rights Reserved.
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p class="mb-0">
                            Made with <i class="fas fa-heart"></i> for curious minds everywhere
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Initialize AOS (Animate On Scroll)
    AOS.init({
        duration: 800,
        once: true,
        offset: 100
    });
    
    // Counter animation for stats
    function animateCounter(element) {
        const target = parseInt(element.getAttribute('data-count'));
        const duration = 2000;
        const increment = target / (duration / 16);
        let current = 0;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            element.textContent = Math.floor(current) + '+';
        }, 16);
    }
    
    // Animate counters when in view
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const statNumbers = entry.target.querySelectorAll('.stat-number');
                statNumbers.forEach(stat => {
                    animateCounter(stat);
                });
            }
        });
    }, { threshold: 0.5 });
    
    const heroSection = document.querySelector('.hero-section');
    if (heroSection) {
        observer.observe(heroSection);
    }
    
    // Navbar scroll effect
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.homepage-nav');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
    
    // Add ripple effect to buttons
    document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('.btn, .category-card');
        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
                if (this.classList.contains('category-card')) return;
                
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.cssText = `
                    position: absolute;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.7);
                    transform: scale(0);
                    animation: ripple-animation 0.6s linear;
                    width: ${size}px;
                    height: ${size}px;
                    top: ${y}px;
                    left: ${x}px;
                    pointer-events: none;
                `;
                
                this.style.position = 'relative';
                this.style.overflow = 'hidden';
                this.appendChild(ripple);
                setTimeout(() => ripple.remove(), 600);
            });
        });
        
        // Add ripple animation CSS
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple-animation {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    });
    </script>
</body>
</html>
