<?php
$pageTitle = 'My Profile';
require_once 'includes/header.php';
$pdo = getDB();
$success = $error = '';

$adminId = $_SESSION[ADMIN_SESSION_NAME];
$admin = $pdo->prepare("SELECT * FROM admin_users WHERE id=?");
$admin->execute([$adminId]);
$admin = $admin->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pa = $_POST['action'] ?? '';

    if ($pa === 'update_profile') {
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        if (!$username) { $error = 'Username required.'; }
        else {
            // Check username not taken by another user
            $check = $pdo->prepare("SELECT id FROM admin_users WHERE username=? AND id!=?");
            $check->execute([$username, $adminId]);
            if ($check->fetch()) { $error = 'Username already taken.'; }
            else {
                $pdo->prepare("UPDATE admin_users SET username=?,email=? WHERE id=?")->execute([$username,$email,$adminId]);
                $_SESSION['admin_username'] = $username;
                $success = 'Profile updated!';
                $admin['username'] = $username;
                $admin['email'] = $email;
            }
        }
    } elseif ($pa === 'change_password') {
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        if (!$current || !$new || !$confirm) { $error = 'All fields required.'; }
        elseif (!password_verify($current, $admin['password'])) { $error = 'Current password is incorrect.'; }
        elseif ($new !== $confirm) { $error = 'New passwords do not match.'; }
        elseif (strlen($new) < 6) { $error = 'Password must be at least 6 characters.'; }
        else {
            $hashed = password_hash($new, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE admin_users SET password=? WHERE id=?")->execute([$hashed, $adminId]);
            $success = 'Password changed successfully!';
        }
    }
}
?>

<?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div><?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">
    <!-- Profile Info -->
    <div class="card">
        <div class="card-header"><span class="card-title"><i class="fas fa-user" style="color:var(--primary)"></i> Profile Info</span></div>
        <div class="card-body">
            <div style="text-align:center;margin-bottom:2rem">
                <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,var(--primary),#8b5cf6);display:flex;align-items:center;justify-content:center;color:white;font-size:2rem;font-weight:700;font-family:'Syne',sans-serif;margin:0 auto 1rem">
                    <?= strtoupper(substr($admin['username'],0,1)) ?>
                </div>
                <div style="font-weight:700;font-size:1.1rem"><?= sanitize($admin['username']) ?></div>
                <div class="text-muted text-sm">Administrator</div>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="update_profile">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-input" value="<?= sanitize($admin['username']) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-input" value="<?= sanitize($admin['email'] ?? '') ?>" placeholder="admin@example.com">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Profile</button>
            </form>
        </div>
    </div>

    <!-- Change Password -->
    <div class="card">
        <div class="card-header"><span class="card-title"><i class="fas fa-lock" style="color:var(--accent)"></i> Change Password</span></div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="action" value="change_password">
                <div class="form-group">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-input" required autocomplete="current-password">
                </div>
                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" name="new_password" class="form-input" required autocomplete="new-password" minlength="6">
                    <p class="form-hint">Minimum 6 characters</p>
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-input" required autocomplete="new-password">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-key"></i> Change Password</button>
            </form>
        </div>
    </div>
</div>

<!-- Quick Info -->
<div class="card" style="margin-top:1.5rem">
    <div class="card-header"><span class="card-title"><i class="fas fa-info-circle" style="color:var(--info)"></i> System Info</span></div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1rem">
            <div style="background:var(--bg-card-hover);padding:1rem;border-radius:var(--radius);border:1px solid var(--border)">
                <div class="text-muted text-sm">PHP Version</div>
                <div style="font-weight:700"><?= phpversion() ?></div>
            </div>
            <div style="background:var(--bg-card-hover);padding:1rem;border-radius:var(--radius);border:1px solid var(--border)">
                <div class="text-muted text-sm">Database</div>
                <div style="font-weight:700">MySQL (PDO)</div>
            </div>
            <div style="background:var(--bg-card-hover);padding:1rem;border-radius:var(--radius);border:1px solid var(--border)">
                <div class="text-muted text-sm">Upload Max Size</div>
                <div style="font-weight:700"><?= ini_get('upload_max_filesize') ?></div>
            </div>
            <div style="background:var(--bg-card-hover);padding:1rem;border-radius:var(--radius);border:1px solid var(--border)">
                <div class="text-muted text-sm">Logged In As</div>
                <div style="font-weight:700"><?= sanitize($_SESSION['admin_username'] ?? 'admin') ?></div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
