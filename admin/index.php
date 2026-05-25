<?php
require_once dirname(__DIR__) . '/includes/init.php';
requireAdmin();

$db = getDb();

$totalBooks = (int) $db->query('SELECT COUNT(*) FROM books')->fetchColumn();
$totalAuthors = (int) $db->query('SELECT COUNT(DISTINCT author) FROM books')->fetchColumn();
$avgPrice = (float) $db->query('SELECT COALESCE(AVG(price), 0) FROM books')->fetchColumn();

$books = $db->query('SELECT id, title, author, price, cover_image, stock FROM books ORDER BY title ASC')->fetchAll();

$pageTitle = 'Admin Dashboard';
$activeAdminNav = 'dashboard';
require_once ROOT_PATH . '/includes/admin-header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary-custom"><i class="bi bi-speedometer2 me-2"></i>Admin Dashboard</h2>
    <div>
        <span class="badge bg-primary-custom me-2"><i class="bi bi-person-circle me-1"></i><?= e(getFullName()) ?></span>
        <a href="<?= baseUrl('logout.php') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-box-arrow-right me-1"></i>Logout
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon me-3"><i class="bi bi-book"></i></div>
                <div>
                    <div class="stat-value"><?= $totalBooks ?></div>
                    <div class="stat-label">Total Books</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon me-3"><i class="bi bi-people"></i></div>
                <div>
                    <div class="stat-value"><?= $totalAuthors ?></div>
                    <div class="stat-label">Total Authors</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon me-3"><i class="bi bi-currency-dollar"></i></div>
                <div>
                    <div class="stat-value">$<?= number_format($avgPrice, 2) ?></div>
                    <div class="stat-label">Avg. Price</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="custom-card">
    <div class="card-header-custom d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Books Management</h5>
        <a href="<?= baseUrl('admin/add-book.php') ?>" class="btn btn-add btn-sm">
            <i class="bi bi-plus-circle me-1"></i>Add Book
        </a>
    </div>
    <div class="card-body-custom p-0">
        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($books)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="bi bi-inbox d-block mb-2" style="font-size:2rem;"></i>
                                No books yet. Use <strong>Add Book</strong> to create your catalog.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($books as $book): ?>
                            <tr>
                                <td>
                                    <img src="<?= coverUrl($book['cover_image']) ?>" alt="" width="50" height="65" style="object-fit:cover;border-radius:4px;">
                                </td>
                                <td><?= e($book['title']) ?></td>
                                <td><?= e($book['author']) ?></td>
                                <td>$<?= number_format((float) $book['price'], 2) ?></td>
                                <td><?= (int) $book['stock'] ?></td>
                                <td>
                                    <a href="<?= baseUrl('admin/edit-book.php?id=' . (int) $book['id']) ?>" class="btn btn-action btn-edit">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <form method="post" action="<?= baseUrl('admin/delete-book.php') ?>" class="d-inline" onsubmit="return confirm('Delete this book?');">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="id" value="<?= (int) $book['id'] ?>">
                                        <button type="submit" class="btn btn-action btn-delete" data-confirm-delete>
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/includes/admin-footer.php'; ?>
