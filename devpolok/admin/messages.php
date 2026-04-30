<?php
$pageTitle = 'Contact Messages';
require_once 'includes/header.php';
$pdo = getDB();
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pa = $_POST['action'] ?? '';
    if ($pa === 'delete') {
        $pdo->prepare("DELETE FROM contact_messages WHERE id=?")->execute([(int)$_POST['id']]);
        $success = 'Message deleted.';
    } elseif ($pa === 'delete_all') {
        $pdo->query("DELETE FROM contact_messages");
        $success = 'All messages deleted.';
    } elseif ($pa === 'mark_read') {
        $pdo->prepare("UPDATE contact_messages SET is_read=1 WHERE id=?")->execute([(int)$_POST['id']]);
    }
}

// Mark as read when viewing detail
$viewId = (int)($_GET['view'] ?? 0);
$viewMsg = null;
if ($viewId) {
    $pdo->prepare("UPDATE contact_messages SET is_read=1 WHERE id=?")->execute([$viewId]);
    $s = $pdo->prepare("SELECT * FROM contact_messages WHERE id=?");
    $s->execute([$viewId]);
    $viewMsg = $s->fetch();
}

$messages = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();
$unread = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read=0")->fetchColumn();
?>

<?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>

<?php if ($viewMsg): ?>
<!-- MESSAGE DETAIL -->
<div class="card" style="margin-bottom:1.5rem">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-envelope-open" style="color:var(--primary)"></i> Message Detail</span>
        <a href="messages.php" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Back to All</a>
    </div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem">
            <div>
                <div class="text-muted text-sm" style="margin-bottom:0.25rem">From</div>
                <div style="font-weight:700;font-size:1.05rem"><?= sanitize($viewMsg['name']) ?></div>
                <a href="mailto:<?= sanitize($viewMsg['email']) ?>" style="color:var(--primary);font-size:0.9rem"><?= sanitize($viewMsg['email']) ?></a>
            </div>
            <div>
                <div class="text-muted text-sm" style="margin-bottom:0.25rem">Received</div>
                <div style="font-weight:600"><?= date('F d, Y — g:i A', strtotime($viewMsg['created_at'])) ?></div>
                <div class="text-muted text-sm">IP: <?= sanitize($viewMsg['ip_address']) ?></div>
            </div>
        </div>
        <?php if ($viewMsg['subject']): ?>
        <div style="margin-bottom:1rem">
            <div class="text-muted text-sm" style="margin-bottom:0.25rem">Subject</div>
            <div style="font-weight:600;font-size:1rem"><?= sanitize($viewMsg['subject']) ?></div>
        </div>
        <?php endif; ?>
        <div>
            <div class="text-muted text-sm" style="margin-bottom:0.5rem">Message</div>
            <div style="background:var(--bg-card-hover);border:1px solid var(--border);border-radius:var(--radius);padding:1.25rem;line-height:1.8;white-space:pre-line"><?= sanitize($viewMsg['message']) ?></div>
        </div>
        <div style="display:flex;gap:0.75rem;margin-top:1.5rem;flex-wrap:wrap">
            <a href="mailto:<?= sanitize($viewMsg['email']) ?>?subject=Re: <?= urlencode($viewMsg['subject'] ?: 'Your message') ?>" class="btn btn-primary"><i class="fas fa-reply"></i> Reply via Email</a>
            <a href="https://wa.me/?text=Hi+<?= urlencode($viewMsg['name']) ?>" target="_blank" class="btn btn-success"><i class="fab fa-whatsapp"></i> WhatsApp</a>
            <form method="POST" style="display:inline">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $viewMsg['id'] ?>">
                <button class="btn btn-danger confirm-delete"><i class="fas fa-trash"></i> Delete</button>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- MESSAGES LIST -->
<div class="card">
    <div class="card-header">
        <span class="card-title">
            <i class="fas fa-inbox" style="color:var(--primary)"></i> All Messages (<?= count($messages) ?>)
            <?php if ($unread > 0): ?><span class="badge" style="margin-left:0.5rem;background:var(--danger);color:white;font-size:0.7rem;padding:2px 7px;border-radius:10px"><?= $unread ?> new</span><?php endif; ?>
        </span>
        <?php if (!empty($messages)): ?>
        <form method="POST" style="display:inline">
            <input type="hidden" name="action" value="delete_all">
            <button class="btn btn-sm btn-danger confirm-delete"><i class="fas fa-trash-alt"></i> Delete All</button>
        </form>
        <?php endif; ?>
    </div>
    <div class="table-wrap">
        <?php if (empty($messages)): ?>
        <div class="card-body" style="text-align:center;padding:3rem;color:var(--text-muted)">
            <i class="fas fa-inbox" style="font-size:3rem;margin-bottom:1rem;display:block;opacity:0.3"></i>
            No messages yet. Messages from your contact form will appear here.
        </div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th></th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $msg): ?>
                <tr class="message-row <?= !$msg['is_read'] ? 'unread' : '' ?>">
                    <td><?= !$msg['is_read'] ? '<span class="unread-dot"></span>' : '' ?></td>
                    <td style="font-weight:<?= !$msg['is_read'] ? '700' : '500' ?>"><?= sanitize($msg['name']) ?></td>
                    <td class="text-muted text-sm"><?= sanitize($msg['email']) ?></td>
                    <td class="truncate text-sm"><?= sanitize($msg['subject'] ?: '(No subject)') ?></td>
                    <td class="text-muted text-sm"><?= date('M d, Y', strtotime($msg['created_at'])) ?></td>
                    <td>
                        <div class="td-actions">
                            <a href="messages.php?view=<?= $msg['id'] ?>" class="btn btn-sm btn-secondary btn-icon" title="Read"><i class="fas fa-eye"></i></a>
                            <a href="mailto:<?= sanitize($msg['email']) ?>" class="btn btn-sm btn-secondary btn-icon" title="Reply"><i class="fas fa-reply"></i></a>
                            <form method="POST" style="display:inline">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $msg['id'] ?>">
                                <button class="btn btn-sm btn-danger btn-icon confirm-delete" title="Delete"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
