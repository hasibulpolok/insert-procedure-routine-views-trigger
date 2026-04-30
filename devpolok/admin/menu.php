<?php
$pageTitle = 'Menu Manager';
require_once 'includes/header.php';
$pdo = getDB();
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pa = $_POST['action'] ?? '';
    if ($pa === 'save') {
        $title  = trim($_POST['title'] ?? '');
        $url    = trim($_POST['url'] ?? '');
        $sort   = (int)($_POST['sort_order'] ?? 0);
        $active = isset($_POST['is_active']) ? 1 : 0;
        $id     = (int)($_POST['id'] ?? 0);
        if (!$title || !$url) { $error = 'Title and URL are required.'; }
        else {
            if ($id) $pdo->prepare("UPDATE nav_menu SET title=?,url=?,sort_order=?,is_active=? WHERE id=?")->execute([$title,$url,$sort,$active,$id]);
            else $pdo->prepare("INSERT INTO nav_menu (title,url,sort_order,is_active) VALUES (?,?,?,?)")->execute([$title,$url,$sort,$active]);
            $success = 'Menu item saved!';
        }
    } elseif ($pa === 'delete') {
        $pdo->prepare("DELETE FROM nav_menu WHERE id=?")->execute([(int)$_POST['id']]);
        $success = 'Menu item deleted.';
    } elseif ($pa === 'toggle') {
        $id = (int)$_POST['id'];
        $pdo->prepare("UPDATE nav_menu SET is_active = 1 - is_active WHERE id=?")->execute([$id]);
        $success = 'Visibility updated.';
    } elseif ($pa === 'reorder') {
        $ids = $_POST['ids'] ?? [];
        foreach ($ids as $order => $mid) {
            $pdo->prepare("UPDATE nav_menu SET sort_order=? WHERE id=?")->execute([$order+1, (int)$mid]);
        }
        echo json_encode(['success'=>true]); exit;
    }
}

$menuItems = $pdo->query("SELECT * FROM nav_menu ORDER BY sort_order ASC")->fetchAll();
$action = $_GET['action'] ?? 'list';
$editId = (int)($_GET['id'] ?? 0);
$editData = null;
if ($action === 'edit' && $editId) {
    $s = $pdo->prepare("SELECT * FROM nav_menu WHERE id=?"); $s->execute([$editId]);
    $editData = $s->fetch(); if (!$editData) $action = 'list';
}
?>

<?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div><?php endif; ?>

<?php if ($action === 'add' || $action === 'edit'): ?>
<div class="card">
    <div class="card-header">
        <span class="card-title"><?= $action==='edit'?'Edit Menu Item':'Add Menu Item' ?></span>
        <a href="menu.php" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= $editData['id'] ?? 0 ?>">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Menu Label *</label>
                    <input type="text" name="title" class="form-input" value="<?= sanitize($editData['title'] ?? '') ?>" required placeholder="e.g. About">
                </div>
                <div class="form-group">
                    <label class="form-label">URL / Anchor *</label>
                    <input type="text" name="url" class="form-input" value="<?= sanitize($editData['url'] ?? '') ?>" required placeholder="e.g. #about or /page.php">
                    <p class="form-hint">Use #section for same-page anchors, or full URL for other pages.</p>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" class="form-input" value="<?= $editData['sort_order'] ?? count($menuItems)+1 ?>">
                </div>
            </div>
            <label class="toggle-switch" style="margin-bottom:1.5rem">
                <input type="checkbox" name="is_active" <?= ($editData['is_active']??1)?'checked':'' ?>>
                <span class="toggle-slider"></span>
                <span class="toggle-label">Show in Navigation</span>
            </label>
            <div style="display:flex;gap:0.75rem;margin-top:1rem">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
                <a href="menu.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php else: ?>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">
    <!-- Menu List -->
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-bars" style="color:var(--primary)"></i> Navigation Items</span>
            <a href="menu.php?action=add" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Add Item</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>⠿</th><th>Label</th><th>URL</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody id="sortableMenu">
                    <?php foreach ($menuItems as $item): ?>
                    <tr data-id="<?= $item['id'] ?>" style="cursor:grab">
                        <td style="color:var(--text-muted);font-size:1.2rem">⠿</td>
                        <td style="font-weight:600"><?= sanitize($item['title']) ?></td>
                        <td><code style="font-size:0.8rem;color:var(--primary)"><?= sanitize($item['url']) ?></code></td>
                        <td>
                            <form method="POST" style="display:inline">
                                <input type="hidden" name="action" value="toggle">
                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                <button class="toggle-switch" style="cursor:pointer" title="Toggle visibility">
                                    <input type="checkbox" <?= $item['is_active']?'checked':'' ?> style="pointer-events:none">
                                    <span class="toggle-slider"></span>
                                </button>
                            </form>
                        </td>
                        <td>
                            <div class="td-actions">
                                <a href="menu.php?action=edit&id=<?= $item['id'] ?>" class="btn btn-sm btn-secondary btn-icon"><i class="fas fa-edit"></i></a>
                                <form method="POST" style="display:inline">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                    <button class="btn btn-sm btn-danger btn-icon confirm-delete"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="card-body" style="border-top:1px solid var(--border);padding-top:1rem">
            <p class="text-muted text-sm"><i class="fas fa-info-circle"></i> Drag rows to reorder. Changes auto-save.</p>
        </div>
    </div>

    <!-- Preview -->
    <div class="card">
        <div class="card-header"><span class="card-title">👀 Navigation Preview</span></div>
        <div class="card-body">
            <div style="background:var(--bg-card-hover);border-radius:var(--radius);padding:1rem;display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap;border:1px solid var(--border)">
                <span style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.1rem">Dev<span style="color:var(--primary)">Polok</span></span>
                <div style="display:flex;gap:0.5rem;flex-wrap:wrap">
                    <?php foreach ($menuItems as $item): if (!$item['is_active']) continue; ?>
                    <a href="<?= sanitize($item['url']) ?>" style="padding:0.4rem 0.9rem;background:var(--bg-card);border:1px solid var(--border);border-radius:8px;font-size:0.82rem;font-weight:600;color:var(--text-muted)">
                        <?= sanitize($item['title']) ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <p class="text-muted text-sm" style="margin-top:1rem"><i class="fas fa-eye-slash"></i> Disabled items are hidden from visitors but shown here in grey.</p>
            <div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-top:0.5rem">
                <?php foreach ($menuItems as $item): if ($item['is_active']) continue; ?>
                <span style="padding:0.4rem 0.9rem;background:var(--bg-card);border:1px dashed var(--border);border-radius:8px;font-size:0.82rem;color:var(--text-muted);opacity:0.5">
                    <?= sanitize($item['title']) ?> (hidden)
                </span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Simple drag-to-reorder
const tbody = document.getElementById('sortableMenu');
if (tbody) {
    let dragging = null;
    tbody.querySelectorAll('tr').forEach(row => {
        row.draggable = true;
        row.addEventListener('dragstart', () => { dragging = row; row.style.opacity = '0.5'; });
        row.addEventListener('dragend', () => {
            row.style.opacity = '1';
            // Save order
            const ids = [...tbody.querySelectorAll('tr')].map(r => r.dataset.id);
            fetch('menu.php', {
                method: 'POST',
                headers: {'Content-Type':'application/x-www-form-urlencoded'},
                body: 'action=reorder&' + ids.map((id,i) => `ids[${i}]=${id}`).join('&')
            });
        });
        row.addEventListener('dragover', e => {
            e.preventDefault();
            const after = getDragAfterElement(tbody, e.clientY);
            if (after == null) tbody.appendChild(dragging);
            else tbody.insertBefore(dragging, after);
        });
    });
    function getDragAfterElement(container, y) {
        const els = [...container.querySelectorAll('tr:not(.dragging)')];
        return els.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            if (offset < 0 && offset > closest.offset) return { offset, element: child };
            return closest;
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    }
}
</script>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
