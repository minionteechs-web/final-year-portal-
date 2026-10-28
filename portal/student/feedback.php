<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('student');
require_student_access();

$user = current_user();
$uid = (int) $user['id'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if ($subject === '' || $message === '') {
            $error = 'Subject and message are required.';
        } elseif (strlen($subject) > 160) {
            $error = 'Subject must be 160 characters or fewer.';
        } else {
            $stmt = db()->prepare('INSERT INTO system_feedback (user_id, subject, message) VALUES (?, ?, ?)');
            $stmt->bind_param('iss', $uid, $subject, $message);
            $stmt->execute();
            $stmt->close();
            set_flash('success', 'Thank you — your feedback has been submitted.');
            header('Location: ' . base_path() . '/student/feedback.php');
            exit;
        }
    }
}

$mine = db()->prepare('SELECT subject, message, created_at FROM system_feedback WHERE user_id = ? ORDER BY created_at DESC');
$mine->bind_param('i', $uid);
$mine->execute();
$myFeedback = $mine->get_result()->fetch_all(MYSQLI_ASSOC);
$mine->close();

$pageTitle = 'System Feedback';
$activeNav = 'feedback';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="container">
    <div class="page-head">
        <h1>System Feedback</h1>
        <p class="lead">Share your experience of the portal — suggestions, issues, or praise.</p>
    </div>

    <?php if ($error): ?>
        <div class="flash flash-error"><?= e($error) ?></div>
    <?php endif; ?>

    <div class="panel">
        <h2 style="margin-top:0;font-family:var(--font-display);color:var(--green-deep);">Send feedback</h2>
        <form method="post" class="form-grid">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="subject">Subject</label>
                <input type="text" id="subject" name="subject" required maxlength="160" value="<?= e($_POST['subject'] ?? '') ?>" placeholder="e.g. Chart readability on mobile">
            </div>
            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" rows="4" required placeholder="Describe your feedback…"><?= e($_POST['message'] ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn btn-green">Submit feedback</button>
        </form>
    </div>

    <div class="panel" style="margin-top:1.5rem;">
        <h2 style="margin-top:0;font-family:var(--font-display);color:var(--green-deep);">Your previous feedback</h2>
        <?php if (!$myFeedback): ?>
            <p style="color:var(--muted);">You haven’t submitted any feedback yet.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table class="data">
                    <thead>
                        <tr><th>Subject</th><th>Message</th><th>Submitted</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($myFeedback as $f): ?>
                            <tr>
                                <td><?= e($f['subject']) ?></td>
                                <td><?= e($f['message']) ?></td>
                                <td><?= e($f['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
