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

$type = $_GET['type'] ?? 'overview';
$userId = (int)$_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT completed_tasks, level_study_days, streak, last_study_date FROM user_progress WHERE user_id = :uid");
$stmt->execute(['uid' => $userId]);
$row = $stmt->fetch();

if (!$row) {
    echo json_encode([]);
    exit;
}

$tasks = json_decode($row['completed_tasks'], true) ?? [];
$levelDays = json_decode($row['level_study_days'], true) ?? [];
$streak = (int)($row['streak'] ?? 0);
$lastDate = $row['last_study_date'] ?? '';

$dailyData = [];
foreach ($tasks as $key => $val) {
    if (!$val) continue;
    $parts = explode('_', $key);
    if (count($parts) < 4) continue;
    $dateStr = $parts[0] . ' ' . $parts[1] . ' ' . $parts[2];
    $ts = strtotime($dateStr);
    if (!$ts) continue;
    $date = date('Y-m-d', $ts);
    $level = $parts[3] ?? 'a1';
    $lang = $parts[4] ?? 'en';

    if (!isset($dailyData[$date])) {
        $dailyData[$date] = ['date' => $date, 'en_done' => 0, 'de_done' => 0, 'total' => 0, 'level' => $level];
    }
    if ($lang === 'en') $dailyData[$date]['en_done']++;
    if ($lang === 'de') $dailyData[$date]['de_done']++;
    $dailyData[$date]['total']++;
}

$dailyData = array_values($dailyData);
usort($dailyData, fn($a, $b) => strcmp($a['date'], $b['date']));

$today = date('Y-m-d');
$last30 = date('Y-m-d', strtotime('-30 days'));
$dailyData = array_filter($dailyData, fn($d) => $d['date'] >= $last30 && $d['date'] <= $today);
$dailyData = array_values($dailyData);

if ($type === 'daily') {
    echo json_encode($dailyData);
    exit;
}

if ($type === 'overview') {
    $totalDays = count($dailyData);
    $levelDaysStr = json_encode($levelDays);
    echo json_encode([
        'total_days' => $totalDays,
        'streak' => $streak,
        'last_study_date' => $lastDate,
        'level_days' => $levelDays,
        'daily' => $dailyData
    ]);
    exit;
}

if ($type === 'weekly') {
    $weekly = [];
    foreach ($dailyData as $d) {
        $week = date('o-W', strtotime($d['date']));
        if (!isset($weekly[$week])) {
            $weekly[$week] = ['week' => $week, 'en_done' => 0, 'de_done' => 0, 'total' => 0];
        }
        $weekly[$week]['en_done'] += $d['en_done'];
        $weekly[$week]['de_done'] += $d['de_done'];
        $weekly[$week]['total'] += $d['total'];
    }
    echo json_encode(array_values($weekly));
    exit;
}

echo json_encode($dailyData);
