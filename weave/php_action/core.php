<?php
/**
 * core.php - Central authentication guard
 * Improvements: Auth check un-commented, session started once,
 * CSRF helper included, user info exposed to templates.
 */

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db_connect.php';

// ---- Auth guard ----
// Redirect unauthenticated users to login page
if (!isset($_SESSION['userId'])) {
    header('Location: ' . (str_contains($_SERVER['PHP_SELF'], 'php_action') ? '../index.php' : 'index.php'));
    exit;
}

// ---- CSRF helper ----
// Ensure a CSRF token exists for all protected pages
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ---- Convenience globals ----
$currentUser = $_SESSION['username'] ?? 'User';
$currentUserId = (int)($_SESSION['userId'] ?? 0);
?>
