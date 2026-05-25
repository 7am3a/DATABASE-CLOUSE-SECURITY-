<?php
require_once dirname(__DIR__) . '/includes/init.php';
requireAdmin();

$bookId = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
if ($bookId <= 0) {
    setFlash('error', 'Invalid book.');
    redirect(baseUrl('admin/index.php'));
}

$db = getDb();
$stmt = $db->prepare('SELECT * FROM books WHERE id = ? LIMIT 1');
$stmt->execute([$bookId]);
$book = $stmt->fetch();

if (!$book) {
    setFlash('error', 'Book not found.');
    redirect(baseUrl('admin/index.php'));
}

$errors = [];
$formData = $book;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireValidCsrf();

    $formData['title'] = sanitizeString($_POST['title'] ?? '', 200);
    $formData['author'] = sanitizeString($_POST['author'] ?? '', 150);
    $formData['description'] = sanitizeString($_POST['description'] ?? '', 5000);
    $formData['price'] = trim($_POST['price'] ?? '');
    $formData['stock'] = trim($_POST['stock'] ?? '');

    if ($formData['title'] === '') {
        $errors[] = 'Title is required.';
    }
    if ($formData['author'] === '') {
        $errors[] = 'Author is required.';
    }
    if ($formData['description'] === '') {
        $errors[] = 'Description is required.';
    }
    if (!isValidPrice($formData['price'])) {
        $errors[] = 'Please enter a valid price.';
    }
    if (!isValidStock($formData['stock'])) {
        $errors[] = 'Please enter a valid stock quantity.';
    }

    $upload = uploadCoverImage($_FILES['cover_image'] ?? [], $book['cover_image']);

    if (!$upload['success']) {
        $errors[] = $upload['message'];
    }

    if (empty($errors)) {
        $update = $db->prepare(
            'UPDATE books SET title = ?, author = ?, description = ?, price = ?, cover_image = ?, stock = ? WHERE id = ?'
        );
        $update->execute([
            $formData['title'],
            $formData['author'],
            $formData['description'],
            (float) $formData['price'],
            $upload['filename'],
            (int) $formData['stock'],
            $bookId,
        ]);

        setFlash('success', 'Book updated successfully.');
        redirect(baseUrl('admin/index.php'));
    }
}

$pageTitle = 'Edit Book';
$activeAdminNav = 'dashboard';
require_once ROOT_PATH . '/includes/admin-header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary-custom"><i class="bi bi-pencil me-2"></i>Edit Book</h2>
    <a href="<?= baseUrl('admin/index.php') ?>" class="btn btn-back"><i class="bi bi-arrow-left me-1"></i>Back to Dashboard</a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger alert-custom mb-4">
        <ul class="mb-0 ps-3"><?php foreach ($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<div class="custom-card">
    <div class="card-header-custom"><h5 class="mb-0"><?= e($formData['title']) ?></h5></div>
    <div class="card-body-custom">
        <form method="post" enctype="multipart/form-data" action="<?= baseUrl('admin/edit-book.php?id=' . $bookId) ?>">
            <?= csrfField() ?>
            <input type="hidden" name="id" value="<?= $bookId ?>">
            <div class="mb-3 text-center">
                <img src="<?= coverUrl($formData['cover_image']) ?>" alt="Current cover" style="max-height:200px;border-radius:8px;">
            </div>
            <div class="mb-3">
                <label for="title" class="form-label">Book Title</label>
                <input type="text" class="form-control form-control-custom" id="title" name="title" value="<?= e($formData['title']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="author" class="form-label">Author</label>
                <input type="text" class="form-control form-control-custom" id="author" name="author" value="<?= e($formData['author']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control form-control-custom" id="description" name="description" rows="5" required><?= e($formData['description']) ?></textarea>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="price" class="form-label">Price ($)</label>
                    <input type="number" step="0.01" min="0.01" class="form-control form-control-custom" id="price" name="price" value="<?= e($formData['price']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="stock" class="form-label">Stock</label>
                    <input type="number" min="0" class="form-control form-control-custom" id="stock" name="stock" value="<?= e($formData['stock']) ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="cover_image" class="form-label">Replace Cover (optional)</label>
                <input type="file" class="form-control form-control-custom" id="cover_image" name="cover_image" accept=".jpg,.jpeg,.png,.webp">
            </div>
            <button type="submit" class="btn btn-submit"><i class="bi bi-save me-2"></i>Update Book</button>
        </form>
    </div>
</div>

<?php require_once ROOT_PATH . '/includes/admin-footer.php'; ?>
