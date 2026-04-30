<?php
$pageTitle = 'Site Settings';
require_once 'includes/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = getDB();
    $action = $_POST['action'] ?? '';

    if ($action === 'save_settings') {
        $fields = [
            'site_title','site_tagline','site_description','site_keywords',
            'hero_title','hero_subtitle','hero_description','hero_cta_primary','hero_cta_secondary',
            'about_title','about_bio','about_experience','about_projects','about_clients',
            'services_title','skills_title','projects_title','contact_title',
            'contact_email','contact_whatsapp','contact_location','footer_text',
            'social_github','social_linkedin','social_instagram','social_fiverr',
            'color_primary','color_accent','dark_mode_default','typing_words'
        ];
        foreach ($fields as $key) {
            if (isset($_POST[$key])) {
                $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
                $stmt->execute([$key, $_POST[$key], $_POST[$key]]);
            }
        }

        // Handle image uploads
        $imageFields = ['site_logo'=>'logo','site_favicon'=>'logo','about_image'=>'about'];
        foreach ($imageFields as $field => $folder) {
            if (!empty($_FILES[$field]['name'])) {
                $result = uploadImage($_FILES[$field], $folder);
                if ($result['success']) {
                    $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
                    $stmt->execute([$field, $result['filename'], $result['filename']]);
                }
            }
        }

        $success = 'Settings saved successfully!';
    }
}

$settings = getAllSettings();
?>

<?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= sanitize($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= sanitize($error) ?></div><?php endif; ?>

<form method="POST" action="" enctype="multipart/form-data">
    <input type="hidden" name="action" value="save_settings">

    <div class="settings-tabs">
        <button type="button" class="settings-tab active" data-panel="panel-general">⚙️ General</button>
        <button type="button" class="settings-tab" data-panel="panel-hero">🦸 Hero</button>
        <button type="button" class="settings-tab" data-panel="panel-about">👤 About</button>
        <button type="button" class="settings-tab" data-panel="panel-contact">📩 Contact</button>
        <button type="button" class="settings-tab" data-panel="panel-social">🔗 Social</button>
        <button type="button" class="settings-tab" data-panel="panel-appearance">🎨 Appearance</button>
        <button type="button" class="settings-tab" data-panel="panel-seo">🔍 SEO</button>
    </div>

    <!-- GENERAL -->
    <div id="panel-general" class="settings-panel active">
        <div class="card mb-6">
            <div class="card-header"><span class="card-title">Site Identity</span></div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Site Title</label>
                        <input type="text" name="site_title" class="form-input" value="<?= sanitize($settings['site_title'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tagline</label>
                        <input type="text" name="site_tagline" class="form-input" value="<?= sanitize($settings['site_tagline'] ?? '') ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Site Logo</label>
                    <?php if (!empty($settings['site_logo'])): ?>
                    <div class="img-preview-wrap">
                        <img src="<?= UPLOAD_URL . sanitize($settings['site_logo']) ?>" class="img-preview show" id="preview-logo" style="height:60px;width:auto;object-fit:contain;">
                    </div>
                    <?php endif; ?>
                    <input type="file" name="site_logo" class="form-input img-file-input" data-preview="preview-logo" accept="image/*" style="margin-top:0.5rem">
                    <p class="form-hint">Upload logo (PNG/SVG recommended). Leave empty to use text logo.</p>
                </div>
                <div class="form-group">
                    <label class="form-label">Favicon</label>
                    <input type="file" name="site_favicon" class="form-input img-file-input" data-preview="preview-favicon" accept="image/*,image/x-icon">
                    <p class="form-hint">Upload .ico, .png (32x32 recommended)</p>
                </div>
                <div class="form-group">
                    <label class="form-label">Footer Text</label>
                    <input type="text" name="footer_text" class="form-input" value="<?= sanitize($settings['footer_text'] ?? '') ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- HERO -->
    <div id="panel-hero" class="settings-panel">
        <div class="card mb-6">
            <div class="card-header"><span class="card-title">Hero Section</span></div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Hero Title <span class="text-muted text-sm">(HTML allowed — use &lt;span class="highlight"&gt;name&lt;/span&gt;)</span></label>
                    <input type="text" name="hero_title" class="form-input" value="<?= htmlspecialchars($settings['hero_title'] ?? '', ENT_QUOTES) ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Typing Words <span class="text-muted text-sm">(comma separated)</span></label>
                    <input type="text" name="typing_words" class="form-input" value="<?= sanitize($settings['typing_words'] ?? '') ?>">
                    <p class="form-hint">Example: Full Stack Developer,PHP Expert,UI/UX Enthusiast</p>
                </div>
                <div class="form-group">
                    <label class="form-label">Hero Description</label>
                    <textarea name="hero_description" class="form-textarea"><?= sanitize($settings['hero_description'] ?? '') ?></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Primary CTA Button Text</label>
                        <input type="text" name="hero_cta_primary" class="form-input" value="<?= sanitize($settings['hero_cta_primary'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Secondary CTA Button Text</label>
                        <input type="text" name="hero_cta_secondary" class="form-input" value="<?= sanitize($settings['hero_cta_secondary'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ABOUT -->
    <div id="panel-about" class="settings-panel">
        <div class="card mb-6">
            <div class="card-header"><span class="card-title">About Section</span></div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Section Title</label>
                    <input type="text" name="about_title" class="form-input" value="<?= sanitize($settings['about_title'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Bio / Description</label>
                    <textarea name="about_bio" class="form-textarea" rows="6"><?= sanitize($settings['about_bio'] ?? '') ?></textarea>
                </div>
                <div class="form-row-3">
                    <div class="form-group">
                        <label class="form-label">Years Experience</label>
                        <input type="text" name="about_experience" class="form-input" value="<?= sanitize($settings['about_experience'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Projects Completed</label>
                        <input type="text" name="about_projects" class="form-input" value="<?= sanitize($settings['about_projects'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Happy Clients</label>
                        <input type="text" name="about_clients" class="form-input" value="<?= sanitize($settings['about_clients'] ?? '') ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Profile Photo</label>
                    <?php if (!empty($settings['about_image'])): ?>
                    <div class="img-preview-wrap">
                        <img src="<?= UPLOAD_URL . sanitize($settings['about_image']) ?>" class="img-preview show" id="preview-about" style="width:100px;height:120px;object-fit:cover;">
                    </div>
                    <?php endif; ?>
                    <input type="file" name="about_image" class="form-input img-file-input" data-preview="preview-about" accept="image/*" style="margin-top:0.5rem">
                </div>
                <div class="form-group">
                    <label class="form-label">Section Labels</label>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Skills Section Title</label>
                        <input type="text" name="skills_title" class="form-input" value="<?= sanitize($settings['skills_title'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Services Section Title</label>
                        <input type="text" name="services_title" class="form-input" value="<?= sanitize($settings['services_title'] ?? '') ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Projects Section Title</label>
                        <input type="text" name="projects_title" class="form-input" value="<?= sanitize($settings['projects_title'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Contact Section Title</label>
                        <input type="text" name="contact_title" class="form-input" value="<?= sanitize($settings['contact_title'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CONTACT -->
    <div id="panel-contact" class="settings-panel">
        <div class="card mb-6">
            <div class="card-header"><span class="card-title">Contact Info</span></div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="contact_email" class="form-input" value="<?= sanitize($settings['contact_email'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">WhatsApp Number</label>
                        <input type="text" name="contact_whatsapp" class="form-input" value="<?= sanitize($settings['contact_whatsapp'] ?? '') ?>">
                        <p class="form-hint">Without country code (e.g. 01765967395)</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Location</label>
                    <input type="text" name="contact_location" class="form-input" value="<?= sanitize($settings['contact_location'] ?? '') ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- SOCIAL -->
    <div id="panel-social" class="settings-panel">
        <div class="card mb-6">
            <div class="card-header"><span class="card-title">Social Media Links</span></div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label"><i class="fab fa-github"></i> GitHub URL</label>
                    <input type="url" name="social_github" class="form-input" value="<?= sanitize($settings['social_github'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fab fa-linkedin"></i> LinkedIn URL</label>
                    <input type="url" name="social_linkedin" class="form-input" value="<?= sanitize($settings['social_linkedin'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fab fa-instagram"></i> Instagram URL</label>
                    <input type="url" name="social_instagram" class="form-input" value="<?= sanitize($settings['social_instagram'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-briefcase"></i> Fiverr Profile URL</label>
                    <input type="url" name="social_fiverr" class="form-input" value="<?= sanitize($settings['social_fiverr'] ?? '') ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- APPEARANCE -->
    <div id="panel-appearance" class="settings-panel">
        <div class="card mb-6">
            <div class="card-header"><span class="card-title">Colors & Theme</span></div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Primary Color</label>
                        <div style="display:flex;gap:0.75rem;align-items:center">
                            <input type="color" name="color_primary" value="<?= sanitize($settings['color_primary'] ?? '#6366f1') ?>" style="width:50px;height:38px;border-radius:8px;border:1px solid var(--border);background:none;cursor:pointer;padding:2px;">
                            <input type="text" id="color_primary_text" class="form-input" value="<?= sanitize($settings['color_primary'] ?? '#6366f1') ?>" style="flex:1" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Accent Color</label>
                        <div style="display:flex;gap:0.75rem;align-items:center">
                            <input type="color" name="color_accent" value="<?= sanitize($settings['color_accent'] ?? '#f59e0b') ?>" style="width:50px;height:38px;border-radius:8px;border:1px solid var(--border);background:none;cursor:pointer;padding:2px;">
                            <input type="text" id="color_accent_text" class="form-input" value="<?= sanitize($settings['color_accent'] ?? '#f59e0b') ?>" style="flex:1" readonly>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Default Theme Mode</label>
                    <select name="dark_mode_default" class="form-select">
                        <option value="dark" <?= ($settings['dark_mode_default'] ?? 'dark') === 'dark' ? 'selected' : '' ?>>Dark Mode</option>
                        <option value="light" <?= ($settings['dark_mode_default'] ?? 'dark') === 'light' ? 'selected' : '' ?>>Light Mode</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- SEO -->
    <div id="panel-seo" class="settings-panel">
        <div class="card mb-6">
            <div class="card-header"><span class="card-title">SEO Meta Tags</span></div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Meta Description</label>
                    <textarea name="site_description" class="form-textarea" rows="3"><?= sanitize($settings['site_description'] ?? '') ?></textarea>
                    <p class="form-hint">Recommended: 150-160 characters</p>
                </div>
                <div class="form-group">
                    <label class="form-label">Meta Keywords</label>
                    <input type="text" name="site_keywords" class="form-input" value="<?= sanitize($settings['site_keywords'] ?? '') ?>">
                    <p class="form-hint">Comma-separated keywords</p>
                </div>
            </div>
        </div>
    </div>

    <div style="position:sticky;bottom:0;background:var(--bg-secondary);padding:1rem 0;border-top:1px solid var(--border);margin-top:1.5rem">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-save"></i> Save All Settings
        </button>
    </div>
</form>

<script>
document.querySelector('input[name="color_primary"]').addEventListener('input', function() {
    document.getElementById('color_primary_text').value = this.value;
});
document.querySelector('input[name="color_accent"]').addEventListener('input', function() {
    document.getElementById('color_accent_text').value = this.value;
});
</script>

<?php require_once 'includes/footer.php'; ?>
