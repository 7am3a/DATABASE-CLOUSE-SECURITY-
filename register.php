<?php
require_once __DIR__ . '/includes/init.php';

preventAuthAccess();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireValidCsrf();

    $fullName = sanitizeString($_POST['full_name'] ?? '', 100);
    $email = sanitizeString($_POST['email'] ?? '', 150);
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $terms = isset($_POST['terms']);

    if (strlen($fullName) < 2) {
        $errors[] = 'Full name must be at least 2 characters.';
    }

    if (!isValidEmail($email)) {
        $errors[] = 'Please enter a valid email address.';
    }

    $passwordCheck = validatePassword($password);
    if (!$passwordCheck['valid']) {
        $errors[] = $passwordCheck['message'];
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }

    if (!$terms) {
        $errors[] = 'You must agree to the Terms and Conditions.';
    }

    if (empty($errors)) {
        $db = getDb();
        $check = $db->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $check->execute([$email]);

        if ($check->fetch()) {
            $errors[] = 'An account with this email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $insert = $db->prepare('INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)');
            $insert->execute([$fullName, $email, $hash, 'user']);

            $user = findUserByEmail($email);
            if ($user) {
                unset($user['password']);
                loginUser($user, false);
                setFlash('success', 'Account created successfully! Welcome to SecureBookStore.');
                redirect(baseUrl('user/index.php'));
            }
        }
    }
}

$pageTitle = 'Register - SecureBookStore';
$activeNav = 'register';
$bodyClass = 'bg-light';
require_once ROOT_PATH . '/includes/header.php';
?>

<section class="section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="form-container fade-in">
                    <div class="text-center mb-4">
                        <i class="bi bi-person-plus" style="font-size: 4rem; color: var(--secondary-blue);"></i>
                        <h2 class="form-title mt-3">Create Account</h2>
                        <p class="text-muted">Join our secure bookstore today</p>
                    </div>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger alert-custom">
                            <ul class="mb-0 ps-3">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= e($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?= baseUrl('register.php') ?>" novalidate>
                        <?= csrfField() ?>
                        <div class="mb-3">
                            <label for="full_name" class="form-label"><i class="bi bi-person me-2"></i>Full Name</label>
                            <input type="text" class="form-control form-control-custom" id="full_name" name="full_name"
                                   value="<?= e($_POST['full_name'] ?? '') ?>" placeholder="Enter your full name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label"><i class="bi bi-envelope me-2"></i>Email Address</label>
                            <input type="email" class="form-control form-control-custom" id="email" name="email"
                                   value="<?= e($_POST['email'] ?? '') ?>" placeholder="Enter your email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label"><i class="bi bi-lock me-2"></i>Password</label>
                            <input type="password" class="form-control form-control-custom" id="password" name="password"
                                   placeholder="Create a password" required>
                            <small class="form-text text-muted">Minimum 8 characters with uppercase, lowercase, and numbers.</small>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label"><i class="bi bi-lock-fill me-2"></i>Confirm Password</label>
                            <input type="password" class="form-control form-control-custom" id="confirm_password" name="confirm_password"
                                   placeholder="Confirm your password" required>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input form-check-input-custom" id="terms" name="terms" required
                                    <?= isset($_POST['terms']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="terms">I agree to the Terms and Conditions</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-submit">
                            <i class="bi bi-person-plus me-2"></i>Create Account
                        </button>
                        <div class="text-center mt-4">
                            <p class="mb-0">Already have an account?
                                <a href="<?= baseUrl('login.php') ?>" class="fw-bold" style="color: var(--secondary-blue);">Login here</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>
