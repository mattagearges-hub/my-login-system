<?php
require_once 'db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: ./");
    exit();
}

$error = '';
$success = '';
$token = $_GET['token'] ?? '';

// Verify token
if (!empty($token)) {
    $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = :tok AND expires_at > datetime('now')");
    $stmt->execute(['tok' => $token]);
    $reset = $stmt->fetch();

    if (!$reset) {
        $error = "الرابط غير صالح أو منتهي الصلاحية.";
        $token = '';
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($token)) {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = "رمز التحقق غير صحيح.";
    } else {
        $password = trim($_POST['password'] ?? '');
        $confirm = trim($_POST['confirm_password'] ?? '');

        if (strlen($password) < 6) {
            $error = "كلمة المرور يجب أن تكون 6 أحرف على الأقل.";
        } elseif ($password !== $confirm) {
            $error = "كلمتا المرور غير متطابقتين.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE users SET password = :pw WHERE id = :uid")
                ->execute(['pw' => $hashed, 'uid' => $reset['user_id']]);
            $pdo->prepare("DELETE FROM password_resets WHERE id = :id")
                ->execute(['id' => $reset['id']]);
            $success = "تم إعادة تعيين كلمة المرور بنجاح! يمكنك تسجيل الدخول الآن.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LinguaTrack — إعادة تعيين كلمة المرور</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
    <div class="auth-box">
        <h2>إعادة تعيين كلمة المرور 🔐</h2>
        <?php if (!empty($error)): ?>
            <div class="auth-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="auth-success"><?= htmlspecialchars($success) ?></div>
            <a class="auth-link" href="login.php">تسجيل الدخول</a>
        <?php elseif (!empty($token)): ?>
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= getCSRFToken() ?>">
                <input type="password" name="password" placeholder="كلمة المرور الجديدة" minlength="6" required>
                <input type="password" name="confirm_password" placeholder="تأكيد كلمة المرور" minlength="6" required>
                <button type="submit">تغيير كلمة المرور</button>
            </form>
        <?php else: ?>
            <p style="color: var(--danger);">الرابط غير صالح أو منتهي الصلاحية.</p>
            <a class="auth-link" href="forgot_password.php">طلب رابط جديد</a>
        <?php endif; ?>
    </div>
</body>
</html>
