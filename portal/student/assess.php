<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('student');
require_student_access();

$user = current_user();
$error = '';
$uid = (int) $user['id'];

$pairsStmt = db()->prepare(
    "SELECT cl.course_id, cl.lecturer_id, c.course_code, c.course_title, c.semester, c.session_year,
            l.title, u.full_name
     FROM student_enrollments se
     JOIN courses c ON c.id = se.course_id
     JOIN course_lecturers cl ON cl.course_id = c.id
     JOIN lecturers l ON l.id = cl.lecturer_id
     JOIN users u ON u.id = l.user_id
     WHERE se.student_id = ?
     ORDER BY c.course_code, u.full_name"
);
$pairsStmt->bind_param('i', $uid);
$pairsStmt->execute();
$pairs = $pairsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$pairsStmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $lecturerId = (int) ($_POST['lecturer_id'] ?? 0);
        $courseId = (int) ($_POST['course_id'] ?? 0);
        $semester = $_POST['semester'] ?? 'First Semester';
        $sessionYear = trim($_POST['session_year'] ?? '2024/2025');
        $comment = trim($_POST['comment'] ?? '');

        if (!in_array($semester, ['First Semester', 'Second Semester'], true)) {
            $semester = 'First Semester';
        }

        $ratings = [];
        foreach (array_keys(LECTURER_QUALITIES) as $key) {
            $val = (int) ($_POST[$key] ?? 0);
            if ($val < 1 || $val > 5) {
                $error = 'Each quality must be rated from 1 to 5.';
                break;
            }
            $ratings[$key] = $val;
        }

        if ($error === '') {
            $validPair = null;
            foreach ($pairs as $p) {
                if ((int) $p['lecturer_id'] === $lecturerId && (int) $p['course_id'] === $courseId) {
                    $validPair = $p;
                    break;
                }
            }

            if (!$validPair) {
                $error = 'You can only assess lecturers for courses you are enrolled in.';
            } else {
                // Force semester/session from the enrolled course offering
                $semester = $validPair['semester'];
                $sessionYear = $validPair['session_year'];

                // One assessment per student per lecturer (regardless of course/semester)
                $dup = db()->prepare(
                    'SELECT id FROM assessments
                     WHERE student_id = ? AND lecturer_id = ?
                     LIMIT 1'
                );
                $sid = $uid;
                $dup->bind_param('ii', $sid, $lecturerId);
                $dup->execute();
                $exists = $dup->get_result()->fetch_assoc();
                $dup->close();

                if ($exists) {
                    $error = 'You have already assessed this lecturer. You may only submit one assessment per lecturer.';
                } else {
                    $sql = 'INSERT INTO assessments
                        (student_id, lecturer_id, course_id, semester, session_year,
                         interactiveness, audibility, lecture_notes, class_control,
                         communication, punctuality, fairness, approachability, comment)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
                    $stmt = db()->prepare($sql);
                    $commentVal = $comment !== '' ? $comment : null;
                    $stmt->bind_param(
                        'iiissiiiiiiiis',
                        $sid,
                        $lecturerId,
                        $courseId,
                        $semester,
                        $sessionYear,
                        $ratings['interactiveness'],
                        $ratings['audibility'],
                        $ratings['lecture_notes'],
                        $ratings['class_control'],
                        $ratings['communication'],
                        $ratings['punctuality'],
                        $ratings['fairness'],
                        $ratings['approachability'],
                        $commentVal
                    );
                    try {
                        $stmt->execute();
                        $stmt->close();
                        set_flash('success', 'Assessment submitted anonymously. Overview charts will update from the new aggregate.');
                        header('Location: ' . base_path() . '/student/overview.php');
                        exit;
                    } catch (Throwable $e) {
                        $error = 'Could not save assessment. You may have already rated this lecturer for the course.';
                        $stmt->close();
                    }
                }
            }
        }
    }
}

$pageTitle = 'Assess Lecturer';
$activeNav = 'assess';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <h1 class="page-title">Assess a lecturer</h1>
    <p class="page-lead">
        Use this at the <strong>end of the semester</strong>. You only see lecturers for courses you selected under My courses.
        Rate the eight teaching qualities (1 = poor, 5 = excellent). Your identity is not shown on public charts.
        <?php if (!$pairs): ?>
            <br><a href="<?= e(base_path()) ?>/student/enroll.php">Select your semester courses</a> first.
        <?php endif; ?>
    </p>

    <?php if ($error): ?>
        <div class="flash flash-error"><?= e($error) ?></div>
    <?php endif; ?>

    <div class="panel" style="max-width:720px;">
        <?php if (!$pairs): ?>
            <div class="empty-state">
                No assessable lecturer–course pairs yet. Enrol in a course that has an assigned lecturer, then return here.
            </div>
        <?php else: ?>
            <form method="post" class="form-grid">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="pair">Lecturer &amp; course (from your enrolments)</label>
                    <select id="pair" name="pair" required>
                        <option value="">Select…</option>
                        <?php foreach ($pairs as $p): ?>
                            <?php $val = $p['lecturer_id'] . ':' . $p['course_id']; ?>
                            <option
                                value="<?= e($val) ?>"
                                data-semester="<?= e($p['semester']) ?>"
                                data-session="<?= e($p['session_year']) ?>"
                                <?= (($_POST['lecturer_id'] ?? '') . ':' . ($_POST['course_id'] ?? '')) === $val ? 'selected' : '' ?>
                            >
                                <?= e($p['title'] . ' ' . $p['full_name'] . ' — ' . $p['course_code'] . ' ' . $p['course_title']
                                    . ' (' . $p['semester'] . ' ' . $p['session_year'] . ')') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="lecturer_id" id="lecturer_id" value="<?= e($_POST['lecturer_id'] ?? '') ?>">
                    <input type="hidden" name="course_id" id="course_id" value="<?= e($_POST['course_id'] ?? '') ?>">
                </div>

                <div class="form-grid two">
                    <div class="form-group">
                        <label for="semester">Semester</label>
                        <input type="text" id="semester_display" readonly value="">
                        <input type="hidden" id="semester" name="semester" value="<?= e($_POST['semester'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="session_year">Session</label>
                        <input type="text" id="session_year" name="session_year" readonly value="<?= e($_POST['session_year'] ?? '') ?>">
                    </div>
                </div>

                <?php foreach (LECTURER_QUALITIES as $key => $label): ?>
                    <div class="rating-row">
                        <label for="<?= e($key) ?>"><?= e($label) ?></label>
                        <select id="<?= e($key) ?>" name="<?= e($key) ?>" required>
                            <option value="">Rate</option>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <option value="<?= $i ?>" <?= (string) ($_POST[$key] ?? '') === (string) $i ? 'selected' : '' ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                <?php endforeach; ?>

                <div class="form-group" style="margin-top:0.5rem;">
                    <label for="comment">Optional comment</label>
                    <textarea id="comment" name="comment" rows="3" placeholder="What stood out in this lecturer's teaching?"><?= e($_POST['comment'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn btn-green">Submit assessment</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
(() => {
  const pair = document.getElementById('pair');
  const lecturerId = document.getElementById('lecturer_id');
  const courseId = document.getElementById('course_id');
  const semester = document.getElementById('semester');
  const semesterDisplay = document.getElementById('semester_display');
  const sessionYear = document.getElementById('session_year');
  if (!pair) return;
  const sync = () => {
    const opt = pair.selectedOptions[0];
    if (!opt || !opt.value) {
      lecturerId.value = '';
      courseId.value = '';
      semester.value = '';
      if (semesterDisplay) semesterDisplay.value = '';
      sessionYear.value = '';
      return;
    }
    const [lid, cid] = opt.value.split(':');
    lecturerId.value = lid;
    courseId.value = cid;
    if (opt.dataset.semester) {
      semester.value = opt.dataset.semester;
      if (semesterDisplay) semesterDisplay.value = opt.dataset.semester;
    }
    if (opt.dataset.session) sessionYear.value = opt.dataset.session;
  };
  pair.addEventListener('change', sync);
  sync();
})();
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
