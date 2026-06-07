<?php
require_once 'db.php';
require_once 'config.php';
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = "رمز التحقق غير صحيح، حاول مرة أخرى.";
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if (empty($username) || empty($password) || empty($email)) {
            $error = "الرجاء إدخال جميع البيانات.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "البريد الإلكتروني غير صحيح.";
        } elseif (strlen($password) < 6) {
            $error = "كلمة المرور يجب أن تكون 6 أحرف على الأقل.";
        } else {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
            $stmt->execute(['username' => $username, 'email' => $email]);
            if ($stmt->fetch()) {
                $error = "اسم المستخدم أو البريد الإلكتروني موجود مسبقاً.";
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $verification_code = sprintf("%06d", mt_rand(1, 999999));

                $insert = $pdo->prepare("INSERT INTO users (username, password, email, verification_code, is_email_verified, is_active, role) VALUES (:username, :password, :email, :code, 0, 0, 'user')");
                if ($insert->execute([
                    'username' => $username,
                    'password' => $hashed,
                    'email' => $email,
                    'code' => $verification_code
                ])) {
                    $mail = new PHPMailer(true);
                    try {
                        $mail->isSMTP();
                        $mail->Host       = SMTP_HOST;
                        $mail->SMTPAuth   = true;
                        $mail->Username   = SMTP_USERNAME;
                        $mail->Password   = SMTP_PASSWORD;
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port       = 587;
                        $mail->SMTPOptions = [
                            'ssl' => [
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                                'allow_self_signed' => true,
                            ],
                        ];
                        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
                        $mail->addAddress($email, $username);
                        $mail->CharSet = 'UTF-8';
                        $mail->isHTML(true);
                        $mail->Subject = 'كود التفعيل - Smart Lang Planner';
                        $mail->Body = "مرحباً $username،<br><br>كود التفعيل الخاص بك هو: <b style='font-size: 24px; color: #667eea;'>$verification_code</b><br><br>شكراً لك.";
                        $mail->send();
                        header("Location: verify.php?email=" . urlencode($email));
                        exit();
                    } catch (Exception $e) {
                        $error = "فشل إرسال إيميل التفعيل: " . $mail->ErrorInfo;
                    }
                } else {
                    $error = "حدث خطأ أثناء التسجيل.";
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
    <title>LinguaTrack — إنشاء حساب</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
    <div class="auth-box">
        <h2>إنشاء حساب</h2>
        <?php if (!empty($error)): ?>
            <div class="auth-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= getCSRFToken() ?>">
            <input type="text" name="username" placeholder="اسم المستخدم" required aria-label="اسم المستخدم">
            <input type="email" name="email" placeholder="البريد الإلكتروني" required aria-label="البريد الإلكتروني">
            <div class="pw-wrap">
                <input type="password" name="password" id="password" placeholder="كلمة المرور (6 أحرف على الأقل)" minlength="6" required aria-label="كلمة المرور">
                <button type="button" class="pw-toggle" onclick="togglePassword('password',this)" aria-label="إظهار/إخفاء كلمة المرور">
                    <svg class="eye-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                    <svg class="eye-slash-icon" style="display:none;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                </button>
            </div>
            <button type="submit" style="margin-top: 0;">تسجيل</button>
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
        <a class="auth-link" href="login.php">لديك حساب بالفعل؟ تسجيل الدخول</a>
    </div>
</body>
</html>
