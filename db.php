<?php
// db.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db_file = __DIR__ . '/app.sqlite';
$dsn = 'sqlite:' . $db_file;

try {
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Create users table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        is_active INTEGER DEFAULT 0,
        role TEXT DEFAULT 'user'
    )");

    // Add new columns for email verification (ignore if already exist)
    try { $pdo->exec("ALTER TABLE users ADD COLUMN email TEXT"); } catch (Exception $e) {}
    try { $pdo->exec("ALTER TABLE users ADD COLUMN is_email_verified INTEGER DEFAULT 0"); } catch (Exception $e) {}
    try { $pdo->exec("ALTER TABLE users ADD COLUMN verification_code TEXT"); } catch (Exception $e) {}

    // Progress persistence table
    $pdo->exec("CREATE TABLE IF NOT EXISTS user_progress (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL UNIQUE,
        current_level TEXT DEFAULT 'a1',
        completed_tasks TEXT DEFAULT '{}',
        streak INTEGER DEFAULT 0,
        last_study_date TEXT DEFAULT '',
        level_study_days TEXT DEFAULT '{\"a1\":0,\"a2\":0,\"b1\":0,\"b2\":0,\"c1\":0}',
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // Password reset tokens table
    $pdo->exec("CREATE TABLE IF NOT EXISTS password_resets (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        token TEXT NOT NULL,
        expires_at TEXT NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // Vocab table for user-added words/sentences
    $pdo->exec("CREATE TABLE IF NOT EXISTS vocab (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        word TEXT NOT NULL,
        language TEXT NOT NULL CHECK(language IN ('en','de')),
        type TEXT DEFAULT 'word' CHECK(type IN ('word','sentence')),
        study_date TEXT NOT NULL,
        created_at TEXT DEFAULT (datetime('now')),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // CSRF tokens table
    $pdo->exec("CREATE TABLE IF NOT EXISTS csrf_tokens (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        session_id TEXT NOT NULL,
        token TEXT NOT NULL,
        created_at TEXT DEFAULT (datetime('now'))
    )");

    // Check if admin exists, if not, create one
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'Matta'");
    $stmt->execute();

    if (!$stmt->fetch()) {
        $insert_admin = $pdo->prepare("INSERT INTO users (username, password, email, is_active, role, is_email_verified) VALUES ('Matta', :password, 'mattagearges@gmail.com', 1, 'admin', 1)");

        $admin_password = password_hash('MATTA@772005@MATTA', PASSWORD_DEFAULT);
        $insert_admin->execute(['password' => $admin_password]);
    } else {
        $pdo->exec("UPDATE users SET is_email_verified = 1 WHERE username = 'Matta'");
    }
} catch (PDOException $e) {
    die("Database Connection Error: " . $e->getMessage());
}

// ===== CSRF Protection Functions =====

function generateCSRFToken() {
    global $pdo;
    $token = bin2hex(random_bytes(32));
    $sessionId = session_id();
    $pdo->prepare("INSERT INTO csrf_tokens (session_id, token) VALUES (:sid, :tok)")
        ->execute(['sid' => $sessionId, 'tok' => $token]);
    $_SESSION['csrf_token'] = $token;
    return $token;
}

function getCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        return generateCSRFToken();
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token, $consume = true) {
    global $pdo;
    if (empty($token)) return false;
    $sessionId = session_id();
    $stmt = $pdo->prepare("SELECT id FROM csrf_tokens WHERE session_id = :sid AND token = :tok");
    $stmt->execute(['sid' => $sessionId, 'tok' => $token]);
    $row = $stmt->fetch();
    if ($row) {
        if ($consume) {
            $pdo->prepare("DELETE FROM csrf_tokens WHERE id = :id")->execute(['id' => $row['id']]);
            unset($_SESSION['csrf_token']);
        }
        return true;
    }
    // Fallback: validate against session token (for servers with SQLite permissions issues)
    if (!empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token)) {
        if ($consume) {
            unset($_SESSION['csrf_token']);
        }
        return true;
    }
    return false;
}

function csrfHiddenInput() {
    return '<input type="hidden" name="csrf_token" value="' . getCSRFToken() . '">';
}
?>
