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

        if ($action === 'approve_level') {
            $id = (int) ($_POST['user_id'] ?? 0);
            $approve = filter_var($_POST['approve'] ?? '0', FILTER_VALIDATE_BOOLEAN);
            $userRow = db()->prepare('SELECT id, full_name, role, pending_level, academic_level FROM users WHERE id = ? AND role = \'student\' LIMIT 1');
            $userRow->bind_param('i', $id);
            $userRow->execute();
            $target = $userRow->get_result()->fetch_assoc();
            $userRow->close();

            if (!$target) {
                $error = 'Student not found.';
            } elseif ($approve && !empty($target['pending_level'])) {
                $stmt = db()->prepare('UPDATE users SET academic_level = ?, pending_level = NULL WHERE id = ?');
                $stmt->bind_param('si', $target['pending_level'], $id);
                $stmt->execute();
                $stmt->close();
                set_flash('success', 'Approved level change for ' . e($target['full_name']) . ' to ' . e($target['pending_level']) . ' Level.');
            } elseif (!$approve) {
                $stmt = db()->prepare('UPDATE users SET pending_level = NULL WHERE id = ?');
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $stmt->close();
                set_flash('success', 'Rejected pending level change for ' . e($target['full_name']) . '.');
            }
            header('Location: ' . base_path() . '/admin/manage_users.php');
            exit;
        }

        if ($action === 'toggle_access') {
            $enabled = filter_var($_POST['enabled'] ?? '1', FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
            $adminId = (int) current_user()['id'];
            $conn = db();
            $conn->begin_transaction();
            try {
                $stmt = $conn->prepare(
                    'INSERT INTO portal_settings (setting_key, setting_value, updated_by)
                     VALUES (\'student_access_enabled\', ?, ?)
                     ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_by = VALUES(updated_by), updated_at = CURRENT_TIMESTAMP'
                );
                $stmt->bind_param('si', $enabled, $adminId);
                $stmt->execute();
                $stmt->close();
                $conn->commit();
                set_flash('success', 'Student access ' . ($enabled === '1' ? 'enabled' : 'disabled') . '.');
            } catch (Throwable $e) {
                $conn->rollback();
                $error = 'Could not update student access setting.';
            }
            header('Location: ' . base_path() . '/admin/manage_users.php');
            exit;
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
    'SELECT id, full_name, email, role, department, matric_number, academic_level, pending_level, is_active, created_at
     FROM users ORDER BY role, full_name'
)->fetch_all(MYSQLI_ASSOC);

$settingsRow = db()->query(
    "SELECT setting_value FROM portal_settings WHERE setting_key = 'student_access_enabled' LIMIT 1"
)->fetch_assoc();
$studentAccessEnabled = $settingsRow === null || (string) $settingsRow['setting_value'] === '1';

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
        <h2 style="margin-top:0;font-family:var(--font-display);color:var(--green-deep);">Student Portal Access</h2>
        <p class="page-lead" style="margin-top:0;">Enable or disable student access across the entire student portal.</p>
        <form method="post" class="form-grid" style="gap:0.5rem;align-items:center;">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="toggle_access">
            <label style="margin:0;display:flex;align-items:center;gap:0.5rem;">
                <input type="checkbox" name="enabled" value="1" <?= $studentAccessEnabled ? 'checked' : '' ?>>
                <span>Student access enabled</span>
            </label>
            <button type="submit" class="btn btn-green">Save access setting</button>
        </form>
        <p class="hint" style="margin-top:0.5rem;">When disabled, students cannot access any student pages.</p>
    </div>

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
                        <th>Level</th>
                        <th>Pending Level</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?= e($u['full_name']) ?><?php if ($u['matric_number']): ?><br><small><?= e($u['matric_number']) ?><?php if (!empty($u['academic_level'])): ?> · L<?= e($u['academic_level']) ?><?php endif; ?></small><?php endif; ?></td>
                            <td><?= e($u['email']) ?></td>
                            <td><?= e($u['department'] ?: 'â€”') ?></td>
                            <td><?= e($u['academic_level'] ?: 'â€”') ?></td>
                            <td><?= !empty($u['pending_level']) ? '<span class="badge badge-warn">' . e($u['pending_level']) . ' Lvl</span>' : 'â€”' ?></td>
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
                                <?php if (!empty($u['pending_level']) && $u['role'] === 'student'): ?>
                                <form method="post" class="inline-form" style="margin-top:0.25rem;"
                                    onsubmit="return confirm('Approve level change?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="approve_level">
                                    <input type="hidden" name="user_id" value="<?= (int) $u['id'] ?>">
                                    <input type="hidden" name="approve" value="1">
                                    <button type="submit" class="btn btn-sm btn-ghost" style="color:var(--green-deep);border-color:var(--line);">Approve Level</button>
                                </form>
                                <form method="post" class="inline-form" style="margin-top:0.25rem;"
                                    onsubmit="return confirm('Reject pending level change?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="approve_level">
                                    <input type="hidden" name="user_id" value="<?= (int) $u['id'] ?>">
                                    <input type="hidden" name="approve" value="0">
                                    <button type="submit" class="btn btn-sm btn-danger">Reject Level</button>
                                </form>
                                <?php endif; ?>
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

