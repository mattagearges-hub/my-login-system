<?php
require_once 'db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: ./");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = "رمز التحقق غير صحيح، حاول مرة أخرى.";
    } else {
        $login = trim($_POST['email']);
        $password = trim($_POST['password']);

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email OR username = :username");
        $stmt->execute(['email' => $login, 'username' => $login]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if (isset($user['is_email_verified']) && $user['is_email_verified'] == 0 && $user['role'] !== 'admin') {
                header("Location: verify.php?email=" . urlencode($user['email'] ?? ''));
                exit();
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['is_active'] = $user['is_active'];

            generateCSRFToken();

            header("Location: ./");
            exit();
        } else {
            $error = "البريد الإلكتروني أو كلمة المرور غير صحيحة.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LinguaTrack — تسجيل الدخول</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
    <div class="auth-box">
        <h2>تسجيل الدخول</h2>
        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'registered'): ?>
            <div class="auth-success">تم التسجيل بنجاح! يرجى تسجيل الدخول.</div>
        <?php endif; ?>
        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'verified'): ?>
            <div class="auth-success">تم التحقق من البريد الإلكتروني بنجاح! يمكنك تسجيل الدخول الآن.</div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="auth-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= getCSRFToken() ?>">
            <input type="email" name="email" placeholder="البريد الإلكتروني" required aria-label="البريد الإلكتروني" dir="ltr" style="text-align: right;">
            <div class="pw-wrap">
                <input type="password" name="password" id="password" placeholder="كلمة المرور" required aria-label="كلمة المرور">
                <button type="button" class="pw-toggle" onclick="togglePassword('password',this)" aria-label="إظهار/إخفاء كلمة المرور">
                    <svg class="eye-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                    <svg class="eye-slash-icon" style="display:none;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                </button>
            </div>
            <button type="submit" style="margin-top: 0;">دخول</button>
        </form>
        <script>
            function togglePassword(id, btn) {
                var x = document.getElementById(id);
                var isPass = x.type === "password";
                x.type = isPass ? "text" : "password";
                btn.querySelector('.eye-icon').style.display = isPass ? 'none' : '';
                btn.querySelector('.eye-slash-icon').style.display = isPass ? '' : 'none';
            }
        </script>
        <a class="auth-link" href="forgot_password.php">نسيت كلمة المرور؟</a>
        <a class="auth-link" href="signup.php">ليس لديك حساب؟ إنشاء حساب جديد</a>
    </div>
</body>
</html>
