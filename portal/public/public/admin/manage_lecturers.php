<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/charts.php';
require_role('admin');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = 'Invalid security token.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'update') {
            $id = (int) ($_POST['lecturer_id'] ?? 0);
            $title = trim($_POST['title'] ?? 'Dr.');
            $bio = trim($_POST['bio'] ?? '');
            $office = trim($_POST['office_location'] ?? '');
            $research = trim($_POST['research_area'] ?? '');

            $stmt = db()->prepare(
                'UPDATE lecturers SET title = ?, bio = ?, office_location = ?, research_area = ? WHERE id = ?'
            );
            $bioVal = $bio !== '' ? $bio : null;
            $officeVal = $office !== '' ? $office : null;
            $researchVal = $research !== '' ? $research : null;
            $stmt->bind_param('ssssi', $title, $bioVal, $officeVal, $researchVal, $id);
            $stmt->execute();
            $stmt->close();
            set_flash('success', 'Lecturer profile updated.');
            header('Location: ' . base_path() . '/admin/manage_lecturers.php');
            exit;
        }

        if ($action === 'create_from_user') {
            $userId = (int) ($_POST['user_id'] ?? 0);
            $title = trim($_POST['title'] ?? 'Dr.');
            $check = db()->prepare("SELECT id, role FROM users WHERE id = ? LIMIT 1");
            $check->bind_param('i', $userId);
            $check->execute();
            $u = $check->get_result()->fetch_assoc();
            $check->close();

            if (!$u || $u['role'] !== 'lecturer') {
                $error = 'Select a user with the lecturer role.';
            } else {
                try {
                    $bio = '';
                    $stmt = db()->prepare('INSERT INTO lecturers (user_id, title, bio) VALUES (?, ?, ?)');
                    $stmt->bind_param('iss', $userId, $title, $bio);
                    $stmt->execute();
                    $stmt->close();
                    set_flash('success', 'Lecturer profile created.');
                    header('Location: ' . base_path() . '/admin/manage_lecturers.php');
                    exit;
                } catch (Throwable $e) {
                    $error = 'Profile already exists for that user.';
                }
            }
        }
    }
}

$lecturers = db()->query(
    "SELECT l.*, u.full_name, u.email, u.department, u.is_active,
            COUNT(a.id) AS rating_count,
            ROUND(AVG((a.interactiveness+a.audibility+a.lecture_notes+a.class_control+a.communication+a.punctuality+a.fairness+a.approachability)/8), 2) AS overall
     FROM lecturers l
     JOIN users u ON u.id = l.user_id
     LEFT JOIN assessments a ON a.lecturer_id = l.id
     GROUP BY l.id
     ORDER BY u.full_name"
)->fetch_all(MYSQLI_ASSOC);

$unlinked = db()->query(
    "SELECT u.id, u.full_name, u.email
     FROM users u
     LEFT JOIN lecturers l ON l.user_id = u.id
     WHERE u.role = 'lecturer' AND l.id IS NULL
     ORDER BY u.full_name"
)->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Manage Lecturers';
$activeNav = 'lecturers';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <h1 class="page-title">Manage lecturers</h1>
    <p class="page-lead">Edit lecturer profiles and review aggregated rating counts.</p>

    <?php if ($error): ?>
        <div class="flash flash-error"><?= e($error) ?></div>
    <?php endif; ?>

    <?php if ($unlinked): ?>
        <div class="panel">
            <h2 style="margin-top:0;font-family:var(--font-display);color:var(--green-deep);">Create missing lecturer profile</h2>
            <form method="post" class="form-grid two">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="create_from_user">
                <div class="form-group">
                    <label for="user_id">Lecturer user</label>
                    <select id="user_id" name="user_id" required>
                        <?php foreach ($unlinked as $u): ?>
                            <option value="<?= (int) $u['id'] ?>"><?= e($u['full_name'] . ' (' . $u['email'] . ')') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="new_title">Title</label>
                    <select id="new_title" name="title">
                        <?php foreach (['Dr.', 'Prof.', 'Mr.', 'Mrs.', 'Ms.'] as $t): ?>
                            <option value="<?= e($t) ?>"><?= e($t) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn btn-green">Create profile</button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <?php foreach ($lecturers as $l): ?>
        <?php
        $summary = lecturer_rating_summary((int) $l['id']);
        $dataset = chart_dataset_from_summary($summary);
        $radar = htmlspecialchars(json_encode([
            'type' => 'radar',
            'labels' => $dataset['labels'],
            'values' => $dataset['values'],
        ]), ENT_QUOTES, 'UTF-8');
        ?>
        <div class="panel">
            <div class="profile-hero">
                <div>
                    <h2 style="margin:0;font-family:var(--font-display);color:var(--green-deep);">
                        <?= e($l['title'] . ' ' . $l['full_name']) ?>
                    </h2>
                    <p class="page-lead" style="margin-bottom:1rem;">
                        <?= e($l['email']) ?> · <?= e($l['department'] ?: 'No department') ?>
                        · <?= (int) $l['rating_count'] ?> rating(s)
                        · Overall <?= e((string) ($l['overall'] ?? '0')) ?>/5
                        · <?= (int) $l['is_active'] ? 'Active' : 'Inactive' ?>
                    </p>
                    <form method="post" class="form-grid">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="lecturer_id" value="<?= (int) $l['id'] ?>">
                        <div class="form-grid two">
                            <div class="form-group">
                                <label>Title</label>
                                <select name="title">
                                    <?php foreach (['Dr.', 'Prof.', 'Mr.', 'Mrs.', 'Ms.'] as $t): ?>
                                        <option value="<?= e($t) ?>" <?= $l['title'] === $t ? 'selected' : '' ?>><?= e($t) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Research area</label>
                                <input type="text" name="research_area" value="<?= e($l['research_area'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Office location</label>
                            <input type="text" name="office_location" value="<?= e($l['office_location'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Bio</label>
                            <textarea name="bio" rows="3"><?= e($l['bio'] ?? '') ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-green btn-sm">Save profile</button>
                    </form>
                </div>
                <div class="chart-card" style="box-shadow:none;padding:0.5rem 0 0;">
                    <div class="chart-wrap">
                        <canvas data-chart="<?= $radar ?>"></canvas>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
