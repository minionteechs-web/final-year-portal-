<?php
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    header('Location: ' . dashboard_url_for_role(current_user()['role']));
    exit;
}

$error = '';
$success = '';
$resetLink = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $email = trim($_POST['email'] ?? '');

        $rlKey = rate_limit_key('forgot', $email);
        if (is_rate_limited($rlKey)) {
            record_rate_limit_failure($rlKey);
            $error = 'Too many password reset requests. Please try again in 15 minutes.';
        } elseif ($email === '') {
            record_rate_limit_failure($rlKey);
            $error = 'Please enter your email address.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            record_rate_limit_failure($rlKey);
            $error = 'Please enter a valid email address.';
        } else {
            $stmt = db()->prepare('SELECT id, email FROM users WHERE email = ? AND is_active = 1 LIMIT 1');
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($user) {
                // Invalidate any previous unused tokens for this user
                $del = db()->prepare('DELETE FROM password_resets WHERE user_id = ? AND used = 0');
                $del->bind_param('i', $user['id']);
                $del->execute();
                $del->close();

                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour

                $ins = db()->prepare('INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)');
                $ins->bind_param('iss', $user['id'], $token, $expires);
                $ins->execute();
                $ins->close();

                $base = base_path();
                $isSecure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
                            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
                $scheme = $isSecure ? 'https' : 'http';
                $resetLink = $scheme . '://' . $_SERVER['HTTP_HOST'] . $base . '/reset_password.php?token=' . $token;

                clear_rate_limit($rlKey);
                $success = 'If an account exists for that email, a password reset link has been generated.';
            } else {
                // Generic response — do not reveal whether email exists
                clear_rate_limit($rlKey);
                $success = 'If an account exists for that email, a password reset link has been generated.';
            }
        }
    }
}

$pageTitle = 'Forgot Password';
$activeNav = 'forgot';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container auth-layout">
    <div class="auth-card">
        <h1>Forgot Password</h1>
        <p class="lead">Enter your email and we’ll generate a reset link.</p>

        <?php if ($error): ?>
            <div class="flash flash-error"><?= e($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="flash flash-success"><?= e($success) ?></div>
        <?php endif; ?>

        <?php if ($resetLink): ?>
            <div class="flash flash-info" style="background:#eef4ff;border:1px solid #b8ccff;">
                <strong>Reset link (dev mode — no mail server configured):</strong><br>
                <a href="<?= e($resetLink) ?>" style="word-break:break-all;"><?= e($resetLink) ?></a>
            </div>
        <?php endif; ?>

        <form method="post" class="form-grid" style="margin-top:1rem;">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>" placeholder="you@unn.edu.ng">
            </div>
            <button type="submit" class="btn btn-green">Send Reset Link</button>
        </form>

        <p style="margin-top:1.25rem;color:var(--muted);">Remember your password? <a href="<?= e(base_path()) ?>/login.php">Log in</a></p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>