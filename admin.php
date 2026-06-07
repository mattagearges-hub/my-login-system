<?php
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied. You must be an admin.");
}

$error = '';
$success = '';

// Read flashed messages from session
if (isset($_SESSION['admin_msg'])) {
    $msg = $_SESSION['admin_msg'];
    if ($msg['type'] === 'success') $success = $msg['text'];
    else $error = $msg['text'];
    unset($_SESSION['admin_msg']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = "رمز التحقق غير صحيح.";
    } else {
        $target_id = (int)($_POST['user_id'] ?? 0);
        if ($target_id <= 0) {
            $error = "معرف المستخدم غير صحيح.";
        } else {
            $check = $pdo->prepare("SELECT id, role FROM users WHERE id = :id");
            $check->execute(['id' => $target_id]);
            $target_user = $check->fetch();

            if (!$target_user || $target_user['role'] === 'admin') {
                $error = "لا يمكن تعديل حساب المشرف.";
            } else {
                if (isset($_POST['toggle_active'])) {
                    $current_status = (int)($_POST['current_status'] ?? 0);
                    $new_status = $current_status == 1 ? 0 : 1;
                    $stmt = $pdo->prepare("UPDATE users SET is_active = :status WHERE id = :id AND role != 'admin'");
                    $stmt->execute(['status' => $new_status, 'id' => $target_id]);
                    $_SESSION['admin_msg'] = ['type' => 'success', 'text' => 'تم تغيير حالة المستخدم بنجاح.'];
                } elseif (isset($_POST['delete_user'])) {
                    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id AND role != 'admin'");
                    $stmt->execute(['id' => $target_id]);
                    $_SESSION['admin_msg'] = ['type' => 'success', 'text' => 'تم حذف المستخدم بنجاح.'];
                }
            }
        }
    }
    // Redirect to prevent form re-submission
    header("Location: admin.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM users WHERE role != 'admin' ORDER BY id DESC");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LinguaTrack — لوحة التحكم</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="admin-page">
    <div class="card" style="max-width:1000px;margin:32px auto;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
            <h2 style="font-size:1.3em;font-weight:700;">🛡️ لوحة تحكم المشرف</h2>
            <div style="display:flex;gap:16px;">
                <a href="./" class="btn btn-sm btn-primary">🏠 الموقع</a>
                <a href="logout.php" class="btn btn-sm btn-danger-ghost">🚪 تسجيل الخروج</a>
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="auth-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="auth-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>م</th>
                        <th>اسم المستخدم</th>
                        <th>الإيميل</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= (int)$u['id'] ?></td>
                        <td style="font-weight:600;"><?= htmlspecialchars($u['username']) ?></td>
                        <td style="color:var(--text-secondary);font-size:0.85em;"><?= htmlspecialchars($u['email'] ?? '—') ?></td>
                        <td>
                            <?php if ($u['is_active'] == 1): ?>
                                <span class="badge" style="background:rgba(52,211,153,0.12);border-color:rgba(52,211,153,0.2);color:var(--success);">مفعل</span>
                            <?php else: ?>
                                <span class="badge" style="background:rgba(248,113,113,0.12);border-color:rgba(248,113,113,0.2);color:var(--danger);">غير مفعل</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="post" style="display:flex;gap:8px;flex-wrap:wrap;">
                                <input type="hidden" name="csrf_token" value="<?= getCSRFToken() ?>">
                                <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                                <input type="hidden" name="current_status" value="<?= (int)$u['is_active'] ?>">
                                <?php if ($u['is_active'] == 1): ?>
                                    <button type="submit" name="toggle_active" class="btn btn-sm btn-warning">إلغاء التفعيل</button>
                                <?php else: ?>
                                    <button type="submit" name="toggle_active" class="btn btn-sm btn-success">تفعيل</button>
                                <?php endif; ?>
                                <button type="submit" name="delete_user" class="btn btn-sm btn-danger-ghost" onclick="return confirm('هل أنت متأكد من حذف هذا الحساب نهائياً؟');">حذف</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (count($users) === 0): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted" style="padding:32px;">لا يوجد مستخدمين مسجلين حتى الآن.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
