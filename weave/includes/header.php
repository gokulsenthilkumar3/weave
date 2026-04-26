<?php
/**
 * header.php - Shared application header
 * Improvements: Bootstrap 5, Google Fonts (Inter), Lucide icons,
 * active nav-link detection, user avatar with username, dark-mode ready.
 */
require_once 'php_action/core.php';

// Detect current page for active nav highlighting
$currentPage = basename($_SERVER['PHP_SELF']);
$currentQuery = $_GET['o'] ?? '';
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weave &mdash; Stock Management</title>

    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="custom/css/custom.css">

    <!-- jQuery (required for DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
</head>
<body>

<nav class="navbar navbar-expand-lg weave-navbar" id="mainNav">
    <div class="container-fluid px-4">
        <!-- Brand / Logo -->
        <a class="navbar-brand weave-brand" href="dashboard.php">
            weave<span class="brand-dot">.</span>
        </a>

        <!-- Mobile toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarContent" aria-controls="navbarContent"
                aria-expanded="false" aria-label="Toggle navigation">
            <i data-lucide="menu" style="width:20px;height:20px;"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <!-- Left nav links -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                        <i data-lucide="layout-dashboard" class="nav-icon"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage === 'brand.php' ? 'active' : ''; ?>" href="brand.php">
                        <i data-lucide="tag" class="nav-icon"></i> Brand
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage === 'categories.php' ? 'active' : ''; ?>" href="categories.php">
                        <i data-lucide="list" class="nav-icon"></i> Categories
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage === 'product.php' ? 'active' : ''; ?>" href="product.php">
                        <i data-lucide="package" class="nav-icon"></i> Products
                    </a>
                </li>
                <!-- Orders dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo $currentPage === 'orders.php' ? 'active' : ''; ?>"
                       href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i data-lucide="shopping-cart" class="nav-icon"></i> Orders
                    </a>
                    <ul class="dropdown-menu weave-dropdown">
                        <li>
                            <a class="dropdown-item <?php echo ($currentPage === 'orders.php' && $currentQuery === 'add') ? 'active' : ''; ?>" href="orders.php?o=add">
                                <i data-lucide="plus-circle" class="nav-icon"></i> Add Order
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item <?php echo ($currentPage === 'orders.php' && $currentQuery === 'manord') ? 'active' : ''; ?>" href="orders.php?o=manord">
                                <i data-lucide="edit" class="nav-icon"></i> Manage Orders
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage === 'report.php' ? 'active' : ''; ?>" href="report.php">
                        <i data-lucide="bar-chart-2" class="nav-icon"></i> Reports
                    </a>
                </li>
            </ul>

            <!-- Right: Dark mode toggle + User menu -->
            <ul class="navbar-nav ms-auto align-items-center gap-2">
                <!-- Dark/Light toggle -->
                <li class="nav-item">
                    <button class="btn btn-icon theme-toggle" id="themeToggle" title="Toggle theme">
                        <i data-lucide="sun" id="themeIcon" style="width:18px;height:18px;"></i>
                    </button>
                </li>
                <!-- User dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle user-menu" href="#" role="button"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="user-avatar"><?php echo strtoupper(substr($currentUser, 0, 1)); ?></span>
                        <span class="user-name d-none d-lg-inline"><?php echo htmlspecialchars($currentUser, ENT_QUOTES, 'UTF-8'); ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end weave-dropdown">
                        <li>
                            <a class="dropdown-item" href="setting.php">
                                <i data-lucide="settings" class="nav-icon"></i> Settings
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="logout.php">
                                <i data-lucide="log-out" class="nav-icon"></i> Logout
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Page content wrapper -->
<div class="page-wrapper container-fluid px-4 py-4">

<script>
// Theme toggle logic
(function() {
    const saved = localStorage.getItem('weave-theme') || 'dark';
    document.documentElement.setAttribute('data-theme', saved);
})();

document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    const btn = document.getElementById('themeToggle');
    const icon = document.getElementById('themeIcon');
    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('weave-theme', theme);
        if (icon) {
            icon.setAttribute('data-lucide', theme === 'dark' ? 'sun' : 'moon');
            lucide.createIcons();
        }
    }
    if (btn) {
        btn.addEventListener('click', function() {
            const current = document.documentElement.getAttribute('data-theme');
            applyTheme(current === 'dark' ? 'light' : 'dark');
        });
    }
    // Set correct icon on load
    const theme = document.documentElement.getAttribute('data-theme');
    if (icon) icon.setAttribute('data-lucide', theme === 'dark' ? 'sun' : 'moon');
    lucide.createIcons();
});
</script>
