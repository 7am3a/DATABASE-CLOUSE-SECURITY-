<footer class="footer-custom">
    <div class="container">
        <div class="row">
            <div class="col-lg-5 mb-4 mb-lg-0">
                <h5 class="footer-title"><i class="bi bi-book-half me-2"></i>SecureBookStore</h5>
                <p class="text-white-50">A secure online bookstore management system built as a university assignment project.</p>
                <div class="mt-3">
                    <a href="#" class="footer-social me-3"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="footer-social me-3"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="footer-social me-3"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="footer-social"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
            <?php if (!isAdmin()): ?>
            <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                <h5 class="footer-title">Quick Links</h5>
                <a href="<?= baseUrl('index.php') ?>" class="footer-link">Home</a>
                <a href="<?= baseUrl('books.php') ?>" class="footer-link">Books</a>
                <a href="<?= baseUrl('login.php') ?>" class="footer-link">Login</a>
                <a href="<?= baseUrl('register.php') ?>" class="footer-link">Register</a>
            </div>
            <?php endif; ?>
            <div class="col-lg-4 col-md-6">
                <h5 class="footer-title">Contact</h5>
                <p class="text-white-50 mb-2"><i class="bi bi-envelope me-2"></i>info@bookstore.com</p>
                <p class="text-white-50 mb-2"><i class="bi bi-telephone me-2"></i>+60 123-456-789</p>
                <p class="text-white-50"><i class="bi bi-geo-alt me-2"></i>MMU Cyberjaya, Campus</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p class="mb-0">&copy; <?= date('Y') ?> Secure Online Bookstore Management System. All rights reserved.</p>
        </div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= asset('js/script.js') ?>"></script>
</body>
</html>
