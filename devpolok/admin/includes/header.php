<?php
// ============================================
// Admin Header Include
// ============================================
require_once dirname(__DIR__, 2) . '/config.php';
requireAdminLogin();
$pdo = getDB();

// Unread messages count
$unreadCount = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

function adminNav($href, $icon, $label, $currentPage, $badge = 0) {
    $active = (basename($href, '.php') === $currentPage) ? 'active' : '';
    $badgeHtml = $badge > 0 ? '<span class="badge">' . $badge . '</span>' : '';
    return "<a href='$href' class='sidebar-link $active'><i class='$icon'></i> $label $badgeHtml</a>";
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Dashboard' ?> — DevPolok CMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/admin/assets/admin.css">
</head>
<body>
<div class="admin-layout">

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <span class="logo-text">Dev<span class="accent">Polok</span></span>
        <span class="sub">CMS ADMIN PANEL</span>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section-label">Main</div>
        <?= adminNav('index.php', 'fas fa-chart-pie', 'Dashboard', $currentPage) ?>
        <?= adminNav('settings.php', 'fas fa-cog', 'Site Settings', $currentPage) ?>
        <?= adminNav('menu.php', 'fas fa-bars', 'Menu Manager', $currentPage) ?>

        <div class="nav-section-label">Content</div>
        <?= adminNav('skills.php', 'fas fa-code', 'Skills', $currentPage) ?>
        <?= adminNav('services.php', 'fas fa-briefcase', 'Services', $currentPage) ?>
        <?= adminNav('projects.php', 'fas fa-laptop-code', 'Projects', $currentPage) ?>
        <?= adminNav('testimonials.php', 'fas fa-star', 'Testimonials', $currentPage) ?>

        <div class="nav-section-label">Communication</div>
        <?= adminNav('messages.php', 'fas fa-envelope', 'Messages', $currentPage, (int)$unreadCount) ?>

        <div class="nav-section-label">Account</div>
        <?= adminNav('profile.php', 'fas fa-user', 'My Profile', $currentPage) ?>
    </nav>
    <div class="sidebar-footer">
        <a href="<?= SITE_URL ?>/" target="_blank" class="view-site">
            <i class="fas fa-external-link-alt"></i> View Website
        </a>
        <a href="logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</aside>

<!-- MAIN -->
<main class="main-content">
<div class="topbar">
    <div class="topbar-left">
        <h1><?= $pageTitle ?? 'Dashboard' ?></h1>
    </div>
    <div class="topbar-right">
        <a href="messages.php" style="position:relative; color: var(--text-muted);">
            <i class="fas fa-bell"></i>
            <?php if ($unreadCount > 0): ?>
            <span style="position:absolute;top:-5px;right:-5px;background:var(--danger);color:white;font-size:0.65rem;font-weight:700;width:16px;height:16px;border-radius:50%;display:flex;align-items:center;justify-content:center;"><?= $unreadCount ?></span>
            <?php endif; ?>
        </a>
        <div class="admin-avatar"><?= strtoupper(substr($_SESSION['admin_username'] ?? 'A', 0, 1)) ?></div>
        <span class="topbar-username"><?= sanitize($_SESSION['admin_username'] ?? 'Admin') ?></span>
    </div>
</div>
<div class="page-content">
