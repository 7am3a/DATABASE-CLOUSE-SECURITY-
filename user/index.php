<?php
require_once dirname(__DIR__) . '/includes/init.php';
requireUser();

$db = getDb();
$userId = getUserId();

$purchasedBooksStmt = $db->prepare(
    'SELECT b.id, b.title, b.author, b.price, b.cover_image, up.quantity AS total_quantity, up.purchased_at
     FROM user_purchases up
     JOIN books b ON b.id = up.book_id
     WHERE up.user_id = ?
     ORDER BY up.purchased_at DESC'
);
$purchasedBooksStmt->execute([$userId]);
$purchasedBooks = $purchasedBooksStmt->fetchAll();

$pageTitle = 'My Dashboard - SecureBookStore';
$activeNav = '';
require_once ROOT_PATH . '/includes/header.php';
?>

<div class="dashboard-container">
    <div class="container">
        <div class="dashboard-header">
            <h1 class="welcome-message"><i class="bi bi-person-circle me-2"></i>Welcome, <?= e(getFullName()) ?>!</h1>
            <p class="welcome-subtitle">View your purchased books and browse our book collection</p>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-12">
                <a href="<?= baseUrl('books.php') ?>" class="custom-card d-block p-4 text-decoration-none h-100">
                    <h4 class="text-primary-custom"><i class="bi bi-book me-2"></i>Browse Books</h4>
                    <p class="text-muted mb-0">See books added by the admin</p>
                </a>
            </div>
        </div>

        <div class="custom-card">
            <div class="card-header-custom">
                <h5 class="mb-0"><i class="bi bi-bookshelf me-2"></i>My Purchased Books</h5>
            </div>
            <div class="card-body-custom p-0">
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Item</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($purchasedBooks)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        No books purchased yet. <a href="<?= baseUrl('books.php') ?>">Browse books</a> to make your first purchase.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($purchasedBooks as $book): ?>
                                    <tr>
                                        <td><?= e(date('M j, Y g:i A', strtotime($book['purchased_at']))) ?></td>
                                        <td>
                                            <strong><?= e($book['title']) ?></strong><br>
                                            <small class="text-muted"><?= e($book['author']) ?> (x<?= (int) $book['total_quantity'] ?>)</small>
                                        </td>
                                        <td>$<?= number_format((float) $book['price'] * (int) $book['total_quantity'], 2) ?></td>
                                        <td><span class="badge bg-success">Completed</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>
