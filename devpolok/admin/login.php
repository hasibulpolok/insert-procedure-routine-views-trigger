<?php
require_once '../config.php';

if (isAdminLoggedIn()) {
    redirect(SITE_URL . '/admin/');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION[ADMIN_SESSION_NAME] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            redirect(SITE_URL . '/admin/');
        } else {
            $error = 'Invalid username or password.';
        }
    } else {
        $error = 'Please fill in all fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — DevPolok CMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/admin.css">
    <style>
      body { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: var(--bg); }
      .login-wrap { width: 100%; max-width: 440px; padding: 1rem; }
      .login-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 3rem 2.5rem;
        box-shadow: 0 25px 60px rgba(0,0,0,0.4);
      }
      .login-logo { text-align: center; margin-bottom: 2rem; }
      .login-logo .logo-text { font-family: 'Syne', sans-serif; font-size: 2rem; font-weight: 800; }
      .login-logo .accent { color: var(--primary); }
      .login-subtitle { color: var(--text-muted); font-size: 0.9rem; text-align: center; margin-bottom: 2.5rem; }
      .error-msg {
        background: rgba(239,68,68,0.1);
        border: 1px solid rgba(239,68,68,0.3);
        color: #ef4444;
        padding: 0.85rem 1rem;
        border-radius: 10px;
        font-size: 0.9rem;
        margin-bottom: 1.5rem;
        text-align: center;
      }
      .default-creds {
        background: rgba(99,102,241,0.1);
        border: 1px solid rgba(99,102,241,0.2);
        color: var(--primary);
        padding: 0.75rem 1rem;
        border-radius: 10px;
        font-size: 0.82rem;
        margin-top: 1.5rem;
        text-align: center;
        line-height: 1.7;
      }
    </style>
</head>
<body>
    <div class="login-wrap">
        <div class="login-card">
            <div class="login-logo">
                <span class="logo-text">Dev<span class="accent">Polok</span></span>
            </div>
            <p class="login-subtitle">🔐 Admin Panel — Secure Login</p>
            <?php if ($error): ?>
            <div class="error-msg"><i class="fas fa-exclamation-circle"></i> <?= sanitize($error) ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-user"></i> Username</label>
                    <input type="text" name="username" class="form-input" placeholder="Enter username" required autocomplete="username" value="<?= sanitize($_POST['username'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" name="password" class="form-input" placeholder="Enter password" required autocomplete="current-password">
                </div>
                <button type="submit" class="btn-primary w-full mt-4">
                    <i class="fas fa-sign-in-alt"></i> Login to Dashboard
                </button>
            </form>
            <div class="default-creds">
                <!-- <strong>Default Credentials:</strong><br> -->
                <!-- Username: <code>admin</code> &nbsp;|&nbsp; Password: <code>password</code> -->
            </div>
        </div>
    </div>
</body>
</html>
