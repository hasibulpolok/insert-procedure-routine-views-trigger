<?php
$pageTitle = 'Services';
require_once 'includes/header.php';
$pdo = getDB();
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pa = $_POST['action'] ?? '';
    if ($pa === 'save') {
        $title = trim($_POST['title'] ?? '');
        $desc  = trim($_POST['description'] ?? '');
        $icon  = trim($_POST['icon'] ?? 'code');
        $sort  = (int)($_POST['sort_order'] ?? 0);
        $active= isset($_POST['is_active']) ? 1 : 0;
        $id    = (int)($_POST['id'] ?? 0);
        if (!$title) { $error = 'Title required.'; }
        else {
            if ($id) $pdo->prepare("UPDATE services SET title=?,description=?,icon=?,sort_order=?,is_active=? WHERE id=?")->execute([$title,$desc,$icon,$sort,$active,$id]);
            else $pdo->prepare("INSERT INTO services (title,description,icon,sort_order,is_active) VALUES (?,?,?,?,?)")->execute([$title,$desc,$icon,$sort,$active]);
            $success = 'Service saved!';
        }
    } elseif ($pa === 'delete') {
        $pdo->prepare("DELETE FROM services WHERE id=?")->execute([(int)$_POST['id']]);
        $success = 'Service deleted.';
    }
}

$services = $pdo->query("SELECT * FROM services ORDER BY sort_order ASC")->fetchAll();
$action = $_GET['action'] ?? 'list';
$editId = (int)($_GET['id'] ?? 0);
$editData = null;
if ($action === 'edit' && $editId) {
    $s = $pdo->prepare("SELECT * FROM services WHERE id=?"); $s->execute([$editId]);
    $editData = $s->fetch();
    if (!$editData) $action = 'list';
}
?>
<?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div><?php endif; ?>

<?php if ($action === 'add' || $action === 'edit'): ?>
<div class="card">
    <div class="card-header">
        <span class="card-title"><?= $action==='edit'?'Edit Service':'Add Service' ?></span>
        <a href="services.php" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= $editData['id'] ?? 0 ?>">
            <div class="form-group">
                <label class="form-label">Service Title *</label>
                <input type="text" name="title" class="form-input" value="<?= sanitize($editData['title'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-textarea" rows="4"><?= sanitize($editData['description'] ?? '') ?></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Icon</label>
                    <select name="icon" class="form-select">
                        <?php foreach (['code','layout','database','server','shopping-cart','settings','mobile','globe','shield','zap','users','mail'] as $ic): ?>
                        <option value="<?= $ic ?>" <?= ($editData['icon']??'code')===$ic?'selected':'' ?>><?= ucfirst($ic) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" class="form-input" value="<?= $editData['sort_order'] ?? 0 ?>">
                </div>
            </div>
            <label class="toggle-switch" style="margin-bottom:1.5rem">
                <input type="checkbox" name="is_active" <?= ($editData['is_active']??1)?'checked':'' ?>>
                <span class="toggle-slider"></span>
                <span class="toggle-label">Active</span>
            </label>
            <div style="display:flex;gap:0.75rem;margin-top:1rem">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
                <a href="services.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php else: ?>
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-briefcase" style="color:var(--primary)"></i> Services (<?= count($services) ?>)</span>
        <a href="services.php?action=add" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Add Service</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>#</th><th>Title</th><th>Description</th><th>Icon</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($services as $i => $s): ?>
                <tr>
                    <td><?= $i+1 ?></td>
                    <td style="font-weight:600"><?= sanitize($s['title']) ?></td>
                    <td class="truncate text-sm text-muted"><?= sanitize(substr($s['description'],0,80)) ?>...</td>
                    <td><code style="font-size:0.8rem;color:var(--primary)"><?= sanitize($s['icon']) ?></code></td>
                    <td><?= $s['is_active']?'<span class="badge-active">Active</span>':'<span class="badge-inactive">Hidden</span>' ?></td>
                    <td>
                        <div class="td-actions">
                            <a href="services.php?action=edit&id=<?= $s['id'] ?>" class="btn btn-sm btn-secondary btn-icon"><i class="fas fa-edit"></i></a>
                            <form method="POST" style="display:inline">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                <button class="btn btn-sm btn-danger btn-icon confirm-delete"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
<?php require_once 'includes/footer.php'; ?>
