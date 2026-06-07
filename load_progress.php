<?php
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit;
}

$token = $_GET['csrf_token'] ?? '';
if (!validateCSRFToken($token, false)) {
    http_response_code(403);
    exit;
}

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM user_progress WHERE user_id = :uid");
$stmt->execute(['uid' => $userId]);
$progress = $stmt->fetch();

if ($progress) {
    echo json_encode([
        'current_level' => $progress['current_level'],
        'completed_tasks' => $progress['completed_tasks'],
        'streak' => $progress['streak'],
        'last_study_date' => $progress['last_study_date'],
        'level_study_days' => $progress['level_study_days']
    ]);
} else {
    echo json_encode(null);
}
