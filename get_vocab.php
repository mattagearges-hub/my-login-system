<?php
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'غير مصرح']);
    exit;
}

$token = $_GET['csrf_token'] ?? '';
if (!validateCSRFToken($token, false)) {
    http_response_code(403);
    echo json_encode(['error' => 'رمز غير صحيح']);
    exit;
}

$userId = (int)$_SESSION['user_id'];
$date = $_GET['date'] ?? date('Y-m-d');
$language = $_GET['language'] ?? '';

$sql = "SELECT id, word, language, type, study_date, created_at FROM vocab WHERE user_id = :uid AND study_date = :date";
$params = ['uid' => $userId, 'date' => $date];

if ($language && in_array($language, ['en', 'de'])) {
    $sql .= " AND language = :lang";
    $params['lang'] = $language;
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

echo json_encode($rows);
