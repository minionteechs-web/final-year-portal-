<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/charts.php';
require_role('lecturer');

$user = current_user();
$lecturerId = get_lecturer_id_for_user((int) $user['id']);

if (!$lecturerId) {
    set_flash('error', 'Your lecturer profile is missing. Contact an administrator.');
    header('Location: ' . base_path() . '/logout.php');
    exit;
}

$profile = db()->prepare('SELECT title, bio, office_location, research_area FROM lecturers WHERE id = ?');
$profile->bind_param('i', $lecturerId);
$profile->execute();
$lec = $profile->get_result()->fetch_assoc();
$profile->close();

$summary = lecturer_rating_summary($lecturerId);
$overall = overall_average($summary['averages']);
$dataset = chart_dataset_from_summary($summary);

$courses = db()->prepare(
    "SELECT c.course_code, c.course_title, c.semester, c.session_year, COUNT(a.id) AS ratings
     FROM course_lecturers cl
     JOIN courses c ON c.id = cl.course_id
     LEFT JOIN assessments a ON a.course_id = c.id AND a.lecturer_id = cl.lecturer_id
     WHERE cl.lecturer_id = ?
     GROUP BY c.id, c.course_code, c.course_title, c.semester, c.session_year
     ORDER BY c.course_code"
);
$courses->bind_param('i', $lecturerId);
$courses->execute();
$courseRows = $courses->get_result()->fetch_all(MYSQLI_ASSOC);
$courses->close();

$comments = db()->prepare(
    "SELECT a.comment, a.created_at, c.course_code
     FROM assessments a
     JOIN courses c ON c.id = a.course_id
     WHERE a.lecturer_id = ? AND a.comment IS NOT NULL AND TRIM(a.comment) <> ''
     ORDER BY a.created_at DESC
     LIMIT 10"
);
$comments->bind_param('i', $lecturerId);
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

$pageTitle = 'Lecturer Dashboard';
$activeNav = 'dashboard';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <h1 class="page-title"><?= e(($lec['title'] ?? 'Dr.') . ' ' . $user['full_name']) ?></h1>
    <p class="page-lead">Aggregated anonymous student feedback on your teaching qualities.</p>

    <div class="stats-grid">
        <div class="stat-tile"><span>Assessments</span><strong><?= (int) $summary['count'] ?></strong></div>
        <div class="stat-tile"><span>Overall average</span><strong><?= e(number_format($overall, 2)) ?></strong></div>
        <div class="stat-tile"><span>Courses linked</span><strong><?= count($courseRows) ?></strong></div>
        <div class="stat-tile"><span>Department</span><strong style="font-size:1.15rem;"><?= e($user['department'] ?: '—') ?></strong></div>
    </div>

    <div class="chart-grid">
        <div class="chart-card">
            <h3>Your quality radar</h3>
            <p class="meta">Mean scores across all courses</p>
            <div class="chart-wrap tall">
                <canvas data-chart="<?= $radar ?>"></canvas>
            </div>
        </div>
        <div class="chart-card">
            <h3>Quality breakdown</h3>
            <p class="meta">Bar chart of the same averages</p>
            <div class="chart-wrap tall">
                <canvas data-chart="<?= $bar ?>"></canvas>
            </div>
        </div>
    </div>

    <div class="panel" style="margin-top:1.25rem;">
        <h2 style="margin-top:0;font-family:var(--font-display);color:var(--green-deep);">Assigned courses</h2>
        <?php if (!$courseRows): ?>
            <div class="empty-state">No courses assigned yet.</div>
        <?php else: ?>
            <div class="table-wrap">
                <table class="data">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Title</th>
                            <th>Semester</th>
                            <th>Session</th>
                            <th>Ratings</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courseRows as $c): ?>
                            <tr>
                                <td><?= e($c['course_code']) ?></td>
                                <td><?= e($c['course_title']) ?></td>
                                <td><?= e($c['semester']) ?></td>
                                <td><?= e($c['session_year']) ?></td>
                                <td><?= (int) $c['ratings'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($commentRows): ?>
        <div class="panel" style="margin-top:1.25rem;">
            <h2 style="margin-top:0;font-family:var(--font-display);color:var(--green-deep);">Recent anonymous comments</h2>
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
