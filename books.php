<?php
require_once __DIR__ . '/includes/init.php';

$db = getDb();
$perPage = 6;
$page = max(1, (int) ($_GET['page'] ?? 1));
$search = sanitizeString($_GET['q'] ?? '', 100);
$offset = paginationOffset($page, $perPage);

$where = '';
$params = [];

if ($search !== '') {
    $where = ' WHERE title LIKE ? OR author LIKE ?';
    $params = ['%' . $search . '%', '%' . $search . '%'];
}

$countSql = 'SELECT COUNT(*) FROM books' . $where;
$countStmt = $db->prepare($countSql);
$countStmt->execute($params);
$totalBooks = (int) $countStmt->fetchColumn();
$totalPages = max(1, (int) ceil($totalBooks / $perPage));

if ($page > $totalPages) {
    $page = $totalPages;
    $offset = paginationOffset($page, $perPage);
}

$limit = (int) $perPage;
$off = (int) $offset;
$sql = 'SELECT id, title, author, price, cover_image FROM books' . $where . ' ORDER BY title ASC LIMIT ' . $limit . ' OFFSET ' . $off;
$stmt = $db->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll();

$queryParams = [];
if ($search !== '') {
    $queryParams['q'] = $search;
}

$pageTitle = 'Browse Books - SecureBookStore';
$activeNav = 'books';
require_once ROOT_PATH . '/includes/header.php';
?>

<div class="dashboard-container">
    <div class="container">
        <div class="dashboard-header">
            <h1 class="welcome-message"><i class="bi bi-book me-2"></i>Browse Our Books</h1>
            <p class="welcome-subtitle">Books added by the admin appear here — buy now when logged in</p>
        </div>

        <div class="search-container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <form method="get" action="<?= baseUrl('books.php') ?>" class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="q" class="form-control search-input border-start-0"
                               value="<?= e($search) ?>" placeholder="Search books by title or author...">
                        <button type="submit" class="btn btn-primary" style="background:#1a4a8a;border:none;">Search</button>
                    </form>
                </div>
            </div>
        </div>

        <?php if (empty($books)): ?>
            <div class="text-center py-5">
                <i class="bi bi-search" style="font-size: 4rem; color: var(--text-light);"></i>
                <h4 class="mt-3 text-muted"><?= $search !== '' ? 'No books found' : 'No books available yet' ?></h4>
                <p class="text-muted"><?= $search !== '' ? 'Try adjusting your search criteria' : 'The admin has not added any books to the store.' ?></p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($books as $book): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="book-card">
                            <div class="book-image-container">
                                <img src="<?= coverUrl($book['cover_image']) ?>" alt="<?= e($book['title']) ?>" class="book-image">
                            </div>
                            <div class="book-info">
                                <h3 class="book-title" title="<?= e($book['title']) ?>"><?= e($book['title']) ?></h3>
                                <p class="book-author"><i class="bi bi-person me-1"></i><?= e($book['author']) ?></p>
                                <p class="book-price">$<?= number_format((float) $book['price'], 2) ?></p>
                                <a href="<?= baseUrl('book-details.php?id=' . (int) $book['id']) ?>" class="btn btn-view-details">
                                    <i class="bi bi-eye me-1"></i>View Details
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <nav class="mt-5" aria-label="Books pagination">
                    <ul class="pagination justify-content-center">
                        <li class="page-item<?= $page <= 1 ? ' disabled' : '' ?>">
                            <a class="page-link" href="<?= e(paginationQuery($queryParams, $page - 1)) ?>">Previous</a>
                        </li>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item<?= $i === $page ? ' active' : '' ?>">
                                <a class="page-link" href="<?= e(paginationQuery($queryParams, $i)) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item<?= $page >= $totalPages ? ' disabled' : '' ?>">
                            <a class="page-link" href="<?= e(paginationQuery($queryParams, $page + 1)) ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>
