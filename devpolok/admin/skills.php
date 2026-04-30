<?php
$pageTitle = 'Skills';
require_once 'includes/header.php';
$pdo = getDB();
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';
    if ($postAction === 'save') {
        $name       = trim($_POST['name'] ?? '');
        $percentage = min(100, max(0, (int)($_POST['percentage'] ?? 0)));
        $category   = trim($_POST['category'] ?? 'Frontend');
        $icon       = trim($_POST['icon'] ?? '');
        $sort_order = (int)($_POST['sort_order'] ?? 0);
        $is_active  = isset($_POST['is_active']) ? 1 : 0;
        $id         = (int)($_POST['id'] ?? 0);
        if (empty($name)) { $error = 'Name is required.'; }
        else {
            if ($id) {
                $pdo->prepare("UPDATE skills SET name=?,percentage=?,category=?,icon=?,sort_order=?,is_active=? WHERE id=?")->execute([$name,$percentage,$category,$icon,$sort_order,$is_active,$id]);
            } else {
                $pdo->prepare("INSERT INTO skills (name,percentage,category,icon,sort_order,is_active) VALUES (?,?,?,?,?,?)")->execute([$name,$percentage,$category,$icon,$sort_order,$is_active]);
            }
            $success = 'Skill saved!';
        }
    } elseif ($postAction === 'delete') {
        $pdo->prepare("DELETE FROM skills WHERE id=?")->execute([(int)$_POST['id']]);
        $success = 'Skill deleted.';
    }
}

$skills = $pdo->query("SELECT * FROM skills ORDER BY sort_order ASC, id ASC")->fetchAll();
$action = $_GET['action'] ?? 'list';
$editId = (int)($_GET['id'] ?? 0);
$editData = null;
if ($action === 'edit' && $editId) {
    $s = $pdo->prepare("SELECT * FROM skills WHERE id=?"); $s->execute([$editId]);
    $editData = $s->fetch();
    if (!$editData) $action = 'list';
}
?>

<?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div><?php endif; ?>

<?php if ($action === 'add' || $action === 'edit'): ?>
<div class="card">
    <div class="card-header">
        <span class="card-title"><?= $action==='edit'?'Edit Skill':'Add Skill' ?></span>
        <a href="skills.php" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
    <div class="card-body">
        <form method="POST" action="skills.php">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= $editData['id'] ?? 0 ?>">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Skill Name *</label>
                    <input type="text" name="name" class="form-input" value="<?= sanitize($editData['name'] ?? '') ?>" required placeholder="e.g. PHP">
                </div>
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select">
                        <?php foreach (['Frontend','Backend','Tools','Design','Other'] as $cat): ?>
                        <option value="<?= $cat ?>" <?= ($editData['category'] ?? 'Frontend') === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Proficiency (%) — <?php echo isset($editData['percentage']) ? $editData['percentage'] : '0' ?>%</label>
                    <input type="range" name="percentage" min="0" max="100" value="<?= $editData['percentage'] ?? 0 ?>" class="form-input" oninput="this.previousElementSibling.textContent='Proficiency (%) — '+this.value+'%'" style="padding:0.5rem 0">
                </div>
                <div class="form-group">
                    <label class="form-label">Icon Key</label>
                    <select name="icon" class="form-select">
                        <?php $icons = ['html5','css3','javascript','php','mysql','git','bootstrap','tailwind','python','nodejs','react','vue','laravel','wordpress','figma','other']; ?>
                        <?php foreach ($icons as $ico): ?>
                        <option value="<?= $ico ?>" <?= ($editData['icon'] ?? '') === $ico ? 'selected' : '' ?>><?= strtoupper($ico) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" class="form-input" value="<?= $editData['sort_order'] ?? 0 ?>">
                </div>
            </div>
            <label class="toggle-switch" style="margin-bottom:1.5rem">
                <input type="checkbox" name="is_active" <?= ($editData['is_active'] ?? 1) ? 'checked' : '' ?>>
                <span class="toggle-slider"></span>
                <span class="toggle-label">Active</span>
            </label>
            <div style="display:flex;gap:0.75rem;margin-top:1rem">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
                <a href="skills.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php else: ?>
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-code" style="color:var(--primary)"></i> Skills (<?= count($skills) ?>)</span>
        <a href="skills.php?action=add" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Add Skill</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>#</th><th>Name</th><th>Category</th><th>Level</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($skills as $i => $skill): ?>
                <tr>
                    <td><?= $i+1 ?></td>
                    <td style="font-weight:600"><?= sanitize($skill['name']) ?></td>
                    <td><span class="badge-tag"><?= sanitize($skill['category']) ?></span></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:0.75rem">
                            <div style="flex:1;height:6px;background:var(--border);border-radius:3px;max-width:120px">
                                <div style="width:<?= $skill['percentage'] ?>%;height:100%;background:linear-gradient(90deg,var(--primary),var(--accent));border-radius:3px"></div>
                            </div>
                            <span style="font-weight:700;color:var(--primary);font-size:0.85rem"><?= $skill['percentage'] ?>%</span>
                        </div>
                    </td>
                    <td><?= $skill['is_active'] ? '<span class="badge-active">Active</span>' : '<span class="badge-inactive">Hidden</span>' ?></td>
                    <td>
                        <div class="td-actions">
                            <a href="skills.php?action=edit&id=<?= $skill['id'] ?>" class="btn btn-sm btn-secondary btn-icon"><i class="fas fa-edit"></i></a>
                            <form method="POST" style="display:inline">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $skill['id'] ?>">
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
