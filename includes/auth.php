<?php
/**
 * Session-based authentication and role checks
 */

function loginUser(array $user, bool $remember = false): void
{
    session_regenerate_id(true);

    $_SESSION['user_id']    = (int) $user['id'];
    $_SESSION['full_name']  = $user['full_name'];
    $_SESSION['email']      = $user['email'];
    $_SESSION['role']       = $user['role'];

    if ($remember) {
        setRememberCookie((int) $user['id'], $user['email']);
    } else {
        clearRememberCookie();
    }
}

function logoutUser(): void
{
    $_SESSION = [];
    clearRememberCookie();

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    session_destroy();
}

function isLoggedIn(): bool
{
    return !empty($_SESSION['user_id']);
}

function isAdmin(): bool
{
    return isLoggedIn() && ($_SESSION['role'] ?? '') === 'admin';
}

function isUser(): bool
{
    return isLoggedIn() && ($_SESSION['role'] ?? '') === 'user';
}

function getUserId(): ?int
{
    return isLoggedIn() ? (int) $_SESSION['user_id'] : null;
}

function getFullName(): string
{
    return $_SESSION['full_name'] ?? 'User';
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        setFlash('error', 'Please log in to access this page.');
        redirect(baseUrl('login.php'));
    }
}

function requireAdmin(): void
{
    if (!isLoggedIn()) {
        setFlash('error', 'Please log in to access the admin area.');
        redirect(baseUrl('login.php'));
    }

    if (!isAdmin()) {
        setFlash('error', 'You do not have permission to access the admin area.');
        redirect(baseUrl('user/index.php'));
    }
}

function requireUser(): void
{
    if (!isLoggedIn()) {
        setFlash('error', 'Please log in to access your dashboard.');
        redirect(baseUrl('login.php'));
    }

    if (isAdmin()) {
        redirect(baseUrl('admin/index.php'));
    }
}

function preventAuthAccess(): void
{
    if (isLoggedIn()) {
        redirectToDashboard();
    }
}

function setRememberCookie(int $userId, string $email): void
{
    $payload = $userId . '|' . $email;
    $signature = hash_hmac('sha256', $payload, REMEMBER_SECRET);
    $value = base64_encode($payload . '|' . $signature);

    setcookie('remember_me', $value, [
        'expires'  => time() + REMEMBER_DURATION,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function clearRememberCookie(): void
{
    setcookie('remember_me', '', [
        'expires'  => time() - 3600,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function checkRememberMe(): void
{
    if (isLoggedIn() || empty($_COOKIE['remember_me'])) {
        return;
    }

    $decoded = base64_decode($_COOKIE['remember_me'], true);
    if ($decoded === false) {
        clearRememberCookie();
        return;
    }

    $parts = explode('|', $decoded);
    if (count($parts) !== 3) {
        clearRememberCookie();
        return;
    }

    [$userId, $email, $signature] = $parts;
    $payload = $userId . '|' . $email;
    $expected = hash_hmac('sha256', $payload, REMEMBER_SECRET);

    if (!hash_equals($expected, $signature)) {
        clearRememberCookie();
        return;
    }

    $db = getDb();
    $stmt = $db->prepare('SELECT id, full_name, email, role FROM users WHERE id = ? AND email = ? LIMIT 1');
    $stmt->execute([(int) $userId, $email]);
    $user = $stmt->fetch();

    if ($user) {
        loginUser($user, true);
    } else {
        clearRememberCookie();
    }
}

function findUserByEmail(string $email): ?array
{
    $db = getDb();
    $stmt = $db->prepare('SELECT id, full_name, email, password, role FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    return $user ?: null;
}
