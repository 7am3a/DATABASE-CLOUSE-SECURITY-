<?php
/**
 * Public site header / navbar
 * @var string $pageTitle
 * @var string $activeNav  home|books|login|register
 */

$pageTitle = $pageTitle ?? 'SecureBookStore';
$activeNav = $activeNav ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Secure Online Bookstore Management System">
    <title><?= e($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body<?= isset($bodyClass) ? ' class="' . e($bodyClass) . '"' : '' ?>>

<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container">
        <a class="navbar-brand" href="<?= baseUrl('index.php') ?>">
            <i class="bi bi-book-half me-2"></i>SecureBookStore
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link<?= $activeNav === 'home' ? ' active' : '' ?>" href="<?= baseUrl('index.php') ?>">
                        <i class="bi bi-house-door me-1"></i>Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= $activeNav === 'books' ? ' active' : '' ?>" href="<?= baseUrl('books.php') ?>">
                        <i class="bi bi-book me-1"></i>Books
                    </a>
                </li>
                <!-- <li class="nav-item">
                    <a class="nav-link<?= $activeNav === 'cart' ? ' active' : '' ?>" href="<?= baseUrl('cart.php') ?>">
                        <i class="bi bi-cart3 me-1"></i>Cart
                        <?php $cartBadge = getCartItemCount(); if ($cartBadge > 0): ?>
                            <span class="badge bg-danger ms-1"><?= $cartBadge ?></span>
                        <?php endif; ?>
                    </a>
                </li> -->
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= baseUrl('admin/index.php') ?>">
                                <i class="bi bi-speedometer2 me-1"></i>Admin
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= baseUrl('user/index.php') ?>">
                                <i class="bi bi-person-circle me-1"></i>Dashboard
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= baseUrl('logout.php') ?>">
                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link<?= $activeNav === 'login' ? ' active' : '' ?>" href="<?= baseUrl('login.php') ?>">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $activeNav === 'register' ? ' active' : '' ?>" href="<?= baseUrl('register.php') ?>">
                            <i class="bi bi-person-plus me-1"></i>Register
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<?= displayFlash() ?>
