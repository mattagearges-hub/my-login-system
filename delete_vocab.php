<?php
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'غير مصرح']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'بيانات غير صالحة']);
    exit;
}

$token = $input['csrf_token'] ?? '';
if (!validateCSRFToken($token, false)) {
    http_response_code(403);
    echo json_encode(['error' => 'رمز غير صحيح']);
    exit;
}

$userId = (int)$_SESSION['user_id'];
$id = (int)($input['id'] ?? 0);

if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'معرف غير صحيح']);
    exit;
}

$stmt = $pdo->prepare("DELETE FROM vocab WHERE id = :id AND user_id = :uid");
$stmt->execute(['id' => $id, 'uid' => $userId]);

echo json_encode(['success' => true]);
