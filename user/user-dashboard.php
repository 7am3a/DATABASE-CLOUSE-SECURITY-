<?php
require_once __DIR__ . '/includes/init.php';
if (isAdmin()) {
    redirect(baseUrl('admin/index.php'));
}
if (isUser()) {
    redirect(baseUrl('user/index.php'));
}
redirect(baseUrl('books.php'));
