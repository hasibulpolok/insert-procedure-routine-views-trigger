<?php
// ============================================
// DevPolok Portfolio CMS - Public API
// ============================================
require_once 'config.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'contact':
        handleContact();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function handleContact() {
    $pdo = getDB();

    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Validation
    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
        return;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
        return;
    }
    if (strlen($name) > 150 || strlen($email) > 255 || strlen($message) > 5000) {
        echo json_encode(['success' => false, 'message' => 'Input too long.']);
        return;
    }

    $ip = $_SERVER['REMOTE_ADDR'] ?? '';

    // Rate limiting: 3 per hour per IP
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM contact_messages WHERE ip_address = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)");
    $stmt->execute([$ip]);
    if ((int)$stmt->fetchColumn() >= 3) {
        echo json_encode(['success' => false, 'message' => 'Too many messages. Please wait before sending again.']);
        return;
    }

    $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message, ip_address) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $email, $subject, $message, $ip]);

    echo json_encode(['success' => true, 'message' => 'Message sent! I\'ll get back to you soon. 🚀']);
}
