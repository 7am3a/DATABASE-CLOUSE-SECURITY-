<?php
require_once dirname(__DIR__) . '/includes/init.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(baseUrl('admin/index.php'));
}

requireValidCsrf();

$bookId = (int) ($_POST['id'] ?? 0);
if ($bookId <= 0) {
    setFlash('error', 'Invalid book.');
    redirect(baseUrl('admin/index.php'));
}

$db = getDb();

$check = $db->prepare('SELECT COUNT(*) FROM order_items WHERE book_id = ?');
$check->execute([$bookId]);
if ((int) $check->fetchColumn() > 0) {
    setFlash('error', 'Cannot delete a book that has been ordered. Consider setting stock to zero instead.');
    redirect(baseUrl('admin/index.php'));
}

$stmt = $db->prepare('SELECT cover_image FROM books WHERE id = ? LIMIT 1');
$stmt->execute([$bookId]);
$book = $stmt->fetch();

if ($book) {
    $delete = $db->prepare('DELETE FROM books WHERE id = ?');
    $delete->execute([$bookId]);
    deleteCoverFile($book['cover_image']);
    setFlash('success', 'Book deleted successfully.');
} else {
    setFlash('error', 'Book not found.');
}

redirect(baseUrl('admin/index.php'));
