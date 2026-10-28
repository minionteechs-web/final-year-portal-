<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('student');
require_student_access();

$user = current_user();
$error = '';
$uid = (int) $user['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $action = $_POST['action'] ?? '';
        $courseId = (int) ($_POST['course_id'] ?? 0);

        if ($action === 'enrol' && $courseId > 0) {
            $check = db()->prepare('SELECT id FROM courses WHERE id = ? LIMIT 1');
            $check->bind_param('i', $courseId);
            $check->execute();
            $course = $check->get_result()->fetch_assoc();
            $check->close();

            if (!$course) {
                $error = 'Course not found.';
            } else {
                $stmt = db()->prepare('INSERT INTO student_enrollments (student_id, course_id) VALUES (?, ?)');
                $stmt->bind_param('ii', $uid, $courseId);
                try {
                    $stmt->execute();
                    $stmt->close();
                    set_flash('success', 'Course selected. At semester end you can assess its lecturer(s) on Assess.');
                    header('Location: ' . base_path() . '/student/enroll.php');
                    exit;
                } catch (Throwable $e) {
                    $stmt->close();
                    $error = 'You are already enrolled in that course, or enrolment failed.';
                }
            }
        } elseif ($action === 'drop' && $courseId > 0) {
            $stmt = db()->prepare('DELETE FROM student_enrollments WHERE student_id = ? AND course_id = ? LIMIT 1');
            $stmt->bind_param('ii', $uid, $courseId);
            $stmt->execute();
            $stmt->close();
            set_flash('success', 'Enrolment removed.');
            header('Location: ' . base_path() . '/student/enroll.php');
            exit;
        } else {
            $error = 'Invalid enrolment action.';
        }
    }
}

$enrolled = [];
$stmt = db()->prepare(
    "SELECT se.course_id, c.course_code, c.course_title, c.level, c.course_type, c.semester, c.session_year,
            GROUP_CONCAT(CONCAT(l.title, ' ', u.full_name) ORDER BY u.full_name SEPARATOR ', ') AS lecturers
     FROM student_enrollments se
     JOIN courses c ON c.id = se.course_id
     LEFT JOIN course_lecturers cl ON cl.course_id = c.id
     LEFT JOIN lecturers l ON l.id = cl.lecturer_id
     LEFT JOIN users u ON u.id = l.user_id
     WHERE se.student_id = ?
     GROUP BY se.course_id, c.course_code, c.course_title, c.level, c.course_type, c.semester, c.session_year
     ORDER BY c.course_code"
);
$stmt->bind_param('i', $uid);
$stmt->execute();
$enrolled = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$enrolledIds = array_map(static fn($r) => (int) $r['course_id'], $enrolled);

$showAll = isset($_GET['all']) && $_GET['all'] === '1';
$studentLevel = $user['academic_level'] ?? '';

if ($showAll || !is_valid_student_level($studentLevel)) {
    $available = db()->query(
        "SELECT c.id, c.course_code, c.course_title, c.level, c.course_type, c.semester, c.session_year,
                GROUP_CONCAT(CONCAT(l.title, ' ', u.full_name) ORDER BY u.full_name SEPARATOR ', ') AS lecturers
         FROM courses c
         LEFT JOIN course_lecturers cl ON cl.course_id = c.id
         LEFT JOIN lecturers l ON l.id = cl.lecturer_id
         LEFT JOIN users u ON u.id = l.user_id
         GROUP BY c.id, c.course_code, c.course_title, c.level, c.course_type, c.semester, c.session_year
         ORDER BY c.level, c.course_type, c.course_code"
    )->fetch_all(MYSQLI_ASSOC);
} else {
    $availStmt = db()->prepare(
        "SELECT c.id, c.course_code, c.course_title, c.level, c.course_type, c.semester, c.session_year,
                GROUP_CONCAT(CONCAT(l.title, ' ', u.full_name) ORDER BY u.full_name SEPARATOR ', ') AS lecturers
         FROM courses c
         LEFT JOIN course_lecturers cl ON cl.course_id = c.id
         LEFT JOIN lecturers l ON l.id = cl.lecturer_id
         LEFT JOIN users u ON u.id = l.user_id
         WHERE c.level = ?
         GROUP BY c.id, c.course_code, c.course_title, c.level, c.course_type, c.semester, c.session_year
         ORDER BY c.course_type, c.course_code"
    );
    $availStmt->bind_param('s', $studentLevel);
    $availStmt->execute();
    $available = $availStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $availStmt->close();
}

$pageTitle = 'My Courses';
$activeNav = 'enroll';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <h1 class="page-title">Select courses for this semester</h1>
    <p class="page-lead">
        After admin publishes compulsory and elective offerings for your level, choose the courses you are taking.
        At the <strong>end of the semester</strong>, Assess will list only the lecturers of those courses.
        Your level: <strong><?= e($user['academic_level'] ? $user['academic_level'] . ' Level' : 'not set') ?></strong>
        (<a href="<?= e(base_path()) ?>/student/profile.php">update</a>).
        <?php if (!$showAll && is_valid_student_level($studentLevel)): ?>
            Showing <?= e($studentLevel) ?>-level offerings.
            <a href="?all=1">Show all levels</a>
        <?php elseif ($showAll): ?>
            Showing all levels. <a href="?">Show my level only</a>
        <?php endif; ?>
    </p>

    <?php if ($error): ?>
        <div class="flash flash-error"><?= e($error) ?></div>
    <?php endif; ?>

    <div class="panel" style="margin-bottom:1.5rem;">
        <h2 style="margin-top:0;font-family:var(--font-display);color:var(--green-deep);font-size:1.35rem;">Enrolled courses</h2>
        <?php if (!$enrolled): ?>
            <div class="empty-state">You are not enrolled in any course yet. Enrol below before assessing lecturers.</div>
        <?php else: ?>
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Type</th>
                            <th>Level</th>
                            <th>Semester / Session</th>
                            <th>Lecturer(s)</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($enrolled as $row): ?>
                            <tr>
                                <td><strong><?= e($row['course_code']) ?></strong><br><small><?= e($row['course_title']) ?></small></td>
                                <td><?= e($row['course_type'] ?? 'Compulsory') ?></td>
                                <td><?= e($row['level']) ?></td>
                                <td><?= e($row['semester'] . ' · ' . $row['session_year']) ?></td>
                                <td><?= e($row['lecturers'] ?: '—') ?></td>
                                <td>
                                    <form method="post" onsubmit="return confirm('Remove this enrolment?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="drop">
                                        <input type="hidden" name="course_id" value="<?= (int) $row['course_id'] ?>">
                                        <button type="submit" class="btn btn-ghost btn-sm">Drop</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p style="margin-top:1rem;"><a class="btn btn-gold btn-sm" href="<?= e(base_path()) ?>/student/assess.php">Go to Assess</a></p>
        <?php endif; ?>
    </div>

    <div class="panel">
        <h2 style="margin-top:0;font-family:var(--font-display);color:var(--green-deep);font-size:1.35rem;">Available offerings (compulsory &amp; elective)</h2>
        <?php if (!$available): ?>
            <div class="empty-state">No courses published for your level yet. Ask admin to publish semester offerings, or <a href="?all=1">view all levels</a>.</div>
        <?php else: ?>
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Type</th>
                            <th>Level</th>
                            <th>Semester / Session</th>
                            <th>Lecturer(s)</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($available as $row): ?>
                            <?php $already = in_array((int) $row['id'], $enrolledIds, true); ?>
                            <tr>
                                <td><strong><?= e($row['course_code']) ?></strong><br><small><?= e($row['course_title']) ?></small></td>
                                <td><?= e($row['course_type'] ?? 'Compulsory') ?></td>
                                <td><?= e($row['level']) ?></td>
                                <td><?= e($row['semester'] . ' · ' . $row['session_year']) ?></td>
                                <td><?= e($row['lecturers'] ?: '—') ?></td>
                                <td>
                                    <?php if ($already): ?>
                                        <span style="color:var(--muted);font-size:0.9rem;">Selected</span>
                                    <?php else: ?>
                                        <form method="post">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="action" value="enrol">
                                            <input type="hidden" name="course_id" value="<?= (int) $row['id'] ?>">
                                            <button type="submit" class="btn btn-green btn-sm">Select course</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
