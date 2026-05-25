<?php
require_once __DIR__ . '/includes/init.php';

$pageTitle = 'Secure Online Bookstore - Home';
$activeNav = 'home';
require_once ROOT_PATH . '/includes/header.php';
?>

<section class="hero-section">
    <div class="container hero-content">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="hero-title">Secure Online Bookstore Management System</h1>
                <p class="hero-subtitle">
                    A secure and reliable platform for browsing, purchasing, and managing books
                    with enterprise-grade security features and a user-friendly interface.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="<?= baseUrl('register.php') ?>" class="btn btn-light btn-hero">
                        <i class="bi bi-person-plus me-2"></i>Get Started
                    </a>
                    <a href="<?= baseUrl('books.php') ?>" class="btn btn-outline-light btn-hero">
                        <i class="bi bi-book me-2"></i>Browse Books
                    </a>
                </div>
            </div>
            <div class="col-lg-4 d-none d-lg-block text-center">
                <i class="bi bi-shield-lock" style="font-size: 12rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</section>

<section class="features-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold text-primary-custom mb-3">Security Features</h2>
            <p class="lead text-muted">Our system implements industry-standard security measures</p>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-shield-check"></i></div>
                    <h4 class="feature-title">Secure Authentication</h4>
                    <p class="feature-description">Password hashing, sessions, and role-based access protect every account.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-database-lock"></i></div>
                    <h4 class="feature-title">SQL Injection Protection</h4>
                    <p class="feature-description">PDO prepared statements and input validation on all database queries.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-person-badge"></i></div>
                    <h4 class="feature-title">Role-Based Access</h4>
                    <p class="feature-description">Admins manage inventory; users browse and purchase with separate dashboards.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-server"></i></div>
                    <h4 class="feature-title">Secure Database</h4>
                    <p class="feature-description">Structured MySQL schema with foreign keys and transactional order processing.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-padding" style="background-color: var(--light-gray);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0 text-center">
                <i class="bi bi-book-half" style="font-size: 8rem; color: var(--secondary-blue);"></i>
            </div>
            <div class="col-lg-6">
                <h2 class="display-5 fw-bold text-primary-custom mb-4">About Our System</h2>
                <p class="lead mb-4">The Secure Online Bookstore Management System demonstrates modern PHP and MySQL development with security as a priority.</p>
                <ul class="list-unstyled">
                    <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>PHP, MySQL, Bootstrap 5, and responsive design</li>
                    <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>CSRF protection on all forms</li>
                    <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Admin book management with image uploads</li>
                    <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Shopping cart and secure checkout for registered users</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php if (!isLoggedIn()): ?>
<section class="section-padding bg-primary-custom text-white">
    <div class="container text-center">
        <h2 class="display-5 fw-bold mb-4">Ready to Get Started?</h2>
        <p class="lead mb-4">Join our secure platform today and explore our collection of books</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="<?= baseUrl('register.php') ?>" class="btn btn-light btn-lg fw-bold">
                <i class="bi bi-person-plus me-2"></i>Register Now
            </a>
            <a href="<?= baseUrl('login.php') ?>" class="btn btn-outline-light btn-lg fw-bold">
                <i class="bi bi-box-arrow-in-right me-2"></i>Login
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>
