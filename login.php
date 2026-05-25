<?php
require_once __DIR__ . '/includes/init.php';

preventAuthAccess();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireValidCsrf();

    $email = sanitizeString($_POST['email'] ?? '', 150);
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember_me']);

    if (!isValidEmail($email)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if ($password === '') {
        $errors[] = 'Password is required.';
    }

    if (empty($errors)) {
        $user = findUserByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            loginUser($user, $remember);
            setFlash('success', 'Welcome back, ' . $user['full_name'] . '!');
            redirectToDashboard();
        }

        $errors[] = 'Invalid email or password.';
    }
}

$pageTitle = 'Login - SecureBookStore';
$activeNav = 'login';
$bodyClass = 'bg-light';
require_once ROOT_PATH . '/includes/header.php';
?>

<section class="section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="form-container fade-in">
                    <div class="text-center mb-4">
                        <i class="bi bi-shield-lock" style="font-size: 4rem; color: var(--secondary-blue);"></i>
                        <h2 class="form-title mt-3">Welcome Back</h2>
                        <p class="text-muted">Login to access your account</p>
                    </div>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger alert-custom">
                            <?php foreach ($errors as $error): ?>
                                <p class="mb-1"><?= e($error) ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?= baseUrl('login.php') ?>" novalidate>
                        <?= csrfField() ?>
                        <div class="mb-3">
                            <label for="email" class="form-label"><i class="bi bi-envelope me-2"></i>Email Address</label>
                            <input type="email" class="form-control form-control-custom" id="email" name="email"
                                   value="<?= e($_POST['email'] ?? '') ?>" placeholder="Enter your email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label"><i class="bi bi-lock me-2"></i>Password</label>
                            <input type="password" class="form-control form-control-custom" id="password" name="password"
                                   placeholder="Enter your password" required>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input form-check-input-custom" id="remember_me" name="remember_me"
                                    <?= isset($_POST['remember_me']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="remember_me">Remember me</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-submit">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login
                        </button>
                        <div class="text-center mt-4">
                            <p class="mb-0">Don't have an account?
                                <a href="<?= baseUrl('register.php') ?>" class="fw-bold" style="color: var(--secondary-blue);">Register here</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>
