<?php
require_once 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$stmt = $pdo->prepare("SELECT is_active FROM users WHERE id = :id");
$stmt->execute(['id' => $_SESSION['user_id']]);
$user = $stmt->fetch();
if ($user && $user['is_active'] == 1) { $_SESSION['is_active'] = 1; header("Location: ./"); exit(); }
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LinguaTrack — بانتظار التفعيل</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
    <div class="auth-box" style="text-align:center;">
        <div style="font-size:3em;margin-bottom:12px;">⏳</div>
        <h2 style="margin-bottom:16px;">حسابك بانتظار التفعيل</h2>
        <p style="color:var(--text-secondary);font-size:1.1em;line-height:1.8;margin-bottom:24px;">
            للاستمتاع بالخطة الذكية لتعلم اللغات، يرجى سداد رسوم الاشتراك.
        </p>
        <div style="font-size:1.5em;color:var(--success);font-weight:bold;margin-bottom:24px;">
            2,900 ج.م — مدى الحياة
        </div>
        <p style="color:var(--text-muted);margin-bottom:32px;">
            بعد الدفع، سيقوم المشرف بتفعيل حسابك قريباً جداً.
        </p>
        <a href="logout.php" style="background:var(--danger);color:white;padding:12px 24px;border-radius:12px;text-decoration:none;font-weight:600;">تسجيل الخروج</a>
    </div>
</body>
</html>
