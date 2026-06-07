<?php
require_once 'db.php';

$email = $_GET['email'] ?? '';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = "رمز التحقق غير صحيح، حاول مرة أخرى.";
    } else {
        $email = trim($_POST['email']);
        $code = trim($_POST['code']);

        $stmt = $pdo->prepare("SELECT id, verification_code FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && $user['verification_code'] === $code) {
            $pdo->prepare("UPDATE users SET is_email_verified = 1, verification_code = NULL WHERE id = :id")->execute(['id' => $user['id']]);
            header("Location: login.php?msg=verified");
            exit();
        } else {
            $error = "الكود غير صحيح، حاول مرة أخرى.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LinguaTrack — تأكيد البريد</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
    <div class="auth-box">
        <h2>تأكيد الإيميل 📧</h2>
        <p style="color: var(--text-secondary); margin-bottom: 20px;">تم إرسال كود من 6 أرقام إلى بريدك الإلكتروني.</p>
        <?php if (!empty($error)): ?>
            <div class="auth-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= getCSRFToken() ?>">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
            <input type="text" name="code" placeholder="------" maxlength="6" required class="code-input" aria-label="كود التفعيل">
            <button type="submit">تأكيد الحساب</button>
        </form>
    </div>
</body>
</html>
