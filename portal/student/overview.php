<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/charts.php';
require_role('student');
require_student_access();

$lecturers = db()->query(
    "SELECT l.id, l.title, l.research_area, u.full_name, u.department,
            COUNT(DISTINCT cl.course_id) AS course_count,
            COUNT(a.id) AS rating_count,
            ROUND(AVG((a.interactiveness+a.audibility+a.lecture_notes+a.class_control+a.communication+a.punctuality+a.fairness+a.approachability)/8), 2) AS overall
     FROM lecturers l
     JOIN users u ON u.id = l.user_id
     LEFT JOIN course_lecturers cl ON cl.lecturer_id = l.id
     LEFT JOIN assessments a ON a.lecturer_id = l.id
     WHERE u.is_active = 1
     GROUP BY l.id, l.title, l.research_area, u.full_name, u.department
     ORDER BY u.full_name"
)->fetch_all(MYSQLI_ASSOC);

$chartPayloads = [];
foreach ($lecturers as $lec) {
    $summary = lecturer_rating_summary((int) $lec['id']);
    $chartPayloads[(int) $lec['id']] = chart_dataset_from_summary($summary);
}

$ranked = array_values(array_filter($lecturers, static function ($l) {
    return (int) ($l['rating_count'] ?? 0) > 0;
}));
usort($ranked, static function ($a, $b) {
    $cmp = ($b['overall'] ?? 0) <=> ($a['overall'] ?? 0);
    if ($cmp !== 0) {
        return $cmp;
    }
    return strcmp((string) $a['full_name'], (string) $b['full_name']);
});
$topThree = array_slice($ranked, 0, 3);

function overview_band($score): string
{
    $s = (float) $score;
    if ($s >= 3.5) {
        return 'High';
    }
    if ($s >= 2.5) {
        return 'Moderate';
    }
    return 'Low';
}


$pageTitle = 'Lecturer Overview';
$activeNav = 'overview';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <h1 class="page-title">Lecturer overview</h1>
    <p class="page-lead">Compare teaching qualities across lecturers with radar and bar charts.</p>

    <?php if ($topThree): ?>
        <div class="panel" style="margin-bottom:1.25rem;">
            <h2 style="margin-top:0;font-family:var(--font-display);color:var(--green-deep);">Top 3 lecturers by overall rating</h2>
            <p class="meta">Total Overview = simple average of the eight quality means (1.00-2.49 Low; 2.50-3.49 Moderate; 3.50-5.00 High).</p>
            <div class="table-wrap">
                <table class="data">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Lecturer</th>
                            <th>Overall rating (/5.00)</th>
                            <th>Interpretation band</th>
                            <th>Ratings</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_values($topThree) as $i => $lec): ?>
                            <tr>
                                <td><?= ($i + 1) ?><?= $i === 0 ? 'st' : ($i === 1 ? 'nd' : 'rd') ?></td>
                                <td><?= e($lec['title'] . ' ' . $lec['full_name']) ?></td>
                                <td><?= e((string) ($lec['overall'] ?? '0')) ?></td>
                                <td><?= e(overview_band($lec['overall'] ?? 0)) ?></td>
                                <td><?= (int) $lec['rating_count'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!$lecturers): ?>
        <div class="empty-state">No lecturers found.</div>
    <?php else: ?>
        <div class="chart-grid">
            <?php foreach ($lecturers as $lec): ?>
                <?php
                $id = (int) $lec['id'];
                $data = $chartPayloads[$id];
                $radar = htmlspecialchars(json_encode([
                    'type' => 'radar',
                    'labels' => $data['labels'],
                    'values' => $data['values'],
                ]), ENT_QUOTES, 'UTF-8');
                $bar = htmlspecialchars(json_encode([
                    'type' => 'bar',
                    'labels' => $data['labels'],
                    'values' => $data['values'],
                ]), ENT_QUOTES, 'UTF-8');
                ?>
                <article class="chart-card">
                    <h3><?= e($lec['title'] . ' ' . $lec['full_name']) ?></h3>
                    <p class="meta">
                        <?= e($lec['department']) ?>
                        <?php if ($lec['research_area']): ?> · <?= e($lec['research_area']) ?><?php endif; ?>
                        · <?= (int) $lec['rating_count'] ?> rating(s)
                        · Overall <strong><?= e((string) ($lec['overall'] ?? '0')) ?>/5</strong>
                    </p>
                    <div class="chart-wrap">
                        <canvas data-chart="<?= $radar ?>"></canvas>
                    </div>
                    <div class="chart-wrap" style="margin-top:1rem;">
                        <canvas data-chart="<?= $bar ?>"></canvas>
                    </div>
                    <div style="margin-top:1rem;">
                        <a class="btn btn-green btn-sm" href="<?= e(base_path()) ?>/student/lecturer-profile.php?id=<?= $id ?>">Open profile</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
