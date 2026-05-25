<?php
/**
 * Flash messages
 */

function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type'    => $type,
        'message' => $message,
    ];
}

function getFlash(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function displayFlash(): string
{
    $flash = getFlash();
    if (!$flash) {
        return '';
    }

    $type = $flash['type'] === 'success' ? 'success' : 'danger';
    $class = $type === 'success' ? 'alert-success-custom' : 'alert-danger-custom';
    $icon = $type === 'success' ? 'check-circle' : 'exclamation-triangle';

    return '<div class="alert alert-' . $type . ' alert-custom ' . $class . ' alert-dismissible fade show" role="alert">'
        . '<i class="bi bi-' . $icon . ' me-2"></i>'
        . e($flash['message'])
        . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'
        . '</div>';
}
