<?php
session_start();

require_once __DIR__ . '/models/Chatbot.php';

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'POST requis'], JSON_UNESCAPED_UNICODE);
    exit;
}

$raw = (string) file_get_contents('php://input');
$message = '';
$ct = strtolower($_SERVER['CONTENT_TYPE'] ?? '');

if ($raw !== '' && str_contains($ct, 'application/json')) {
    $data = json_decode($raw, true);
    $message = is_array($data) ? (string)($data['message'] ?? '') : '';
} else {
    $message = (string)($_POST['message'] ?? '');
}

$message = trim($message);
if (strlen($message) > 2000) {
    $message = substr($message, 0, 2000);
}

$loggedIn = isset($_SESSION['user']);
$name = null;
if ($loggedIn) {
    $full = trim((string)($_SESSION['user']['fullname'] ?? ''));
    $name = $full !== '' ? $full : null;
}

$out = Chatbot::analyze($message, $loggedIn, $name);
$category = $out['category'];
echo json_encode([
    'ok' => true,
    'reply' => $out['reply'],
    'category' => $category,
    'category_label' => Chatbot::categoryLabel($category),
], JSON_UNESCAPED_UNICODE);
