<?php
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $_SESSION['user_id']]);
$user = $stmt->fetch();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = "رمز التحقق غير صحيح.";
    } elseif (isset($_POST['update_profile'])) {
        $email = trim($_POST['email']);
        $new_password = trim($_POST['new_password']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "البريد الإلكتروني غير صحيح.";
        } else {
            $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
            $stmtCheck->execute(['email' => $email, 'id' => $user['id']]);
            if ($stmtCheck->fetch()) {
                $error = "هذا البريد مستخدم مسبقاً.";
            } else {
                if ($email !== $user['email']) {
                    $pdo->prepare("UPDATE users SET email = :email WHERE id = :id")->execute(['email' => $email, 'id' => $user['id']]);
                    $user['email'] = $email;
                    $success = "تم تحديث البريد الإلكتروني بنجاح.";
                }

                if (!empty($new_password)) {
                    if (strlen($new_password) < 6) {
                        $error = "كلمة المرور يجب أن تكون 6 أحرف على الأقل.";
                    } else {
                        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                        $pdo->prepare("UPDATE users SET password = :password WHERE id = :id")->execute(['password' => $hashed, 'id' => $user['id']]);
                        $success = "تم تحديث كلمة المرور بنجاح.";
                    }
                }
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
    <title>LinguaTrack — الملف الشخصي</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="profile-page">
    <div class="profile-box">
        <h2 style="font-size: 1.5em; margin-bottom: 24px; text-align: center;">الملف الشخصي 👤</h2>
        <?php if (!empty($error)): ?>
            <div style="color: #ff6b6b; margin-bottom: 15px; font-size: 0.9em; text-align: center;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div style="color: var(--success); margin-bottom: 15px; font-size: 0.9em; text-align: center;"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= getCSRFToken() ?>">
            <label class="profile-label">اسم المستخدم (غير قابل للتعديل)</label>
            <input type="text" value="<?= htmlspecialchars($user['username']) ?>" disabled class="profile-input" style="opacity:0.5;">

            <label class="profile-label">البريد الإلكتروني</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required class="profile-input">

            <label style="color: var(--text-secondary); display: block; margin-bottom: 6px; margin-top: 16px; font-size:0.88em;">كلمة المرور الجديدة <span style="color:var(--text-muted);font-weight:400;">(اتركها فارغة إذا لم ترد تغييرها)</span></label>
            <div class="pw-wrap">
                <input type="password" name="new_password" id="password" placeholder="كلمة مرور جديدة" class="profile-input">
                <button type="button" class="pw-toggle" onclick="togglePassword('password',this)" aria-label="إظهار/إخفاء كلمة المرور">
                    <svg class="eye-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                    <svg class="eye-slash-icon" style="display:none;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                </button>
            </div>

            <button type="submit" name="update_profile" class="btn btn-primary btn-block" style="margin-top:20px;padding:14px;">حفظ التعديلات</button>
        </form>
        <a href="./" class="auth-link">🏠 العودة للصفحة الرئيسية</a>
    </div>
    <script>
        function togglePassword(id, btn) {
            var x = document.getElementById(id);
            var isPass = x.type === "password";
            x.type = isPass ? "text" : "password";
            btn.querySelector('.eye-icon').style.display = isPass ? 'none' : '';
            btn.querySelector('.eye-slash-icon').style.display = isPass ? '' : 'none';
        }
    </script>
</body>
</html>
