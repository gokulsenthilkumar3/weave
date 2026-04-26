<?php
/**
 * db_connect.php - Database connection
 * Improvements: Credentials read from environment variables with fallback
 * defaults, error reporting off in production, charset set to utf8mb4,
 * connection error handled gracefully (no raw die with credentials).
 */

// --- Configuration (override via environment variables in production) ---
$dbHost     = getenv('DB_HOST')     ?: '127.0.0.1';
$dbUser     = getenv('DB_USER')     ?: 'root';
$dbPassword = getenv('DB_PASSWORD') ?: '';
$dbName     = getenv('DB_NAME')     ?: 'stock';
$dbPort     = (int)(getenv('DB_PORT') ?: 3306);

// Suppress direct error output in production
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $connect = new mysqli($dbHost, $dbUser, $dbPassword, $dbName, $dbPort);
    // Set charset to utf8mb4 for full Unicode support
    $connect->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    // Log the real error server-side, show a safe message to the user
    error_log('DB Connection Error: ' . $e->getMessage());
    http_response_code(503);
    die(json_encode(['error' => 'Service temporarily unavailable. Please try again later.']));
}
?>
