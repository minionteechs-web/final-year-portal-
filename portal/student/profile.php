<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('student');
require_student_access();

$user = current_user();
$uid = (int) $user['id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = 'Invalid security token. Please try again.';
    } elseif (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['profile_picture'];
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = @$finfo->file($file['tmp_name']);
        if (!isset($allowed[$mime])) {
            $error = 'Only JPG, PNG, GIF, or WEBP images are allowed.';
        } elseif ($file['size'] > 2 * 1024 * 1024) {
            $error = 'Profile picture must be under 2 MB.';
        } else {
            $ext = $allowed[$mime];
            $filename = 'user_' . $uid . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
            $dest = __DIR__ . '/../uploads/' . $filename;
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                if (!empty($user['profile_picture'])) {
                    $old = __DIR__ . '/../' . $user['profile_picture'];
                    if (file_exists($old)) { @unlink($old); }
                }
                $stmt = db()->prepare("UPDATE users SET profile_picture = ? WHERE id = ? AND role = 'student' LIMIT 1");
                $stmt->bind_param('si', 'uploads/' . $filename, $uid);
                $stmt->execute();
                $stmt->close();
                set_flash('success', 'Profile picture updated.');
                header('Location: ' . base_path() . '/student/profile.php');
                exit;
            } else {
                $error = 'Could not upload profile picture. Please try again.';
            }
        }
    } else {
        $level = trim($_POST['academic_level'] ?? '');
        if (!is_valid_student_level($level)) {
            $error = 'Select a valid academic level (100–400).';
        } else {
            // Submit for admin approval — level stays pending until admin approves
            $stmt = db()->prepare('UPDATE users SET pending_level = ? WHERE id = ? AND role = \'student\' LIMIT 1');
            $uid = (int) $user['id'];
            $stmt->bind_param('si', $level, $uid);
            if ($stmt->execute()) {
                $stmt->close();
                set_flash('success', 'Level change request for ' . $level . ' Level submitted. It will take effect once an admin approves it.');
                header('Location: ' . base_path() . '/student/profile.php');
                exit;
            }
            $stmt->close();
            $error = 'Could not submit level change. Please try again.';
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
        <div class="profile-hero" style="text-align:center;margin-bottom:1.5rem;">
            <?php if (!empty($user['profile_picture'])): ?>
                <img src="<?= e(base_path()) . '/' . e($user['profile_picture']) ?>" alt="Profile picture" class="profile-avatar">
            <?php else: ?>
                <div class="default-avatar"><?= strtoupper(substr(e($user['full_name']), 0, 1)) ?></div>
            <?php endif; ?>
            <p class="hint" style="margin-top:0.5rem;"><?= e($user['full_name']) ?></p>
        </div>

        <form method="post" enctype="multipart/form-data" class="form-grid" style="max-width:520px;margin:0 auto 1.5rem;">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="profile_picture">Profile picture</label>
                <input type="file" id="profile_picture" name="profile_picture" accept="image/jpeg,image/png,image/gif,image/webp">
                <small class="hint">JPG, PNG, GIF or WEBP. Max 2 MB.</small>
            </div>
            <button type="submit" class="btn btn-green">Update profile picture</button>
        </form>

        <dl class="meta-list" style="margin:0 0 1.25rem;">
            <div><dt>Name</dt><dd><?= e($user['full_name']) ?></dd></div>
            <div><dt>Email</dt><dd><?= e($user['email']) ?></dd></div>
            <div><dt>Matric number</dt><dd><?= e($user['matric_number'] ?: '—') ?></dd></div>
            <div><dt>Department</dt><dd><?= e($user['department'] ?: '—') ?></dd></div>
            <div><dt>Current level</dt><dd><?= e($user['academic_level'] ? $user['academic_level'] . ' Level' : 'Not set') ?></dd></div>
            <?php if (!empty($user['pending_level'])): ?>
                <div><dt>Pending approval</dt><dd><em><?= e($user['pending_level']) ?> Level</em> — awaiting admin approval</dd></div>
            <?php endif; ?>
        </dl>

        <form method="post" class="form-grid">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="academic_level">Request level change</label>
                <select id="academic_level" name="academic_level" required>
                    <?php foreach (academic_levels() as $lvl): ?>
                        <option value="<?= e($lvl) ?>" <?= ($user['pending_level'] ?? $user['academic_level'] ?? '') === $lvl ? 'selected' : '' ?>><?= e($lvl) ?> Level</option>
                    <?php endforeach; ?>
                </select>
                <small class="hint">Submitted to admin for approval. Your level will update once approved.</small>
            </div>
            <button type="submit" class="btn btn-green">Request level change</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
