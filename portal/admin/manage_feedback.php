<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$feedback = db()->query(
    "SELECT sf.id, sf.subject, sf.message, sf.created_at, u.full_name, u.email
     FROM system_feedback sf
     LEFT JOIN users u ON u.id = sf.user_id
     ORDER BY sf.created_at DESC"
)->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'System Feedback';
$activeNav = 'feedback';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="container">
    <div class="page-head">
        <h1>System Feedback</h1>
        <p class="lead">Portal feedback submitted by users.</p>
    </div>

    <div class="panel">
        <?php if (!$feedback): ?>
            <p style="color:var(--muted);">No feedback submitted yet.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table class="data">
                    <thead>
                        <tr><th>User</th><th>Subject</th><th>Message</th><th>Submitted</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($feedback as $f): ?>
                            <tr>
                                <td><?= e($f['full_name'] ?? 'Anonymous') ?><br><small><?= e($f['email'] ?? '') ?></small></td>
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
