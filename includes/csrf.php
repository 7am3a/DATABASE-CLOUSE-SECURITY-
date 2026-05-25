<?php
/**
 * CSRF protection
 */

function generateCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function validateCsrfToken(?string $token): bool
{
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}

function csrfField(): string
{
    $token = generateCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . e($token) . '">';
}

function requireValidCsrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!validateCsrfToken($token)) {
        setFlash('error', 'Invalid security token. Please try again.');
        redirect($_SERVER['HTTP_REFERER'] ?? baseUrl('index.php'));
    }
}
