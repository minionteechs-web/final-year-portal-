<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('student');

$user = current_user();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $level = trim($_POST['academic_level'] ?? '');
        if (!is_valid_student_level($level)) {
            $error = 'Select a valid academic level (100–400).';
        } else {
            $stmt = db()->prepare('UPDATE users SET academic_level = ? WHERE id = ? AND role = \'student\' LIMIT 1');
            $uid = (int) $user['id'];
            $stmt->bind_param('si', $level, $uid);
            if ($stmt->execute()) {
                $stmt->close();
                set_flash('success', 'Academic level updated to ' . $level . ' Level.');
                header('Location: ' . base_path() . '/student/profile.php');
                exit;
            }
            $stmt->close();
            $error = 'Could not update level. Please try again.';
        }
    }
}

$pageTitle = 'My Profile';
$activeNav = 'profile';
require_once __DIR__ . '/../includes/header.php';
$user = current_user();
?>

<div class="container">
    <h1 class="page-title">Student profile</h1>
    <p class="page-lead">Your level is stored on your account. Update it when you progress (the system does not advance levels automatically).</p>

    <?php if ($error): ?>
        <div class="flash flash-error"><?= e($error) ?></div>
    <?php endif; ?>

    <div class="panel" style="max-width:560px;">
        <dl class="meta-list" style="margin:0 0 1.25rem;">
            <div><dt>Name</dt><dd><?= e($user['full_name']) ?></dd></div>
            <div><dt>Email</dt><dd><?= e($user['email']) ?></dd></div>
            <div><dt>Matric number</dt><dd><?= e($user['matric_number'] ?: '—') ?></dd></div>
            <div><dt>Department</dt><dd><?= e($user['department'] ?: '—') ?></dd></div>
            <div><dt>Current level</dt><dd><?= e($user['academic_level'] ? $user['academic_level'] . ' Level' : 'Not set') ?></dd></div>
        </dl>

        <form method="post" class="form-grid">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="academic_level">Update academic level</label>
                <select id="academic_level" name="academic_level" required>
                    <?php foreach (academic_levels() as $lvl): ?>
                        <option value="<?= e($lvl) ?>" <?= ($user['academic_level'] ?? '') === $lvl ? 'selected' : '' ?>><?= e($lvl) ?> Level</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-green">Save level</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
