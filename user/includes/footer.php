
</main>

<footer class="bg-surface">
    <div class="container">
        <div class="row py-5">
            <div class="col-lg-4 mb-4 mb-lg-0">
                <div class="footer-logo mb-3">
                    <i class="material-icons-round" style="color: #6200ee; font-size: 32px;">quiz</i>
                    <h4 class="ms-2 mb-0"><?php echo APP_NAME; ?></h4>
                </div>
                <p class="footer-tagline mb-4">Expand your knowledge, one quiz at a time. Learn, test, and grow with our interactive platform.</p>
                
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
                    <a href="#" title="GitHub">
                        <i class="fab fa-github"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
                <h5 class="mb-3">Platform</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="<?php echo BASE_URL; ?>user/quizzes.php" class="text-secondary text-decoration-none">Browse Quizzes</a></li>
                    <li class="mb-2"><a href="<?php echo BASE_URL; ?>user/dashboard.php" class="text-secondary text-decoration-none">Dashboard</a></li>
                    <li class="mb-2"><a href="<?php echo BASE_URL; ?>leaderboard.php" class="text-secondary text-decoration-none">Leaderboard</a></li>
                    <li class="mb-2"><a href="<?php echo BASE_URL; ?>categories.php" class="text-secondary text-decoration-none">Categories</a></li>
                </ul>
            </div>
            
            <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
                <h5 class="mb-3">Account</h5>
                <ul class="list-unstyled">
                    <?php if(isLoggedIn()): ?>
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>user/profile.php" class="text-secondary text-decoration-none">Profile</a></li>
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>user/settings.php" class="text-secondary text-decoration-none">Settings</a></li>
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>user/logout.php" class="text-secondary text-decoration-none">Logout</a></li>
                    <?php else: ?>
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>user/login.php" class="text-secondary text-decoration-none">Login</a></li>
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>user/register.php" class="text-secondary text-decoration-none">Register</a></li>
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>user/forgot_password.php" class="text-secondary text-decoration-none">Forgot Password</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <div class="col-lg-4 col-md-4">
                <h5 class="mb-3">Legal</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="<?php echo BASE_URL; ?>privacy.php" class="text-secondary text-decoration-none">Privacy Policy</a></li>
                    <li class="mb-2"><a href="<?php echo BASE_URL; ?>terms.php" class="text-secondary text-decoration-none">Terms of Service</a></li>
                    <li class="mb-2"><a href="<?php echo BASE_URL; ?>cookies.php" class="text-secondary text-decoration-none">Cookie Policy</a></li>
                    <li class="mb-2"><a href="<?php echo BASE_URL; ?>contact.php" class="text-secondary text-decoration-none">Contact Us</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom pt-4 border-top">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <p class="mb-0 text-secondary">
                        &copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All Rights Reserved.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0 text-secondary">
                        Made with <i class="fas fa-heart" style="color: #dd2c00;"></i> for curious minds everywhere
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Material Design Ripple Effect -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add ripple effect to buttons
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
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
            `;
            
            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);
        });
    });
    
    // Animate progress bars
    const progressBars = document.querySelectorAll('.progress-bar');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0';
        setTimeout(() => {
            bar.style.transition = 'width 1s cubic-bezier(0.4, 0, 0.2, 1)';
            bar.style.width = width;
        }, 300);
    });
    
    // Add fade-in animation to cards
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('.card').forEach(card => {
        observer.observe(card);
    });
});

// Add CSS for ripple animation
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    .btn {
        position: relative;
        overflow: hidden;
    }
`;
document.head.appendChild(style);
</script>
</body>
</html>
