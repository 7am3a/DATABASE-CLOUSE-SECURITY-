<?php
/**
 * Database connection configuration
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'secure_bookstore');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/** Secret key for remember-me cookie HMAC (change in production) */
define('REMEMBER_SECRET', 'SecureBookStore_Change_This_Secret_Key_2024');

/** Remember-me cookie duration: 7 days */
define('REMEMBER_DURATION', 60 * 60 * 24 * 7);

/**
 * Get PDO database connection (singleton)
 */
function getDb(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            die('Database connection failed. Please check config/db.php and ensure MySQL is running.');
        }
    }

    return $pdo;
}
