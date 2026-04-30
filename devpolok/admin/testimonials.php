<?php
$pageTitle = 'Testimonials';
require_once 'includes/header.php';
$pdo = getDB();
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pa = $_POST['action'] ?? '';
    if ($pa === 'save') {
        $name    = trim($_POST['client_name'] ?? '');
        $role    = trim($_POST['client_role'] ?? '');
        $country = trim($_POST['client_country'] ?? '');
        $review  = trim($_POST['review'] ?? '');
        $rating  = min(5, max(1, (int)($_POST['rating'] ?? 5)));
        $sort    = (int)($_POST['sort_order'] ?? 0);
        $active  = isset($_POST['is_active']) ? 1 : 0;
        $id      = (int)($_POST['id'] ?? 0);
        if (!$name || !$review) { $error = 'Name and review are required.'; }
        else {
            $image = $_POST['current_image'] ?? '';
            if (!empty($_FILES['client_image']['name'])) {
                $result = uploadImage($_FILES['client_image'], 'testimonials');
                if ($result['success']) { if ($image) deleteImage($image); $image = $result['filename']; }
            }
            if (!$error) {
                if ($id) $pdo->prepare("UPDATE testimonials SET client_name=?,client_role=?,client_country=?,client_image=?,review=?,rating=?,sort_order=?,is_active=? WHERE id=?")->execute([$name,$role,$country,$image,$review,$rating,$sort,$active,$id]);
                else $pdo->prepare("INSERT INTO testimonials (client_name,client_role,client_country,client_image,review,rating,sort_order,is_active) VALUES (?,?,?,?,?,?,?,?)")->execute([$name,$role,$country,$image,$review,$rating,$sort,$active]);
                $success = 'Testimonial saved!';
            }
        }
    } elseif ($pa === 'delete') {
        $t = $pdo->prepare("SELECT client_image FROM testimonials WHERE id=?"); $t->execute([(int)$_POST['id']]);
        $row = $t->fetch();
        if ($row && $row['client_image']) deleteImage($row['client_image']);
        $pdo->prepare("DELETE FROM testimonials WHERE id=?")->execute([(int)$_POST['id']]);
        $success = 'Testimonial deleted.';
    }
}

$testimonials = $pdo->query("SELECT * FROM testimonials ORDER BY sort_order ASC")->fetchAll();
$action = $_GET['action'] ?? 'list';
$editId = (int)($_GET['id'] ?? 0);
$editData = null;
if ($action === 'edit' && $editId) {
    $s = $pdo->prepare("SELECT * FROM testimonials WHERE id=?"); $s->execute([$editId]);
    $editData = $s->fetch(); if (!$editData) $action = 'list';
}
?>
<?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div><?php endif; ?>

<?php if ($action === 'add' || $action === 'edit'): ?>
<div class="card">
    <div class="card-header">
        <span class="card-title"><?= $action==='edit'?'Edit Testimonial':'Add Testimonial' ?></span>
        <a href="testimonials.php" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= $editData['id'] ?? 0 ?>">
            <input type="hidden" name="current_image" value="<?= sanitize($editData['client_image'] ?? '') ?>">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Client Name *</label>
                    <input type="text" name="client_name" class="form-input" value="<?= sanitize($editData['client_name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Role / Title</label>
                    <input type="text" name="client_role" class="form-input" value="<?= sanitize($editData['client_role'] ?? '') ?>" placeholder="CEO, Marketing Director...">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Country</label>
                    <input type="text" name="client_country" class="form-input" value="<?= sanitize($editData['client_country'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Rating (1-5)</label>
                    <select name="rating" class="form-select">
                        <?php for ($r=5;$r>=1;$r--): ?>
                        <option value="<?= $r ?>" <?= ($editData['rating']??5)==$r?'selected':'' ?>><?= $r ?> Star<?= $r>1?'s':'' ?> <?= str_repeat('⭐',$r) ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Review *</label>
                <textarea name="review" class="form-textarea" rows="5" required><?= sanitize($editData['review'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Client Photo</label>
                <?php if (!empty($editData['client_image'])): ?>
                <div><img src="<?= UPLOAD_URL.sanitize($editData['client_image']) ?>" style="width:60px;height:60px;border-radius:50%;object-fit:cover;margin-bottom:0.5rem"></div>
                <?php endif; ?>
                <input type="file" name="client_image" class="form-input img-file-input" data-preview="preview-testi" accept="image/*">
                <img id="preview-testi" class="img-preview" style="width:60px;height:60px;border-radius:50%;margin-top:0.5rem">
            </div>
            <div class="form-row">
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
                <a href="testimonials.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php else: ?>
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-star" style="color:var(--accent)"></i> Testimonials (<?= count($testimonials) ?>)</span>
        <a href="testimonials.php?action=add" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Add Testimonial</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>#</th><th>Client</th><th>Review</th><th>Rating</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($testimonials as $i => $t): ?>
                <tr>
                    <td><?= $i+1 ?></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:0.75rem">
                            <?php if ($t['client_image']): ?>
                            <img src="<?= UPLOAD_URL.sanitize($t['client_image']) ?>" style="width:36px;height:36px;border-radius:50%;object-fit:cover">
                            <?php else: ?>
                            <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--primary),#8b5cf6);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:0.9rem"><?= strtoupper(substr($t['client_name'],0,1)) ?></div>
                            <?php endif; ?>
                            <div>
                                <div style="font-weight:600;font-size:0.9rem"><?= sanitize($t['client_name']) ?></div>
                                <div class="text-muted text-sm"><?= sanitize($t['client_role']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="truncate text-sm">"<?= sanitize(substr($t['review'],0,80)) ?>..."</td>
                    <td style="color:var(--accent)"><?= str_repeat('★', $t['rating']) ?></td>
                    <td><?= $t['is_active']?'<span class="badge-active">Active</span>':'<span class="badge-inactive">Hidden</span>' ?></td>
                    <td>
                        <div class="td-actions">
                            <a href="testimonials.php?action=edit&id=<?= $t['id'] ?>" class="btn btn-sm btn-secondary btn-icon"><i class="fas fa-edit"></i></a>
                            <form method="POST" style="display:inline">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $t['id'] ?>">
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
