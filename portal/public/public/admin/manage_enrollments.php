<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = 'Invalid security token.';
    } else {
        $action = $_POST['action'] ?? '';
        $studentId = (int) ($_POST['student_id'] ?? 0);
        $courseId = (int) ($_POST['course_id'] ?? 0);

        if ($action === 'enrol' && $studentId > 0 && $courseId > 0) {
            $stmt = db()->prepare('INSERT INTO student_enrollments (student_id, course_id) VALUES (?, ?)');
            $stmt->bind_param('ii', $studentId, $courseId);
            try {
                $stmt->execute();
                $stmt->close();
                set_flash('success', 'Student enrolled in course.');
                header('Location: ' . base_path() . '/admin/manage_enrollments.php');
                exit;
            } catch (Throwable $e) {
                $stmt->close();
                $error = 'Enrolment already exists or could not be saved.';
            }
        } elseif ($action === 'drop') {
            $enrollId = (int) ($_POST['enrollment_id'] ?? 0);
            $stmt = db()->prepare('DELETE FROM student_enrollments WHERE id = ? LIMIT 1');
            $stmt->bind_param('i', $enrollId);
            $stmt->execute();
            $stmt->close();
            set_flash('success', 'Enrolment removed.');
            header('Location: ' . base_path() . '/admin/manage_enrollments.php');
            exit;
        } else {
            $error = 'Invalid action.';
        }
    }
}

$students = db()->query(
    "SELECT id, full_name, matric_number, academic_level
     FROM users WHERE role = 'student' AND is_active = 1
     ORDER BY full_name"
)->fetch_all(MYSQLI_ASSOC);

$courses = db()->query(
    "SELECT id, course_code, course_title, semester, session_year
     FROM courses ORDER BY course_code"
)->fetch_all(MYSQLI_ASSOC);

$rows = db()->query(
    "SELECT se.id, u.full_name, u.matric_number, u.academic_level,
            c.course_code, c.course_title, c.semester, c.session_year, se.enrolled_at
     FROM student_enrollments se
     JOIN users u ON u.id = se.student_id
     JOIN courses c ON c.id = se.course_id
     ORDER BY u.full_name, c.course_code"
)->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Manage Enrolments';
$activeNav = 'enrollments';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <h1 class="page-title">Student course enrolments</h1>
    <p class="page-lead">Students may assess only lecturers linked to courses they are enrolled in. Assign enrolments here or let students self-enrol.</p>

    <?php if ($error): ?>
        <div class="flash flash-error"><?= e($error) ?></div>
    <?php endif; ?>

    <div class="panel" style="margin-bottom:1.5rem;max-width:720px;">
        <h2 style="margin-top:0;font-family:var(--font-display);color:var(--green-deep);font-size:1.35rem;">Add enrolment</h2>
        <form method="post" class="form-grid two">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="enrol">
            <div class="form-group">
                <label for="student_id">Student</label>
                <select id="student_id" name="student_id" required>
                    <option value="">Select…</option>
                    <?php foreach ($students as $s): ?>
                        <option value="<?= (int) $s['id'] ?>">
                            <?= e($s['full_name'] . ' · ' . ($s['matric_number'] ?: 'no matric') . ' · L' . ($s['academic_level'] ?: '?')) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="course_id">Course offering</label>
                <select id="course_id" name="course_id" required>
                    <option value="">Select…</option>
                    <?php foreach ($courses as $c): ?>
                        <option value="<?= (int) $c['id'] ?>">
                            <?= e($c['course_code'] . ' — ' . $c['course_title'] . ' (' . $c['semester'] . ' ' . $c['session_year'] . ')') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-green">Enrol student</button>
            </div>
        </form>
    </div>

    <div class="panel">
        <h2 style="margin-top:0;font-family:var(--font-display);color:var(--green-deep);font-size:1.35rem;">Current enrolments</h2>
        <?php if (!$rows): ?>
            <div class="empty-state">No enrolments yet.</div>
        <?php else: ?>
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Level</th>
                            <th>Course</th>
                            <th>Offering</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $r): ?>
                            <tr>
                                <td><?= e($r['full_name']) ?><br><small><?= e($r['matric_number'] ?: '') ?></small></td>
                                <td><?= e($r['academic_level'] ?: '—') ?></td>
                                <td><strong><?= e($r['course_code']) ?></strong><br><small><?= e($r['course_title']) ?></small></td>
                                <td><?= e($r['semester'] . ' · ' . $r['session_year']) ?></td>
                                <td>
                                    <form method="post" onsubmit="return confirm('Remove enrolment?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="drop">
                                        <input type="hidden" name="enrollment_id" value="<?= (int) $r['id'] ?>">
                                        <button type="submit" class="btn btn-ghost btn-sm">Remove</button>
                                    </form>
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
