<?php
/**
 * Admin layout header with sidebar
 * @var string $pageTitle
 * @var string $activeAdminNav dashboard|add|books
 */

$pageTitle = $pageTitle ?? 'Admin Dashboard';
$activeAdminNav = $activeAdminNav ?? 'dashboard';
requireAdmin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> - SecureBookStore Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 admin-sidebar">
            <div class="sidebar-brand"><i class="bi bi-book-half me-2"></i>Admin Panel</div>
            <nav class="sidebar-nav">
                <a href="<?= baseUrl('admin/index.php') ?>" class="sidebar-link<?= $activeAdminNav === 'dashboard' ? ' active' : '' ?>">
                    <i class="bi bi-speedometer2"></i>Dashboard
                </a>
                <a href="<?= baseUrl('admin/add-book.php') ?>" class="sidebar-link<?= $activeAdminNav === 'add' ? ' active' : '' ?>">
                    <i class="bi bi-plus-circle"></i>Add Book
                </a>
                <a href="<?= baseUrl('books.php') ?>" class="sidebar-link<?= $activeAdminNav === 'books' ? ' active' : '' ?>">
                    <i class="bi bi-book"></i>View Books
                </a>
                <a href="<?= baseUrl('index.php') ?>" class="sidebar-link">
                    <i class="bi bi-house-door"></i>Back to Home
                </a>
                <a href="<?= baseUrl('logout.php') ?>" class="sidebar-link">
                    <i class="bi bi-box-arrow-right"></i>Logout
                </a>
            </nav>
        </div>
        <div class="col-md-9 col-lg-10 admin-content">
            <?= displayFlash() ?>
