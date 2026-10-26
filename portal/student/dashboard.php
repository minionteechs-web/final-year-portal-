<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/charts.php';
require_role('student');

$user = current_user();

$stats = [
    'lecturers' => (int) db()->query('SELECT COUNT(*) AS c FROM lecturers')->fetch_assoc()['c'],
    'courses'   => (int) db()->query('SELECT COUNT(*) AS c FROM courses')->fetch_assoc()['c'],
    'mine'      => 0,
];

$stmt = db()->prepare('SELECT COUNT(*) AS c FROM assessments WHERE student_id = ?');
$uid = (int) $user['id'];
$stmt->bind_param('i', $uid);
$stmt->execute();
$stats['mine'] = (int) $stmt->get_result()->fetch_assoc()['c'];
$stmt->close();

$recent = db()->query(
    "SELECT l.id, l.title, u.full_name, u.department,
            ROUND(AVG((a.interactiveness+a.audibility+a.lecture_notes+a.class_control+a.communication+a.punctuality+a.fairness+a.approachability)/8), 2) AS overall,
            COUNT(a.id) AS ratings
     FROM lecturers l
     JOIN users u ON u.id = l.user_id
     LEFT JOIN assessments a ON a.lecturer_id = l.id
     GROUP BY l.id, l.title, u.full_name, u.department
     ORDER BY overall DESC, u.full_name ASC
     LIMIT 5"
)->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Student Dashboard';
$activeNav = 'dashboard';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <h1 class="page-title">Welcome, <?= e($user['full_name']) ?></h1>
    <p class="page-lead">
        Level on file: <strong><?= e($user['academic_level'] ? $user['academic_level'] . ' Level' : 'not set — update Profile') ?></strong>.
        Enrol in your courses, then submit anonymous assessments. Overview charts update from aggregated means.
    </p>

    <div class="stats-grid">
        <div class="stat-tile"><span>Lecturers</span><strong><?= $stats['lecturers'] ?></strong></div>
        <div class="stat-tile"><span>Courses</span><strong><?= $stats['courses'] ?></strong></div>
        <div class="stat-tile"><span>Your assessments</span><strong><?= $stats['mine'] ?></strong></div>
        <div class="stat-tile"><span>Qualities rated</span><strong>8</strong></div>
    </div>

    <div class="panel">
        <div style="display:flex;justify-content:space-between;gap:1rem;flex-wrap:wrap;align-items:center;margin-bottom:1rem;">
            <h2 style="margin:0;font-family:var(--font-display);color:var(--green-deep);font-size:1.5rem;">Top lecturer snapshots</h2>
            <div class="actions">
                <a class="btn btn-ghost btn-sm" href="<?= e(base_path()) ?>/student/enroll.php">My courses</a>
                <a class="btn btn-green btn-sm" href="<?= e(base_path()) ?>/student/overview.php">Full overview</a>
                <a class="btn btn-gold btn-sm" href="<?= e(base_path()) ?>/student/assess.php">Assess a lecturer</a>
            </div>
        </div>

        <?php if (!$recent): ?>
            <div class="empty-state">No lecturer data yet.</div>
        <?php else: ?>
            <div class="lecturer-list">
                <?php foreach ($recent as $row): ?>
                    <div class="lecturer-row">
                        <div>
                            <h3><?= e($row['title'] . ' ' . $row['full_name']) ?></h3>
                            <p><?= e($row['department']) ?> · <?= (int) $row['ratings'] ?> rating(s) · Overall <?= e((string) ($row['overall'] ?? '—')) ?>/5</p>
                        </div>
                        <a class="btn btn-green btn-sm" href="<?= e(base_path()) ?>/student/lecturer-profile.php?id=<?= (int) $row['id'] ?>">View charts</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
