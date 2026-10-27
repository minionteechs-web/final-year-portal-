<?php
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    header('Location: ' . dashboard_url_for_role(current_user()['role']));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $rlKey = rate_limit_key('login', $email);
        if (is_rate_limited($rlKey)) {
            $error = 'Too many failed login attempts. Please try again in 15 minutes.';
        } elseif ($email === '' || $password === '') {
            $error = 'Email and password are required.';
        } else {
            $stmt = db()->prepare('SELECT id, full_name, email, password, role, is_active FROM users WHERE email = ? LIMIT 1');
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$user || !(int) $user['is_active'] || !password_verify($password, $user['password'])) {
                record_rate_limit_failure($rlKey);
                $error = 'Invalid email or password.';
            } else {
                clear_rate_limit($rlKey);
                login_user($user);
                set_flash('success', 'Welcome back, ' . $user['full_name'] . '!');
                header('Location: ' . dashboard_url_for_role($user['role']));
                exit;
            }
        }
    }
}

$pageTitle = 'Login';
$activeNav = 'login';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container auth-layout">
    <div class="auth-card">
        <h1>Sign in</h1>
        <p class="lead">Access your student, lecturer, or admin workspace.</p>

        <?php if ($error): ?>
            <div class="flash flash-error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post" class="form-grid" style="margin-top:1rem;">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>" placeholder="you@unn.edu.ng">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-green">Login</button>
        </form>

        <p style="margin-top:1.25rem;color:var(--muted);">No account? <a href="<?= e(base_path()) ?>/register.php">Register</a> &bull; <a href="<?= e(base_path()) ?>/forgot_password.php">Forgot password?</a></p>

        
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
