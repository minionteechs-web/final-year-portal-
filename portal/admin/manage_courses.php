<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$error = '';

$lecturerOptions = db()->query(
    "SELECT l.id, l.title, u.full_name
     FROM lecturers l
     JOIN users u ON u.id = l.user_id
     ORDER BY u.full_name"
)->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = 'Invalid security token.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'create') {
            $code = strtoupper(trim($_POST['course_code'] ?? ''));
            $title = trim($_POST['course_title'] ?? '');
            $department = trim($_POST['department'] ?? '');
            $level = trim($_POST['level'] ?? '100');
            $courseType = $_POST['course_type'] ?? 'Compulsory';
            $semester = $_POST['semester'] ?? 'First Semester';
            $session = trim($_POST['session_year'] ?? '2024/2025');
            $description = trim($_POST['description'] ?? '');
            $lecturerId = (int) ($_POST['lecturer_id'] ?? 0);

            if (!in_array($semester, ['First Semester', 'Second Semester'], true)) {
                $semester = 'First Semester';
            }
            if (!in_array($courseType, ['Compulsory', 'Elective'], true)) {
                $courseType = 'Compulsory';
            }

            if ($code === '' || $title === '' || $department === '') {
                $error = 'Course code, title, and department are required.';
            } else {
                $conn = db();
                $conn->begin_transaction();
                try {
                    $stmt = $conn->prepare(
                        'INSERT INTO courses (course_code, course_title, department, level, course_type, semester, session_year, description)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
                    );
                    $descVal = $description !== '' ? $description : null;
                    $stmt->bind_param('ssssssss', $code, $title, $department, $level, $courseType, $semester, $session, $descVal);
                    $stmt->execute();
                    $courseId = (int) $stmt->insert_id;
                    $stmt->close();

                    if ($lecturerId > 0) {
                        $link = $conn->prepare('INSERT INTO course_lecturers (course_id, lecturer_id, is_primary) VALUES (?, ?, 1)');
                        $link->bind_param('ii', $courseId, $lecturerId);
                        $link->execute();
                        $link->close();
                    }

                    $conn->commit();
                    set_flash('success', 'Course created.');
                    header('Location: ' . base_path() . '/admin/manage_courses.php');
                    exit;
                } catch (Throwable $e) {
                    $conn->rollback();
                    $error = 'Could not create course (code may already exist).';
                }
            }
        }

        if ($action === 'assign') {
            $courseId = (int) ($_POST['course_id'] ?? 0);
            $lecturerId = (int) ($_POST['lecturer_id'] ?? 0);
            if ($courseId && $lecturerId) {
                try {
                    $stmt = db()->prepare('INSERT INTO course_lecturers (course_id, lecturer_id, is_primary) VALUES (?, ?, 1)');
                    $stmt->bind_param('ii', $courseId, $lecturerId);
                    $stmt->execute();
                    $stmt->close();
                    set_flash('success', 'Lecturer assigned to course.');
                    header('Location: ' . base_path() . '/admin/manage_courses.php');
                    exit;
                } catch (Throwable $e) {
                    $error = 'Assignment already exists or is invalid.';
                }
            } else {
                $error = 'Select both course and lecturer.';
            }
        }

        if ($action === 'delete') {
            $id = (int) ($_POST['course_id'] ?? 0);
            $stmt = db()->prepare('DELETE FROM courses WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
            set_flash('success', 'Course deleted.');
            header('Location: ' . base_path() . '/admin/manage_courses.php');
            exit;
        }
    }
}

$courses = db()->query(
    "SELECT c.*,
            GROUP_CONCAT(CONCAT(l.title, ' ', u.full_name) ORDER BY u.full_name SEPARATOR ', ') AS lecturers
     FROM courses c
     LEFT JOIN course_lecturers cl ON cl.course_id = c.id
     LEFT JOIN lecturers l ON l.id = cl.lecturer_id
     LEFT JOIN users u ON u.id = l.user_id
     GROUP BY c.id
     ORDER BY c.course_code"
)->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Manage Courses';
$activeNav = 'courses';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <h1 class="page-title">Manage courses</h1>
    <p class="page-lead">
        At the <strong>beginning of each semester</strong>, publish compulsory and elective courses for each level and assign the lecturer(s) taking them.
        Students then select the courses they are offering; at semester end they rate only those lecturers.
    </p>

    <?php if ($error): ?>
        <div class="flash flash-error"><?= e($error) ?></div>
    <?php endif; ?>

    <div class="panel">
        <h2 style="margin-top:0;font-family:var(--font-display);color:var(--green-deep);">Add course</h2>
        <form method="post" class="form-grid">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="create">
            <div class="form-grid two">
                <div class="form-group">
                    <label for="course_code">Course code</label>
                    <input type="text" id="course_code" name="course_code" required placeholder="COS 301">
                </div>
                <div class="form-group">
                    <label for="course_title">Course title</label>
                    <input type="text" id="course_title" name="course_title" required>
                </div>
            </div>
            <div class="form-grid two">
                <div class="form-group">
                    <label for="department">Department</label>
                    <input type="text" id="department" name="department" value="Computer Science" required>
                </div>
                <div class="form-group">
                    <label for="level">Level offered</label>
                    <select id="level" name="level">
                        <?php foreach (academic_levels() as $lvl): ?>
                            <option value="<?= e($lvl) ?>" <?= $lvl === '300' ? 'selected' : '' ?>><?= e($lvl) ?> Level</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-grid two">
                <div class="form-group">
                    <label for="course_type">Course type</label>
                    <select id="course_type" name="course_type">
                        <option value="Compulsory">Compulsory</option>
                        <option value="Elective">Elective</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="semester">Semester</label>
                    <select id="semester" name="semester">
                        <option value="First Semester">First Semester</option>
                        <option value="Second Semester">Second Semester</option>
                    </select>
                </div>
            </div>
            <div class="form-grid two">
                <div class="form-group">
                    <label for="session_year">Session</label>
                    <input type="text" id="session_year" name="session_year" value="2024/2025" required>
                </div>
                <div class="form-group">
                    <label for="lecturer_id">Primary lecturer</label>
                    <select id="lecturer_id" name="lecturer_id">
                        <option value="0">— Assign later —</option>
                        <?php foreach ($lecturerOptions as $l): ?>
                            <option value="<?= (int) $l['id'] ?>"><?= e($l['title'] . ' ' . $l['full_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="2"></textarea>
            </div>
            <button type="submit" class="btn btn-green">Publish course offering</button>
        </form>
    </div>

    <div class="panel">
        <h2 style="margin-top:0;font-family:var(--font-display);color:var(--green-deep);">Assign lecturer to existing course</h2>
        <form method="post" class="form-grid two">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="assign">
            <div class="form-group">
                <label for="assign_course">Course</label>
                <select id="assign_course" name="course_id" required>
                    <option value="">Select course…</option>
                    <?php foreach ($courses as $c): ?>
                        <option value="<?= (int) $c['id'] ?>"><?= e($c['course_code'] . ' — ' . $c['course_title'] . ' (L' . $c['level'] . ', ' . ($c['course_type'] ?? 'Compulsory') . ')') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="assign_lecturer">Lecturer</label>
                <select id="assign_lecturer" name="lecturer_id" required>
                    <option value="">Select lecturer…</option>
                    <?php foreach ($lecturerOptions as $l): ?>
                        <option value="<?= (int) $l['id'] ?>"><?= e($l['title'] . ' ' . $l['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-gold">Assign</button>
            </div>
        </form>
    </div>

    <div class="panel">
        <h2 style="margin-top:0;font-family:var(--font-display);color:var(--green-deep);">All courses</h2>
        <div class="table-wrap">
            <table class="data">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Title</th>
                        <th>Type / Level</th>
                        <th>Semester</th>
                        <th>Lecturers</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $c): ?>
                        <tr>
                            <td><?= e($c['course_code']) ?></td>
                            <td><?= e($c['course_title']) ?><br><small><?= e($c['department']) ?></small></td>
                            <td><?= e(($c['course_type'] ?? 'Compulsory') . ' · L' . $c['level']) ?></td>
                            <td><?= e($c['semester'] . ' ' . $c['session_year']) ?></td>
                            <td><?= e($c['lecturers'] ?: '—') ?></td>
                            <td>
                                <form method="post" class="inline-form" onsubmit="return confirm('Delete this course?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="course_id" value="<?= (int) $c['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
