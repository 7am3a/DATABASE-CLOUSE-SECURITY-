<?php
/**
 * Session-based shopping cart
 */

function initCart(): void
{
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

function getCart(): array
{
    initCart();
    return $_SESSION['cart'];
}

function getCartItemCount(): int
{
    $cart = getCart();
    return (int) array_sum($cart);
}

/**
 * @return list<array<string, mixed>>
 */
function getCartLines(): array
{
    $cart = getCart();
    if ($cart === []) {
        return [];
    }

    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $db = getDb();
    $stmt = $db->prepare(
        "SELECT id, title, author, price, cover_image, stock FROM books WHERE id IN ($placeholders)"
    );
    $stmt->execute($ids);
    $books = [];
    foreach ($stmt->fetchAll() as $book) {
        $books[(int) $book['id']] = $book;
    }

    $lines = [];
    foreach ($cart as $bookId => $quantity) {
        $bookId = (int) $bookId;
        if (!isset($books[$bookId])) {
            unset($_SESSION['cart'][$bookId]);
            continue;
        }
        $book = $books[$bookId];
        $qty = (int) $quantity;
        $lines[] = [
            'book_id'    => $bookId,
            'title'      => $book['title'],
            'author'     => $book['author'],
            'price'      => (float) $book['price'],
            'cover_image'=> $book['cover_image'],
            'stock'      => (int) $book['stock'],
            'quantity'   => $qty,
            'line_total' => (float) $book['price'] * $qty,
        ];
    }

    return $lines;
}

function getCartSubtotal(): float
{
    $total = 0.0;
    foreach (getCartLines() as $line) {
        $total += $line['line_total'];
    }
    return $total;
}

function addToCart(int $bookId, int $quantity = 1): array
{
    initCart();

    if ($bookId <= 0 || $quantity < 1 || $quantity > 10) {
        return ['success' => false, 'message' => 'Invalid quantity. Use 1-10.'];
    }

    $db = getDb();
    $stmt = $db->prepare('SELECT id, stock FROM books WHERE id = ? LIMIT 1');
    $stmt->execute([$bookId]);
    $book = $stmt->fetch();

    if (!$book) {
        return ['success' => false, 'message' => 'Book not found.'];
    }

    $stock = (int) $book['stock'];
    if ($stock < 1) {
        return ['success' => false, 'message' => 'This book is out of stock.'];
    }

    $current = (int) ($_SESSION['cart'][$bookId] ?? 0);
    $newQty = $current + $quantity;

    if ($newQty > $stock) {
        return ['success' => false, 'message' => 'Not enough stock. Only ' . $stock . ' available.'];
    }
    if ($newQty > 10) {
        return ['success' => false, 'message' => 'Maximum 10 units per book in cart.'];
    }

    $_SESSION['cart'][$bookId] = $newQty;

    return ['success' => true, 'message' => 'Added to cart.'];
}

function updateCartQuantity(int $bookId, int $quantity): array
{
    initCart();

    if ($bookId <= 0) {
        return ['success' => false, 'message' => 'Invalid book.'];
    }

    if ($quantity < 1) {
        return removeFromCart($bookId);
    }

    if ($quantity > 10) {
        return ['success' => false, 'message' => 'Maximum 10 units per book.'];
    }

    $db = getDb();
    $stmt = $db->prepare('SELECT stock FROM books WHERE id = ? LIMIT 1');
    $stmt->execute([$bookId]);
    $book = $stmt->fetch();

    if (!$book) {
        unset($_SESSION['cart'][$bookId]);
        return ['success' => false, 'message' => 'Book no longer available.'];
    }

    if ($quantity > (int) $book['stock']) {
        return ['success' => false, 'message' => 'Not enough stock available.'];
    }

    $_SESSION['cart'][$bookId] = $quantity;

    return ['success' => true, 'message' => 'Cart updated.'];
}

function removeFromCart(int $bookId): array
{
    initCart();
    unset($_SESSION['cart'][$bookId]);
    return ['success' => true, 'message' => 'Item removed from cart.'];
}

function clearCart(): void
{
    $_SESSION['cart'] = [];
}

function checkoutCart(int $userId): array
{
    $lines = getCartLines();
    if ($lines === []) {
        return ['success' => false, 'message' => 'Your cart is empty.'];
    }

    $db = getDb();

    try {
        $db->beginTransaction();

        foreach ($lines as $line) {
            $lock = $db->prepare('SELECT stock, price FROM books WHERE id = ? FOR UPDATE');
            $lock->execute([$line['book_id']]);
            $row = $lock->fetch();
            if (!$row || (int) $row['stock'] < $line['quantity']) {
                throw new RuntimeException('Insufficient stock for: ' . $line['title']);
            }
        }

        $total = getCartSubtotal();
        $orderStmt = $db->prepare('INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, ?)');
        $orderStmt->execute([$userId, $total, 'completed']);
        $orderId = (int) $db->lastInsertId();

        $itemStmt = $db->prepare(
            'INSERT INTO order_items (order_id, book_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)'
        );
        $stockStmt = $db->prepare('UPDATE books SET stock = stock - ? WHERE id = ? AND stock >= ?');

        foreach ($lines as $line) {
            $itemStmt->execute([$orderId, $line['book_id'], $line['quantity'], $line['price']]);
            $stockStmt->execute([$line['quantity'], $line['book_id'], $line['quantity']]);
            if ($stockStmt->rowCount() === 0) {
                throw new RuntimeException('Stock update failed for: ' . $line['title']);
            }
        }

        $db->commit();
        clearCart();

        return ['success' => true, 'message' => 'Order #' . $orderId . ' placed successfully!', 'order_id' => $orderId];
    } catch (Exception $e) {
        $db->rollBack();
        return ['success' => false, 'message' => 'Checkout failed. Please review your cart and try again.'];
    }
}
