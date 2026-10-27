<?php
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    header('Location: ' . dashboard_url_for_role(current_user()['role']));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirm = trim($_POST['password_confirm'] ?? '');
        $role = $_POST['role'] ?? 'student';
        $department = trim($_POST['department'] ?? '');
        $matric = trim($_POST['matric_number'] ?? '');
        $academicLevel = trim($_POST['academic_level'] ?? '');
        $title = trim($_POST['title'] ?? 'Dr.');

        if (!in_array($role, ['student', 'lecturer'], true)) {
            $role = 'student';
        }

        $rlKey = rate_limit_key('register', $email);
        if (is_rate_limited($rlKey)) {
            $error = 'Too many registration attempts. Please try again in 15 minutes.';
        } elseif ($fullName === '' || $email === '' || $password === '') {
            record_rate_limit_failure($rlKey);
            $error = 'Name, email, and password are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            record_rate_limit_failure($rlKey);
            $error = 'Please enter a valid email address.';
        } elseif (strlen($password) < 6) {
            record_rate_limit_failure($rlKey);
            $error = 'Password must be at least 6 characters.';
        } elseif ($password !== $confirm) {
            record_rate_limit_failure($rlKey);
            $error = 'Passwords do not match.';
        } elseif ($role === 'student' && $matric === '') {
            record_rate_limit_failure($rlKey);
            $error = 'Matric number is required for students.';
        } elseif ($role === 'student' && !is_valid_student_level($academicLevel)) {
            record_rate_limit_failure($rlKey);
            $error = 'Select your current academic level (100–400).';
        } else {
            $check = db()->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
            $check->bind_param('s', $email);
            $check->execute();
            $exists = $check->get_result()->fetch_assoc();
            $check->close();

            if ($exists) {
                $error = 'Registration complete. If the email is new, an account has been created.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $conn = db();
                $conn->begin_transaction();
                try {
                    $stmt = $conn->prepare(
                        'INSERT INTO users (full_name, email, password, role, department, matric_number, academic_level)
                         VALUES (?, ?, ?, ?, ?, ?, ?)'
                    );
                    $matricVal = $role === 'student' ? $matric : null;
                    $levelVal = $role === 'student' ? $academicLevel : null;
                    $stmt->bind_param('sssssss', $fullName, $email, $hash, $role, $department, $matricVal, $levelVal);
                    $stmt->execute();
                    $userId = (int) $stmt->insert_id;
                    $stmt->close();

                    if ($role === 'lecturer') {
                        $bio = '';
                        $lstmt = $conn->prepare('INSERT INTO lecturers (user_id, title, bio) VALUES (?, ?, ?)');
                        $lstmt->bind_param('iss', $userId, $title, $bio);
                        $lstmt->execute();
                        $lstmt->close();
                    }

                    $conn->commit();
                    clear_rate_limit($rlKey);
                    set_flash('success', 'Registration successful. Please log in, then enrol in your courses before assessing lecturers.');
                    header('Location: ' . base_path() . '/login.php');
                    exit;
                } catch (Throwable $e) {
                    $conn->rollback();
                    $error = 'Registration failed. Please try again.';
                }
            }
        }
    }
}

$pageTitle = 'Register';
$activeNav = 'register';
require_once __DIR__ . '/includes/header.php';
$roleSelected = $_POST['role'] ?? 'student';
?>

<div class="container auth-layout">
    <div class="auth-card" style="width:min(560px,100%);">
        <h1>Create account</h1>
        <p class="lead">Register as a student or lecturer to use the portal.</p>

        <?php if ($error): ?>
            <div class="flash flash-error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post" class="form-grid" style="margin-top:1rem;" id="register-form">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="full_name">Full name</label>
                <input type="text" id="full_name" name="full_name" required value="<?= e($_POST['full_name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>" placeholder="you@unn.edu.ng">
            </div>
            <div class="form-grid two">
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required minlength="6">
                </div>
                <div class="form-group">
                    <label for="password_confirm">Confirm password</label>
                    <input type="password" id="password_confirm" name="password_confirm" required minlength="6">
                </div>
            </div>
            <div class="form-grid two">
                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role">
                        <option value="student" <?= $roleSelected === 'student' ? 'selected' : '' ?>>Student</option>
                        <option value="lecturer" <?= $roleSelected === 'lecturer' ? 'selected' : '' ?>>Lecturer</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="department">Department</label>
                    <input type="text" id="department" name="department" value="<?= e($_POST['department'] ?? 'Computer Science') ?>">
                </div>
            </div>
            <div class="form-group" data-student-field>
                <label for="matric_number">Matric number</label>
                <input type="text" id="matric_number" name="matric_number" value="<?= e($_POST['matric_number'] ?? '') ?>" placeholder="2019/180001">
            </div>
            <div class="form-group" data-student-field>
                <label for="academic_level">Current academic level</label>
                <select id="academic_level" name="academic_level">
                    <option value="">Select level…</option>
                    <?php foreach (academic_levels() as $lvl): ?>
                        <option value="<?= e($lvl) ?>" <?= ($_POST['academic_level'] ?? '') === $lvl ? 'selected' : '' ?>><?= e($lvl) ?> Level</option>
                    <?php endforeach; ?>
                </select>
                <small style="color:var(--muted);display:block;margin-top:0.35rem;">Update this on your Profile when you progress (e.g. 100 → 200).</small>
            </div>
            <div class="form-group" data-lecturer-field style="display:none;">
                <label for="title">Title</label>
                <select id="title" name="title">
                    <?php foreach (['Dr.', 'Prof.', 'Mr.', 'Mrs.', 'Ms.'] as $t): ?>
                        <option value="<?= e($t) ?>" <?= ($_POST['title'] ?? 'Dr.') === $t ? 'selected' : '' ?>><?= e($t) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-green">Register</button>
        </form>

        <p style="margin-top:1.25rem;color:var(--muted);">Already registered? <a href="<?= e(base_path()) ?>/login.php">Login</a></p>
    </div>
</div>

<script>
(() => {
  const role = document.getElementById('role');
  const studentFields = document.querySelectorAll('[data-student-field]');
  const lecturerField = document.querySelector('[data-lecturer-field]');
  const sync = () => {
    const isStudent = role.value === 'student';
    studentFields.forEach((el) => { el.style.display = isStudent ? '' : 'none'; });
    lecturerField.style.display = isStudent ? 'none' : '';
  };
  role.addEventListener('change', sync);
  sync();

  // --- Client-side password match validation ---
  const password = document.getElementById('password');
  const confirm = document.getElementById('password_confirm');
  const form = document.querySelector('form');
  let matchError = null;

  const showMatchError = (show) => {
    if (show && !matchError) {
      matchError = document.createElement('div');
      matchError.style.color = 'var(--danger, #c0392b)';
      matchError.style.fontSize = '0.85rem';
      matchError.style.marginTop = '0.35rem';
      matchError.textContent = 'Passwords do not match.';
      confirm.parentNode.insertBefore(matchError, confirm.nextSibling);
      password.style.borderColor = 'var(--danger, #c0392b)';
      confirm.style.borderColor = 'var(--danger, #c0392b)';
    } else if (!show && matchError) {
      matchError.remove();
      matchError = null;
      password.style.borderColor = '';
      confirm.style.borderColor = '';
    }
  };

  const checkMatch = () => {
    showMatchError(password.value !== '' && confirm.value !== '' && password.value !== confirm.value);
  };

  password.addEventListener('input', checkMatch);
  confirm.addEventListener('input', checkMatch);

  form.addEventListener('submit', (e) => {
    checkMatch();
    if (matchError) e.preventDefault();
  });
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
