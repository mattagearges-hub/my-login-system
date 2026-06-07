<?php
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    http_response_code(400);
    exit;
}

if (!validateCSRFToken($data['csrf_token'] ?? '', false)) {
    http_response_code(403);
    exit;
}

$userId = $_SESSION['user_id'];
$currentLevel = $data['current_level'] ?? 'a1';
$completedTasks = $data['completed_tasks'] ?? '{}';
$streak = (int)($data['streak'] ?? 0);
$lastStudyDate = $data['last_study_date'] ?? '';
$levelStudyDays = $data['level_study_days'] ?? '{}';

if (is_array($completedTasks)) $completedTasks = json_encode($completedTasks);
if (is_array($levelStudyDays)) $levelStudyDays = json_encode($levelStudyDays);

$stmt = $pdo->prepare("INSERT INTO user_progress (user_id, current_level, completed_tasks, streak, last_study_date, level_study_days)
    VALUES (:uid, :cl, :ct, :st, :lsd, :lsd_json)
    ON CONFLICT(user_id) DO UPDATE SET
        current_level = :cl2,
        completed_tasks = :ct2,
        streak = :st2,
        last_study_date = :lsd2,
        level_study_days = :lsd_json2");

$stmt->execute([
    'uid' => $userId,
    'cl' => $currentLevel, 'ct' => $completedTasks, 'st' => $streak,
    'lsd' => $lastStudyDate, 'lsd_json' => $levelStudyDays,
    'cl2' => $currentLevel, 'ct2' => $completedTasks, 'st2' => $streak,
    'lsd2' => $lastStudyDate, 'lsd_json2' => $levelStudyDays
]);

echo json_encode(['ok' => true]);
