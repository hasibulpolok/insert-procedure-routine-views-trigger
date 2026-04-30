<?php
// ============================================
// DevPolok Portfolio CMS - Configuration
// ============================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // Change to your MySQL username
define('DB_PASS', '');            // Change to your MySQL password
define('DB_NAME', 'devpolok_portfolio');
define('DB_CHARSET', 'utf8mb4');

define('SITE_URL', 'http://localhost/devpolok'); // Change to your URL
define('UPLOAD_DIR', __DIR__ . '/assets/uploads/');
define('UPLOAD_URL', SITE_URL . '/assets/uploads/');
define('ADMIN_SESSION_NAME', 'devpolok_admin');

// ============================================
// Database Connection (PDO)
// ============================================
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
        }
    }
    return $pdo;
}

// ============================================
// Helper Functions
// ============================================
function getSetting($key, $default = '') {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    return $row ? $row['setting_value'] : $default;
}

function getAllSettings() {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings");
    $settings = [];
    while ($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    return $settings;
}

function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function isAdminLoggedIn() {
    return isset($_SESSION[ADMIN_SESSION_NAME]) && !empty($_SESSION[ADMIN_SESSION_NAME]);
}

function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        redirect(SITE_URL . '/admin/login.php');
    }
}

function uploadImage($file, $folder = 'uploads') {
    $uploadPath = UPLOAD_DIR . $folder . '/';
    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0755, true);
    }
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'ico'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        return ['success' => false, 'message' => 'Invalid file type.'];
    }
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['success' => false, 'message' => 'File too large (max 5MB).'];
    }
    $filename = uniqid('img_', true) . '.' . $ext;
    $destination = $uploadPath . $filename;
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => true, 'filename' => $folder . '/' . $filename, 'url' => UPLOAD_URL . $folder . '/' . $filename];
    }
    return ['success' => false, 'message' => 'Upload failed.'];
}

function deleteImage($path) {
    $fullPath = UPLOAD_DIR . $path;
    if (file_exists($fullPath)) {
        unlink($fullPath);
    }
}

session_start();
