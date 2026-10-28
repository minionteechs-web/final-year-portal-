<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/charts.php';
require_role('student');
require_student_access();

$id = (int) ($_GET['id'] ?? 0);
$stmt = db()->prepare(
    "SELECT l.id, l.title, l.bio, l.office_location, l.research_area,
            u.full_name, u.department, u.email
     FROM lecturers l
     JOIN users u ON u.id = l.user_id
     WHERE l.id = ? AND u.is_active = 1
     LIMIT 1"
);
$stmt->bind_param('i', $id);
$stmt->execute();
$lecturer = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$lecturer) {
    set_flash('error', 'Lecturer not found.');
    header('Location: ' . base_path() . '/student/overview.php');
    exit;
}

$summary = lecturer_rating_summary($id);
$overall = overall_average($summary['averages']);
$dataset = chart_dataset_from_summary($summary);

$courses = db()->prepare(
    "SELECT c.id, c.course_code, c.course_title, c.semester, c.session_year
     FROM courses c
     JOIN course_lecturers cl ON cl.course_id = c.id
     WHERE cl.lecturer_id = ?
     ORDER BY c.course_code"
);
$courses->bind_param('i', $id);
$courses->execute();
$courseRows = $courses->get_result()->fetch_all(MYSQLI_ASSOC);
$courses->close();

$comments = db()->prepare(
    "SELECT a.comment, a.created_at, c.course_code
     FROM assessments a
     JOIN courses c ON c.id = a.course_id
     WHERE a.lecturer_id = ? AND a.comment IS NOT NULL AND TRIM(a.comment) <> ''
     ORDER BY a.created_at DESC
     LIMIT 8"
);
$comments->bind_param('i', $id);
$comments->execute();
$commentRows = $comments->get_result()->fetch_all(MYSQLI_ASSOC);
$comments->close();

$radar = htmlspecialchars(json_encode([
    'type' => 'radar',
    'labels' => $dataset['labels'],
    'values' => $dataset['values'],
]), ENT_QUOTES, 'UTF-8');
$bar = htmlspecialchars(json_encode([
    'type' => 'bar',
    'labels' => $dataset['labels'],
    'values' => $dataset['values'],
]), ENT_QUOTES, 'UTF-8');

$pageTitle = $lecturer['title'] . ' ' . $lecturer['full_name'];
$activeNav = 'overview';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <p style="margin:0 0 0.75rem;"><a href="<?= e(base_path()) ?>/student/overview.php">&larr; Back to overview</a></p>
    <h1 class="page-title"><?= e($lecturer['title'] . ' ' . $lecturer['full_name']) ?></h1>
    <p class="page-lead"><?= e($lecturer['department']) ?><?php if ($lecturer['research_area']): ?> · <?= e($lecturer['research_area']) ?><?php endif; ?></p>

    <div class="profile-hero">
        <div class="panel">
            <h2 style="margin-top:0;font-family:var(--font-display);color:var(--green-deep);">Profile</h2>
            <p><?= e($lecturer['bio'] ?: 'No biography provided yet.') ?></p>
            <?php if ($lecturer['office_location']): ?>
                <p><strong>Office:</strong> <?= e($lecturer['office_location']) ?></p>
            <?php endif; ?>
            <p><span class="badge"><?= (int) $summary['count'] ?> assessment(s)</span></p>
            <?php if ($courseRows): ?>
                <h3 style="font-family:var(--font-display);color:var(--green-deep);">Courses</h3>
                <ul>
                    <?php foreach ($courseRows as $c): ?>
                        <li><?= e($c['course_code'] . ' — ' . $c['course_title'] . ' (' . $c['semester'] . ' ' . $c['session_year'] . ')') ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <a class="btn btn-gold btn-sm" href="<?= e(base_path()) ?>/student/assess.php">Assess this lecturer</a>
        </div>
        <div class="panel score-ring">
            <div class="score"><?= e(number_format($overall, 2)) ?></div>
            <div class="label">Overall average / 5</div>
        </div>
    </div>

    <div class="chart-grid">
        <div class="chart-card">
            <h3>Quality radar</h3>
            <p class="meta">Aggregated anonymous student ratings</p>
            <div class="chart-wrap tall">
                <canvas data-chart="<?= $radar ?>"></canvas>
            </div>
        </div>
        <div class="chart-card">
            <h3>Quality bars</h3>
            <p class="meta">Same data, bar comparison</p>
            <div class="chart-wrap tall">
                <canvas data-chart="<?= $bar ?>"></canvas>
            </div>
        </div>
    </div>

    <?php if ($commentRows): ?>
        <div class="panel" style="margin-top:1.25rem;">
            <h2 style="margin-top:0;font-family:var(--font-display);color:var(--green-deep);">Anonymous comments</h2>
            <div class="lecturer-list">
                <?php foreach ($commentRows as $c): ?>
                    <div class="lecturer-row">
                        <div>
                            <p style="margin:0;color:var(--ink);"><?= e($c['comment']) ?></p>
                            <p><?= e($c['course_code']) ?> · <?= e(date('j M Y', strtotime($c['created_at']))) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
