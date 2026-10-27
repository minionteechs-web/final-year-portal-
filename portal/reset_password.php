<?php
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    header('Location: ' . dashboard_url_for_role(current_user()['role']));
    exit;
}

$token = $_GET['token'] ?? '';
$error = '';
$success = '';

if ($token === '') {
    set_flash('error', 'Invalid reset link.');
    header('Location: ' . base_path() . '/login.php');
    exit;
}

// Validate token
$stmt = db()->prepare('SELECT pr.id, pr.user_id, pr.expires_at, pr.used, u.id AS uid, u.email
    FROM password_resets pr
    JOIN users u ON u.id = pr.user_id
    WHERE pr.token = ? LIMIT 1');
$stmt->bind_param('s', $token);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

$tokenValid = false;
if ($row) {
    $tokenValid = (int) $row['used'] === 0 && strtotime($row['expires_at']) > time();
}

if (!$tokenValid) {
    set_flash('error', 'This reset link is invalid or has expired.');
    header('Location: ' . base_path() . '/forgot_password.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $password = trim($_POST['password'] ?? '');
        $confirm = trim($_POST['password_confirm'] ?? '');

        if ($password === '' || $confirm === '') {
            $error = 'Password and confirmation are required.';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters.';
        } elseif ($password !== $confirm) {
            $error = 'Passwords do not match.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $conn = db();
            $conn->begin_transaction();
            try {
                $u = $conn->prepare('UPDATE users SET password = ? WHERE id = ?');
                $u->bind_param('si', $hash, $row['user_id']);
                $u->execute();
                $u->close();

                $m = $conn->prepare('UPDATE password_resets SET used = 1 WHERE id = ?');
                $m->bind_param('i', $row['id']);
                $m->execute();
                $m->close();

                $conn->commit();
                set_flash('success', 'Your password has been reset. You can now log in.');
                header('Location: ' . base_path() . '/login.php');
                exit;
            } catch (Throwable $e) {
                $conn->rollback();
                $error = 'Something went wrong. Please try again.';
            }
        }
    }
}

$pageTitle = 'Reset Password';
$activeNav = 'reset';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container auth-layout">
    <div class="auth-card">
        <h1>Reset Password</h1>
        <p class="lead">Enter a new password for your account.</p>

        <?php if ($error): ?>
            <div class="flash flash-error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post" class="form-grid" style="margin-top:1rem;">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="password">New password</label>
                <input type="password" id="password" name="password" required minlength="6" autocomplete="new-password">
            </div>
            <div class="form-group">
                <label for="password_confirm">Confirm new password</label>
                <input type="password" id="password_confirm" name="password_confirm" required minlength="6" autocomplete="new-password">
            </div>
            <button type="submit" class="btn btn-green">Reset Password</button>
        </form>

        <p style="margin-top:1.25rem;color:var(--muted);">Remember your password? <a href="<?= e(base_path()) ?>/login.php">Log in</a></p>
    </div>
</div>

<script>
(() => {
  const password = document.getElementById('password');
  const confirm = document.getElementById('password_confirm');
  const form = document.querySelector('form');
  let matchError = null;

  const showMatchError = (show) => {
    if (show && !matchError) {
      matchError = document.createElement('div');
      matchError.style.color = 'var(--danger, #c0392b)';
      matchError.style.fontSize = '0.85rem';
      matchError.style.marginTop = '0.35rem';
      matchError.textContent = 'Passwords do not match.';
      confirm.parentNode.insertBefore(matchError, confirm.nextSibling);
      password.style.borderColor = 'var(--danger, #c0392b)';
      confirm.style.borderColor = 'var(--danger, #c0392b)';
    } else if (!show && matchError) {
      matchError.remove();
      matchError = null;
      password.style.borderColor = '';
      confirm.style.borderColor = '';
    }
  };

  const checkMatch = () => {
    showMatchError(password.value !== '' && confirm.value !== '' && password.value !== confirm.value);
  };

  password.addEventListener('input', checkMatch);
  confirm.addEventListener('input', checkMatch);

  form.addEventListener('submit', (e) => {
    checkMatch();
    if (matchError) e.preventDefault();
  });
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>