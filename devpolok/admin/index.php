<?php
$pageTitle = 'Dashboard';
require_once 'includes/header.php';

$pdo = getDB();
$skillCount       = $pdo->query("SELECT COUNT(*) FROM skills WHERE is_active=1")->fetchColumn();
$serviceCount     = $pdo->query("SELECT COUNT(*) FROM services WHERE is_active=1")->fetchColumn();
$projectCount     = $pdo->query("SELECT COUNT(*) FROM projects WHERE is_active=1")->fetchColumn();
$testimonialCount = $pdo->query("SELECT COUNT(*) FROM testimonials WHERE is_active=1")->fetchColumn();
$msgCount         = $pdo->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
$unreadMsgCount   = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read=0")->fetchColumn();
$recentMessages   = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-laptop-code"></i></div>
        <div><div class="stat-num-lg"><?= $projectCount ?></div><div class="stat-label-sm">Active Projects</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-star"></i></div>
        <div><div class="stat-num-lg"><?= $testimonialCount ?></div><div class="stat-label-sm">Testimonials</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber"><i class="fas fa-code"></i></div>
        <div><div class="stat-num-lg"><?= $skillCount ?></div><div class="stat-label-sm">Skills</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-envelope"></i></div>
        <div>
            <div class="stat-num-lg"><?= $unreadMsgCount ?></div>
            <div class="stat-label-sm">Unread Messages <?php if ($msgCount > 0): ?><span style="color:var(--text-muted)">(<?= $msgCount ?> total)</span><?php endif; ?></div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:2rem;" class="dash-grid">
    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-bolt" style="color:var(--accent)"></i> Quick Actions</span>
        </div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:0.75rem;">
            <a href="projects.php?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Project</a>
            <a href="skills.php?action=add" class="btn btn-secondary"><i class="fas fa-plus"></i> Add New Skill</a>
            <a href="services.php?action=add" class="btn btn-secondary"><i class="fas fa-plus"></i> Add New Service</a>
            <a href="testimonials.php?action=add" class="btn btn-secondary"><i class="fas fa-plus"></i> Add Testimonial</a>
            <a href="settings.php" class="btn btn-secondary"><i class="fas fa-cog"></i> Site Settings</a>
            <a href="<?= SITE_URL ?>/" target="_blank" class="btn btn-secondary"><i class="fas fa-eye"></i> View Live Site</a>
        </div>
    </div>

    <!-- Recent Messages -->
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-envelope" style="color:var(--primary)"></i> Recent Messages</span>
            <a href="messages.php" class="btn btn-sm btn-secondary">View All</a>
        </div>
        <div class="table-wrap">
            <?php if (empty($recentMessages)): ?>
            <div class="card-body text-muted text-sm">No messages yet.</div>
            <?php else: ?>
            <table>
                <tbody>
                    <?php foreach ($recentMessages as $msg): ?>
                    <tr class="<?= !$msg['is_read'] ? 'message-row unread' : '' ?>">
                        <td>
                            <?php if (!$msg['is_read']): ?><span class="unread-dot" style="margin-right:6px"></span><?php endif; ?>
                            <div style="font-weight:600;font-size:0.85rem"><?= sanitize($msg['name']) ?></div>
                            <div class="text-muted text-sm"><?= sanitize($msg['email']) ?></div>
                        </td>
                        <td>
                            <div class="truncate text-sm"><?= sanitize($msg['subject'] ?: 'No subject') ?></div>
                            <div class="text-muted text-sm"><?= date('M d', strtotime($msg['created_at'])) ?></div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Content Overview -->
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-chart-bar" style="color:var(--primary)"></i> Content Overview</span>
    </div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:1rem;">
            <?php
            $items = [
                ['label'=>'Skills', 'count'=>$skillCount, 'link'=>'skills.php', 'icon'=>'fas fa-code', 'color'=>'purple'],
                ['label'=>'Services', 'count'=>$serviceCount, 'link'=>'services.php', 'icon'=>'fas fa-briefcase', 'color'=>'amber'],
                ['label'=>'Projects', 'count'=>$projectCount, 'link'=>'projects.php', 'icon'=>'fas fa-laptop-code', 'color'=>'cyan'],
                ['label'=>'Testimonials', 'count'=>$testimonialCount, 'link'=>'testimonials.php', 'icon'=>'fas fa-star', 'color'=>'green'],
            ];
            foreach ($items as $item): ?>
            <a href="<?= $item['link'] ?>" style="display:flex;align-items:center;gap:1rem;background:var(--bg-card-hover);padding:1rem;border-radius:var(--radius);border:1px solid var(--border);transition:var(--transition);" onmouseover="this.style.borderColor='rgba(99,102,241,0.4)'" onmouseout="this.style.borderColor='var(--border)'">
                <div class="stat-icon <?= $item['color'] ?>" style="min-width:40px;height:40px;"><i class="<?= $item['icon'] ?>"></i></div>
                <div>
                    <div style="font-family:var(--font-display);font-size:1.5rem;font-weight:800;line-height:1"><?= $item['count'] ?></div>
                    <div class="text-muted text-sm"><?= $item['label'] ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
@media(max-width:768px){ .dash-grid{grid-template-columns:1fr !important;} }
</style>

<?php require_once 'includes/footer.php'; ?>
