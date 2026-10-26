<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = 'Invalid security token.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'toggle') {
            $id = (int) ($_POST['user_id'] ?? 0);
            $me = (int) current_user()['id'];
            if ($id === $me) {
                $error = 'You cannot deactivate your own admin account.';
            } else {
                $stmt = db()->prepare('UPDATE users SET is_active = IF(is_active=1,0,1) WHERE id = ?');
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $stmt->close();
                set_flash('success', 'User status updated.');
                header('Location: ' . base_path() . '/admin/manage_users.php');
                exit;
            }
        }

        if ($action === 'create') {
            $fullName = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'student';
            $department = trim($_POST['department'] ?? '');
            $matric = trim($_POST['matric_number'] ?? '');

            if (!in_array($role, ['student', 'lecturer', 'admin'], true)) {
                $role = 'student';
            }

            if ($fullName === '' || $email === '' || $password === '') {
                $error = 'Name, email, and password are required.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Invalid email.';
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
                    $levelVal = ($role === 'student' && is_valid_student_level(trim($_POST['academic_level'] ?? '')))
                        ? trim($_POST['academic_level'])
                        : null;
                    $stmt->bind_param('sssssss', $fullName, $email, $hash, $role, $department, $matricVal, $levelVal);
                    $stmt->execute();
                    $uid = (int) $stmt->insert_id;
                    $stmt->close();

                    if ($role === 'lecturer') {
                        $title = 'Dr.';
                        $bio = '';
                        $l = $conn->prepare('INSERT INTO lecturers (user_id, title, bio) VALUES (?, ?, ?)');
                        $l->bind_param('iss', $uid, $title, $bio);
                        $l->execute();
                        $l->close();
                    }

                    $conn->commit();
                    set_flash('success', 'User created successfully.');
                    header('Location: ' . base_path() . '/admin/manage_users.php');
                    exit;
                } catch (Throwable $e) {
                    $conn->rollback();
                    $error = 'Could not create user (email may already exist).';
                }
            }
        }

        if ($action === 'delete') {
            $id = (int) ($_POST['user_id'] ?? 0);
            $me = (int) current_user()['id'];
            if ($id === $me) {
                $error = 'You cannot delete your own account.';
            } else {
                $stmt = db()->prepare('DELETE FROM users WHERE id = ?');
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $stmt->close();
                set_flash('success', 'User deleted.');
                header('Location: ' . base_path() . '/admin/manage_users.php');
                exit;
            }
        }
    }
}

$users = db()->query(
    'SELECT id, full_name, email, role, department, matric_number, academic_level, is_active, created_at
     FROM users ORDER BY role, full_name'
)->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Manage Users';
$activeNav = 'users';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <h1 class="page-title">Manage users</h1>
    <p class="page-lead">Create accounts and activate or remove portal users.</p>

    <?php if ($error): ?>
        <div class="flash flash-error"><?= e($error) ?></div>
    <?php endif; ?>

    <div class="panel">
        <h2 style="margin-top:0;font-family:var(--font-display);color:var(--green-deep);">Add user</h2>
        <form method="post" class="form-grid">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="create">
            <div class="form-grid two">
                <div class="form-group">
                    <label for="full_name">Full name</label>
                    <input type="text" id="full_name" name="full_name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
            </div>
            <div class="form-grid two">
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required minlength="6">
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role">
                        <option value="student">Student</option>
                        <option value="lecturer">Lecturer</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>
            <div class="form-grid two">
                <div class="form-group">
                    <label for="department">Department</label>
                    <input type="text" id="department" name="department" value="Computer Science">
                </div>
                <div class="form-group">
                    <label for="matric_number">Matric number (students)</label>
                    <input type="text" id="matric_number" name="matric_number">
                </div>
            </div>
            <div class="form-group">
                <label for="academic_level">Academic level (students)</label>
                <select id="academic_level" name="academic_level">
                    <option value="">—</option>
                    <?php foreach (academic_levels() as $lvl): ?>
                        <option value="<?= e($lvl) ?>"><?= e($lvl) ?> Level</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-green">Create user</button>
        </form>
    </div>

    <div class="panel">
        <h2 style="margin-top:0;font-family:var(--font-display);color:var(--green-deep);">All users</h2>
        <div class="table-wrap">
            <table class="data">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?= e($u['full_name']) ?><?php if ($u['matric_number']): ?><br><small><?= e($u['matric_number']) ?><?php if (!empty($u['academic_level'])): ?> · L<?= e($u['academic_level']) ?><?php endif; ?></small><?php endif; ?></td>
                            <td><?= e($u['email']) ?></td>
                            <td><span class="badge"><?= e($u['role']) ?></span></td>
                            <td><?= e($u['department'] ?: '—') ?></td>
                            <td><?= (int) $u['is_active'] ? 'Active' : 'Inactive' ?></td>
                            <td class="actions">
                                <form method="post" class="inline-form">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="toggle">
                                    <input type="hidden" name="user_id" value="<?= (int) $u['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-ghost" style="color:var(--green-deep);border-color:var(--line);"><?= (int) $u['is_active'] ? 'Deactivate' : 'Activate' ?></button>
                                </form>
                                <?php if ((int) $u['id'] !== (int) current_user()['id']): ?>
                                    <form method="post" class="inline-form" onsubmit="return confirm('Delete this user permanently?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="user_id" value="<?= (int) $u['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
