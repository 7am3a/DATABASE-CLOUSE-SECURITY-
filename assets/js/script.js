/**
 * SecureBookStore - Client-side enhancements
 */

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('clientSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            const cards = document.querySelectorAll('.book-card-item');
            let visible = 0;

            cards.forEach(function (card) {
                const title = (card.dataset.title || '').toLowerCase();
                const author = (card.dataset.author || '').toLowerCase();
                const match = title.includes(query) || author.includes(query);
                card.style.display = match ? '' : 'none';
                if (match) visible++;
            });

            const noResults = document.getElementById('noResults');
            if (noResults) {
                noResults.classList.toggle('d-none', visible > 0 || query === '');
            }
        });
    }

    const confirmDelete = document.querySelectorAll('[data-confirm-delete]');
    confirmDelete.forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            if (!confirm('Are you sure you want to delete this book? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
});
