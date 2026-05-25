<?php
require_once __DIR__ . '/includes/init.php';

$bookId = (int) ($_GET['id'] ?? 0);
if ($bookId <= 0) {
    setFlash('error', 'Invalid book selected.');
    redirect(baseUrl('books.php'));
}

$db = getDb();
$stmt = $db->prepare('SELECT * FROM books WHERE id = ? LIMIT 1');
$stmt->execute([$bookId]);
$book = $stmt->fetch();

if (!$book) {
    setFlash('error', 'Book not found.');
    redirect(baseUrl('books.php'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireValidCsrf();
    requireLogin();

    $quantity = (int) ($_POST['quantity'] ?? 1);

    // Insert into user_purchases table
    $stmt = $db->prepare('INSERT INTO user_purchases (user_id, book_id, quantity) VALUES (?, ?, ?) 
                          ON DUPLICATE KEY UPDATE quantity = quantity + ?');
    $stmt->execute([getUserId(), $bookId, $quantity, $quantity]);

    setFlash('success', 'You bought successfully!');
    redirect(baseUrl('book-details.php?id=' . $bookId));
}

$pageTitle = e($book['title']) . ' - Book Details';
$activeNav = 'books';
require_once ROOT_PATH . '/includes/header.php';
?>

<section class="section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <a href="<?= baseUrl('books.php') ?>" class="btn btn-back mb-4">
                    <i class="bi bi-arrow-left me-2"></i>Back to Books
                </a>
                <div class="book-details-container">
                    <div class="row">
                        <div class="col-md-5 mb-4 mb-md-0">
                            <img src="<?= coverUrl($book['cover_image']) ?>" alt="<?= e($book['title']) ?>" class="book-details-image">
                        </div>
                        <div class="col-md-7">
                            <h1 class="book-details-title"><?= e($book['title']) ?></h1>
                            <p class="book-details-author"><i class="bi bi-person me-2"></i><?= e($book['author']) ?></p>
                            <p class="book-details-price">$<?= number_format((float) $book['price'], 2) ?></p>
                            <p class="mb-3"><span class="badge bg-secondary">In stock: <?= (int) $book['stock'] ?></span></p>
                            <p class="book-details-description"><?= nl2br(e($book['description'])) ?></p>

                            <?php if ((int) $book['stock'] > 0): ?>
                                <?php if (!isAdmin()): ?>
                                    <form method="post" action="<?= baseUrl('book-details.php?id=' . $bookId) ?>" class="row g-3 align-items-end">
                                        <?= csrfField() ?>
                                        <div class="col-auto">
                                            <label for="quantity" class="form-label">Quantity</label>
                                            <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" max="<?= min(10, (int) $book['stock']) ?>" required>
                                        </div>
                                        <div class="col-auto">
                                            <button type="submit" class="btn btn-submit px-4">
                                                <i class="bi bi-cart-check me-2"></i>Buy Now
                                            </button>
                                        </div>
                                    </form>
                                    <?php if (getCartItemCount() > 0): ?>
                                        <p class="text-muted mt-2 mb-0">
                                            <a href="<?= baseUrl('cart.php') ?>">View cart</a> (<?= getCartItemCount() ?> items)
                                        </p>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="alert alert-info alert-custom">Admins cannot purchase books.</div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="alert alert-warning alert-custom">This book is currently out of stock.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>
