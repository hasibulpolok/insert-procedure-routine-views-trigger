<?php
$pageTitle = 'Projects';
require_once 'includes/header.php';
$pdo = getDB();

$success = $error = '';

// Handle actions
$action = $_GET['action'] ?? $_POST['action'] ?? 'list';
$editId = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';

    if ($postAction === 'save') {
        $title       = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $tech_stack  = trim($_POST['tech_stack'] ?? '');
        $category    = trim($_POST['category'] ?? '');
        $live_url    = trim($_POST['live_url'] ?? '');
        $github_url  = trim($_POST['github_url'] ?? '');
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $is_active   = isset($_POST['is_active']) ? 1 : 0;
        $sort_order  = (int)($_POST['sort_order'] ?? 0);
        $id          = (int)($_POST['id'] ?? 0);

        if (empty($title)) { $error = 'Title is required.'; }
        else {
            $image = $_POST['current_image'] ?? '';
            if (!empty($_FILES['image']['name'])) {
                $result = uploadImage($_FILES['image'], 'projects');
                if ($result['success']) {
                    if ($image) deleteImage($image);
                    $image = $result['filename'];
                } else { $error = $result['message']; }
            }

            if (!$error) {
                if ($id) {
                    $stmt = $pdo->prepare("UPDATE projects SET title=?,description=?,tech_stack=?,category=?,image=?,live_url=?,github_url=?,is_featured=?,is_active=?,sort_order=? WHERE id=?");
                    $stmt->execute([$title,$description,$tech_stack,$category,$image,$live_url,$github_url,$is_featured,$is_active,$sort_order,$id]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO projects (title,description,tech_stack,category,image,live_url,github_url,is_featured,is_active,sort_order) VALUES (?,?,?,?,?,?,?,?,?,?)");
                    $stmt->execute([$title,$description,$tech_stack,$category,$image,$live_url,$github_url,$is_featured,$is_active,$sort_order]);
                }
                $success = 'Project saved!';
                $action = 'list';
            }
        }
    } elseif ($postAction === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $proj = $pdo->prepare("SELECT image FROM projects WHERE id=?");
        $proj->execute([$id]);
        $row = $proj->fetch();
        if ($row && $row['image']) deleteImage($row['image']);
        $pdo->prepare("DELETE FROM projects WHERE id=?")->execute([$id]);
        $success = 'Project deleted.';
        $action = 'list';
    }
}

// Fetch for edit
$editData = null;
if ($action === 'edit' && $editId) {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id=?");
    $stmt->execute([$editId]);
    $editData = $stmt->fetch();
    if (!$editData) { $action = 'list'; }
}

$projects = $pdo->query("SELECT * FROM projects ORDER BY sort_order ASC, created_at DESC")->fetchAll();
?>

<?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= sanitize($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= sanitize($error) ?></div><?php endif; ?>

<?php if ($action === 'add' || $action === 'edit'): ?>
<!-- ADD / EDIT FORM -->
<div class="card">
    <div class="card-header">
        <span class="card-title"><?= $action === 'edit' ? 'Edit Project' : 'Add New Project' ?></span>
        <a href="projects.php" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
    <div class="card-body">
        <form method="POST" action="projects.php" enctype="multipart/form-data">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= $editData['id'] ?? 0 ?>">
            <input type="hidden" name="current_image" value="<?= sanitize($editData['image'] ?? '') ?>">

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Project Title *</label>
                    <input type="text" name="title" class="form-input" value="<?= sanitize($editData['title'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <input type="text" name="category" class="form-input" value="<?= sanitize($editData['category'] ?? '') ?>" placeholder="Web App, Website, CMS...">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-textarea" rows="4"><?= sanitize($editData['description'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Tech Stack <span class="text-muted text-sm">(comma separated)</span></label>
                <input type="text" name="tech_stack" class="form-input" value="<?= sanitize($editData['tech_stack'] ?? '') ?>" placeholder="PHP, MySQL, JavaScript, Tailwind CSS">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Live URL</label>
                    <input type="url" name="live_url" class="form-input" value="<?= sanitize($editData['live_url'] ?? '') ?>" placeholder="https://...">
                </div>
                <div class="form-group">
                    <label class="form-label">GitHub URL</label>
                    <input type="url" name="github_url" class="form-input" value="<?= sanitize($editData['github_url'] ?? '') ?>" placeholder="https://github.com/...">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Project Screenshot</label>
                <?php if (!empty($editData['image'])): ?>
                <div class="img-preview-wrap">
                    <img src="<?= UPLOAD_URL . sanitize($editData['image']) ?>" class="img-preview show" id="preview-proj" style="width:200px;height:120px;object-fit:cover;">
                </div>
                <?php endif; ?>
                <input type="file" name="image" class="form-input img-file-input" data-preview="preview-proj" accept="image/*" style="margin-top:0.5rem">
                <img id="preview-proj" class="img-preview" style="width:200px;height:120px;object-fit:cover;margin-top:0.5rem">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" class="form-input" value="<?= (int)($editData['sort_order'] ?? 0) ?>">
                </div>
            </div>
            <div style="display:flex;gap:1.5rem;margin-bottom:1.5rem">
                <label class="toggle-switch">
                    <input type="checkbox" name="is_active" <?= ($editData['is_active'] ?? 1) ? 'checked' : '' ?>>
                    <span class="toggle-slider"></span>
                    <span class="toggle-label">Active (visible on site)</span>
                </label>
                <label class="toggle-switch">
                    <input type="checkbox" name="is_featured" <?= ($editData['is_featured'] ?? 0) ? 'checked' : '' ?>>
                    <span class="toggle-slider"></span>
                    <span class="toggle-label">Featured Project</span>
                </label>
            </div>
            <div style="display:flex;gap:0.75rem">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Project</button>
                <a href="projects.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php else: ?>
<!-- LIST VIEW -->
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-laptop-code" style="color:var(--primary)"></i> All Projects (<?= count($projects) ?>)</span>
        <a href="projects.php?action=add" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Add Project</a>
    </div>
    <div class="table-wrap">
        <?php if (empty($projects)): ?>
        <div class="card-body text-muted">No projects yet. <a href="projects.php?action=add" style="color:var(--primary)">Add one!</a></div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Tech Stack</th>
                    <th>Featured</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projects as $i => $p): ?>
                <tr>
                    <td><?= $i+1 ?></td>
                    <td>
                        <?php if ($p['image']): ?>
                        <img src="<?= UPLOAD_URL . sanitize($p['image']) ?>" class="table-img">
                        <?php else: ?><div class="table-placeholder">💻</div><?php endif; ?>
                    </td>
                    <td>
                        <div style="font-weight:600"><?= sanitize($p['title']) ?></div>
                        <div class="text-muted text-sm truncate"><?= sanitize(substr($p['description'],0,60)) ?>...</div>
                    </td>
                    <td><?= sanitize($p['category']) ?></td>
                    <td>
                        <?php foreach (array_slice(explode(',', $p['tech_stack']), 0, 3) as $t): ?>
                        <span class="badge-tag"><?= sanitize(trim($t)) ?></span>
                        <?php endforeach; ?>
                    </td>
                    <td><?= $p['is_featured'] ? '<span class="badge-active">⭐ Yes</span>' : '<span class="text-muted text-sm">No</span>' ?></td>
                    <td><?= $p['is_active'] ? '<span class="badge-active">Active</span>' : '<span class="badge-inactive">Hidden</span>' ?></td>
                    <td>
                        <div class="td-actions">
                            <a href="projects.php?action=edit&id=<?= $p['id'] ?>" class="btn btn-sm btn-secondary btn-icon" title="Edit"><i class="fas fa-edit"></i></a>
                            <form method="POST" action="projects.php" style="display:inline">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger btn-icon confirm-delete" title="Delete"><i class="fas fa-trash"></i></button>
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
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
