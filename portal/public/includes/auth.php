<?php
/**
 * Authentication helpers, CSRF, flash messages, and role guards.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

/** Eight lecturer qualities used across assessments and charts */
const LECTURER_QUALITIES = [
    'interactiveness' => 'Interactiveness',
    'audibility'      => 'Audibility',
    'lecture_notes'   => 'Lecture Notes',
    'class_control'   => 'Class Control',
    'communication'   => 'Communication',
    'punctuality'     => 'Punctuality',
    'fairness'        => 'Fairness',
    'approachability' => 'Approachability',
];

const STUDENT_LEVELS = ['100', '200', '300', '400'];

function academic_levels(): array
{
    return STUDENT_LEVELS;
}

function is_valid_student_level(?string $level): bool
{
    return $level !== null && in_array($level, STUDENT_LEVELS, true);
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

function verify_csrf(): bool
{
    $token = $_POST['csrf_token'] ?? '';
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function is_logged_in(): bool
{
    return !empty($_SESSION['user_id']);
}

function current_user(): ?array
{
    if (!is_logged_in()) {
        return null;
    }

    static $user = null;
    if ($user !== null) {
        return $user;
    }

    $stmt = db()->prepare('SELECT id, full_name, email, role, department, matric_number, academic_level, is_active FROM users WHERE id = ? LIMIT 1');
    $uid = (int) $_SESSION['user_id'];
    $stmt->bind_param('i', $uid);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc() ?: null;
    $stmt->close();

    if (!$user || !(int) $user['is_active']) {
        logout_user();
        return null;
    }

    return $user;
}

function login_user(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_name'] = $user['full_name'];
}

function logout_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

function require_login(): void
{
    if (!is_logged_in()) {
        set_flash('error', 'Please log in to continue.');
        header('Location: ' . base_path() . '/login.php');
        exit;
    }
}

function require_role(string ...$roles): void
{
    require_login();
    $user = current_user();
    if (!$user || !in_array($user['role'], $roles, true)) {
        set_flash('error', 'You do not have permission to access that page.');
        header('Location: ' . dashboard_url_for_role($user['role'] ?? 'student'));
        exit;
    }
}

function dashboard_url_for_role(string $role): string
{
    $base = base_path();
    return match ($role) {
        'admin'    => $base . '/admin/dashboard.php',
        'lecturer' => $base . '/lecturer/dashboard.php',
        default    => $base . '/student/dashboard.php',
    };
}

function base_path(): string
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }

    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    if (preg_match('#^(.*?)/portal(?:/|$)#', $script, $m)) {
        $base = $m[1] . '/portal';
    } else {
        $dir = str_replace('\\', '/', dirname($script));
        if (preg_match('#/(student|lecturer|admin)$#', $dir)) {
            $dir = dirname($dir);
        }
        $base = ($dir === '/' || $dir === '\\' || $dir === '.') ? '' : $dir;
    }

    return $base;
}

/**
 * Average ratings for a lecturer (optionally filtered by course).
 * @return array{averages: array<string,float>, count: int}|null
 */
function lecturer_rating_summary(int $lecturerId, ?int $courseId = null): ?array
{
    $sql = 'SELECT
                COUNT(*) AS total,
                AVG(interactiveness) AS interactiveness,
                AVG(audibility) AS audibility,
                AVG(lecture_notes) AS lecture_notes,
                AVG(class_control) AS class_control,
                AVG(communication) AS communication,
                AVG(punctuality) AS punctuality,
                AVG(fairness) AS fairness,
                AVG(approachability) AS approachability
            FROM assessments
            WHERE lecturer_id = ?';

    if ($courseId) {
        $sql .= ' AND course_id = ?';
        $stmt = db()->prepare($sql);
        $stmt->bind_param('ii', $lecturerId, $courseId);
    } else {
        $stmt = db()->prepare($sql);
        $stmt->bind_param('i', $lecturerId);
    }

    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row || (int) $row['total'] === 0) {
        return ['averages' => array_fill_keys(array_keys(LECTURER_QUALITIES), 0.0), 'count' => 0];
    }

    $averages = [];
    foreach (array_keys(LECTURER_QUALITIES) as $key) {
        $averages[$key] = round((float) $row[$key], 2);
    }

    return ['averages' => $averages, 'count' => (int) $row['total']];
}

function overall_average(array $averages): float
{
    if (empty($averages)) {
        return 0.0;
    }
    return round(array_sum($averages) / count($averages), 2);
}

function get_lecturer_id_for_user(int $userId): ?int
{
    $stmt = db()->prepare('SELECT id FROM lecturers WHERE user_id = ? LIMIT 1');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row ? (int) $row['id'] : null;
}
