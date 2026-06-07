<?php
require_once 'db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: ./");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = "رمز التحقق غير صحيح، حاول مرة أخرى.";
    } else {
        $email = trim($_POST['email'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "البريد الإلكتروني غير صحيح.";
        } else {
            $stmt = $pdo->prepare("SELECT id, username FROM users WHERE email = :email AND role != 'admin'");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user) {
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

                $pdo->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (:uid, :tok, :exp)")
                    ->execute(['uid' => $user['id'], 'tok' => $token, 'exp' => $expires]);

                header("Location: reset_password.php?token=" . urlencode($token));
                exit();
            } else {
                $success = "إذا كان البريد مسجلاً لدينا، سيتم إرسال رابط إعادة التعيين.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LinguaTrack — نسيت كلمة المرور</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
    <div class="auth-box">
        <h2>نسيت كلمة المرور؟ 🔑</h2>
        <?php if (!empty($error)): ?>
            <div class="auth-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="auth-success"><?= htmlspecialchars($success) ?></div>
        <?php else: ?>
            <p style="color: var(--text-secondary); margin-bottom: 20px;">أدخل بريدك الإلكتروني لإعادة تعيين كلمة المرور.</p>
        <?php endif; ?>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= getCSRFToken() ?>">
            <input type="email" name="email" placeholder="البريد الإلكتروني" required>
            <button type="submit">إرسال رابط إعادة التعيين</button>
        </form>
        <a class="auth-link" href="login.php">العودة لتسجيل الدخول</a>
    </div>
</body>
</html>
