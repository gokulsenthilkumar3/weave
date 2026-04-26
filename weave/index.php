<?php
/**
 * index.php - Login Page
 * Improvements: SQL injection fix (prepared statements), password_verify,
 * session_regenerate_id, CSRF token, XSS-safe PHP_SELF removal.
 */
require_once 'php_action/db_connect.php';

session_start();

// Redirect already-logged-in users
if (isset($_SESSION['userId'])) {
    header('Location: dashboard.php');
    exit;
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF validation
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($username === '') {
            $errors[] = 'Username is required.';
        }
        if ($password === '') {
            $errors[] = 'Password is required.';
        }

        if (empty($errors)) {
            // Prepared statement - prevents SQL injection
            $stmt = $connect->prepare('SELECT id, username, password FROM users WHERE username = ? LIMIT 1');
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $user   = $result->fetch_assoc();
            $stmt->close();

            if ($user && password_verify($password, $user['password'])) {
                // Prevent session fixation
                session_regenerate_id(true);
                $_SESSION['userId']   = $user['id'];
                $_SESSION['username'] = $user['username'];
                // Rotate CSRF token on login
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                header('Location: dashboard.php');
                exit;
            } else {
                $errors[] = 'Invalid username or password.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weave &mdash; Stock Management</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="custom/css/custom.css">
    <style>
        body {
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
        }
        .login-card {
            background: var(--surface);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 420px;
            border: 1px solid var(--border);
        }
        .login-logo {
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: -1px;
            color: var(--primary);
        }
        .login-logo span { color: var(--text-primary); }
        .login-subtitle {
            color: var(--text-muted);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .form-label {
            font-weight: 500;
            color: var(--text-secondary);
            font-size: 0.85rem;
        }
        .form-control {
            background: var(--input-bg);
            border: 1px solid var(--border);
            color: var(--text-primary);
            border-radius: var(--radius-md);
            padding: 0.65rem 0.9rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-control:focus {
            background: var(--input-bg);
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
            color: var(--text-primary);
        }
        .btn-login {
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: var(--radius-md);
            padding: 0.7rem;
            font-weight: 600;
            font-size: 0.95rem;
            width: 100%;
            transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
        }
        .btn-login:hover {
            background: var(--primary-hover);
            box-shadow: var(--shadow-md);
            transform: translateY(-1px);
        }
        .btn-login:active { transform: translateY(0); }
        .alert-error {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.3);
            color: #f87171;
            border-radius: var(--radius-md);
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            margin-bottom: 1.25rem;
        }
        .input-icon-wrapper {
            position: relative;
        }
        .input-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
            width: 16px;
            height: 16px;
        }
        .input-icon-wrapper .form-control {
            padding-left: 2.4rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="mb-4 text-center">
            <div class="login-logo">weave<span>.</span></div>
            <div class="login-subtitle">Stock Management System &mdash; Sign in to continue</div>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert-error">
                <?php foreach ($errors as $error): ?>
                    <div><i data-lucide="alert-circle" style="width:14px;height:14px;vertical-align:middle;margin-right:6px;"></i><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php" novalidate>
            <!-- CSRF token -->
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <div class="input-icon-wrapper">
                    <i data-lucide="user" class="input-icon"></i>
                    <input type="text" class="form-control" id="username" name="username"
                           placeholder="Enter username" autocomplete="username" required
                           value="<?php echo htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <div class="input-icon-wrapper">
                    <i data-lucide="lock" class="input-icon"></i>
                    <input type="password" class="form-control" id="password" name="password"
                           placeholder="Enter password" autocomplete="current-password" required>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i data-lucide="log-in" style="width:16px;height:16px;vertical-align:middle;margin-right:6px;"></i>
                Sign In
            </button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>lucide.createIcons();</script>
</body>
</html>
