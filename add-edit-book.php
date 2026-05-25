<?php
require_once __DIR__ . '/includes/init.php';
$id = (int) ($_GET['id'] ?? 0);
if ($id > 0) {
    redirect(baseUrl('admin/edit-book.php?id=' . $id));
}
redirect(baseUrl('admin/add-book.php'));
