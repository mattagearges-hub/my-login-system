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
$word = trim($input['word'] ?? '');
$language = $input['language'] ?? '';
$type = $input['type'] ?? 'word';
$studyDate = $input['study_date'] ?? date('Y-m-d');

if (empty($word) || !in_array($language, ['en', 'de']) || !in_array($type, ['word', 'sentence'])) {
    http_response_code(400);
    echo json_encode(['error' => 'بيانات غير مكتملة']);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO vocab (user_id, word, language, type, study_date) VALUES (:uid, :word, :lang, :type, :date)");
$stmt->execute([
    'uid' => $userId,
    'word' => $word,
    'lang' => $language,
    'type' => $type,
    'date' => $studyDate
]);

echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
