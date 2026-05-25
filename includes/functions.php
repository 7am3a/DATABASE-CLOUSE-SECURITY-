<?php
/**
 * Shared helper functions
 */

/**
 * Escape HTML output
 */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect and exit
 */
function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/**
 * Get base URL path relative to document root
 */
function baseUrl(string $path = ''): string
{
    static $base = null;

    if ($base === null) {
        $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $dir = dirname($script);
        if ($dir === '/' || $dir === '.') {
            $dir = '';
        }
        if (preg_match('#/(admin|user)$#', $dir)) {
            $dir = dirname($dir);
        }
        $base = rtrim($dir, '/');
    }

    $path = ltrim($path, '/');
    if ($path === '') {
        return $base === '' ? '/' : $base . '/';
    }

    return ($base === '' ? '' : $base) . '/' . $path;
}

/**
 * Asset URL helper
 */
function asset(string $path): string
{
    return baseUrl('assets/' . ltrim($path, '/'));
}

/**
 * Sanitize string input
 */
function sanitizeString(string $value, int $maxLength = 255): string
{
    $value = trim($value);
    $value = strip_tags($value);

    if (mb_strlen($value) > $maxLength) {
        $value = mb_substr($value, 0, $maxLength);
    }

    return $value;
}

/**
 * Validate email format
 */
function isValidEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate password: min 8 chars, uppercase, lowercase, number
 */
function validatePassword(string $password): array
{
    if (strlen($password) < 8) {
        return ['valid' => false, 'message' => 'Password must be at least 8 characters long.'];
    }

    if (!preg_match('/[A-Z]/', $password)) {
        return ['valid' => false, 'message' => 'Password must contain at least one uppercase letter.'];
    }

    if (!preg_match('/[a-z]/', $password)) {
        return ['valid' => false, 'message' => 'Password must contain at least one lowercase letter.'];
    }

    if (!preg_match('/[0-9]/', $password)) {
        return ['valid' => false, 'message' => 'Password must contain at least one number.'];
    }

    return ['valid' => true, 'message' => ''];
}

/**
 * Validate positive decimal price
 */
function isValidPrice($price): bool
{
    return is_numeric($price) && (float) $price > 0;
}

/**
 * Validate positive integer stock
 */
function isValidStock($stock): bool
{
    return filter_var($stock, FILTER_VALIDATE_INT) !== false && (int) $stock >= 0;
}

/**
 * Upload book cover image
 */
function uploadCoverImage(array $file, ?string $existingImage = null): array
{
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => true, 'filename' => $existingImage ?? 'default-cover.svg'];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload failed. Please try again.'];
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        return ['success' => false, 'message' => 'Cover image must not exceed 2MB.'];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    if (!isset($allowed[$mime])) {
        return ['success' => false, 'message' => 'Only JPG, PNG, and WEBP images are allowed.'];
    }

    $uploadDir = ROOT_PATH . '/assets/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $filename = uniqid('book_', true) . '.' . $allowed[$mime];
    $destination = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => false, 'message' => 'Could not save uploaded file.'];
    }

    if ($existingImage && !in_array($existingImage, ['default-cover.png', 'default-cover.svg'], true)) {
        $oldPath = $uploadDir . $existingImage;
        if (is_file($oldPath)) {
            unlink($oldPath);
        }
    }

    return ['success' => true, 'filename' => $filename];
}

/**
 * Delete book cover file from disk
 */
function deleteCoverFile(?string $filename): void
{
    if (!$filename || in_array($filename, ['default-cover.png', 'default-cover.svg'], true)) {
        return;
    }

    $path = ROOT_PATH . '/assets/uploads/' . $filename;
    if (is_file($path)) {
        unlink($path);
    }
}

/**
 * Book cover image URL
 */
function coverUrl(?string $filename): string
{
    $filename = $filename ?: 'default-cover.svg';

    if ($filename === 'default-cover.png' || $filename === 'default-cover.svg') {
        return asset('images/default-cover.svg');
    }

    return asset('uploads/' . $filename);
}

/**
 * Pagination offset
 */
function paginationOffset(int $page, int $perPage): int
{
    return max(0, ($page - 1) * $perPage);
}

/**
 * Build pagination query string preserving search
 */
function paginationQuery(array $params, int $page): string
{
    $params['page'] = $page;
    return '?' . http_build_query($params);
}

/**
 * Redirect user to appropriate dashboard by role
 */
function redirectToDashboard(): void
{
    if (isAdmin()) {
        redirect(baseUrl('admin/index.php'));
    }

    redirect(baseUrl('user/index.php'));
}
