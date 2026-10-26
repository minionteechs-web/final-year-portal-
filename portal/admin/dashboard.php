<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$counts = [
    'users' => (int) db()->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'],
    'students' => (int) db()->query("SELECT COUNT(*) AS c FROM users WHERE role='student'")->fetch_assoc()['c'],
    'lecturers' => (int) db()->query("SELECT COUNT(*) AS c FROM lecturers")->fetch_assoc()['c'],
    'courses' => (int) db()->query("SELECT COUNT(*) AS c FROM courses")->fetch_assoc()['c'],
    'assessments' => (int) db()->query("SELECT COUNT(*) AS c FROM assessments")->fetch_assoc()['c'],
];

$recent = db()->query(
    "SELECT a.created_at, c.course_code, u.full_name AS lecturer_name, l.title,
            ROUND((a.interactiveness+a.audibility+a.lecture_notes+a.class_control+a.communication+a.punctuality+a.fairness+a.approachability)/8, 2) AS avg_score
     FROM assessments a
     JOIN courses c ON c.id = a.course_id
     JOIN lecturers l ON l.id = a.lecturer_id
     JOIN users u ON u.id = l.user_id
     ORDER BY a.created_at DESC
     LIMIT 8"
)->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Admin Dashboard';
$activeNav = 'dashboard';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <h1 class="page-title">Admin dashboard</h1>
    <p class="page-lead">Manage users, courses, and lecturer profiles for the Lecturer Overview Portal.</p>

    <div class="stats-grid">
        <div class="stat-tile"><span>Users</span><strong><?= $counts['users'] ?></strong></div>
        <div class="stat-tile"><span>Students</span><strong><?= $counts['students'] ?></strong></div>
        <div class="stat-tile"><span>Lecturers</span><strong><?= $counts['lecturers'] ?></strong></div>
        <div class="stat-tile"><span>Assessments</span><strong><?= $counts['assessments'] ?></strong></div>
    </div>

    <div class="actions" style="margin-bottom:1.25rem;">
        <a class="btn btn-green" href="<?= e(base_path()) ?>/admin/manage_users.php">Manage users</a>
        <a class="btn btn-green" href="<?= e(base_path()) ?>/admin/manage_courses.php">Manage courses</a>
        <a class="btn btn-gold" href="<?= e(base_path()) ?>/admin/manage_lecturers.php">Manage lecturers</a>
    </div>

    <div class="panel">
        <h2 style="margin-top:0;font-family:var(--font-display);color:var(--green-deep);">Recent assessments</h2>
        <?php if (!$recent): ?>
            <div class="empty-state">No assessments yet.</div>
        <?php else: ?>
            <div class="table-wrap">
                <table class="data">
                    <thead>
                        <tr>
                            <th>When</th>
                            <th>Lecturer</th>
                            <th>Course</th>
                            <th>Avg</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent as $row): ?>
                            <tr>
                                <td><?= e(date('j M Y H:i', strtotime($row['created_at']))) ?></td>
                                <td><?= e($row['title'] . ' ' . $row['lecturer_name']) ?></td>
                                <td><?= e($row['course_code']) ?></td>
                                <td><?= e((string) $row['avg_score']) ?>/5</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
