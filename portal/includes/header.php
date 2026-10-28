<?php
/**
 * Shared page header
 * Expects optional: $pageTitle, $bodyClass, $activeNav
 */
require_once __DIR__ . '/auth.php';

$pageTitle  = $pageTitle ?? 'Lecturer Overview Portal';
$bodyClass  = $bodyClass ?? '';
$activeNav  = $activeNav ?? '';
$user       = current_user();
$flash      = get_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> · UNN Lecturer Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Source+Sans+3:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(base_path()) ?>/assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
    <script src="<?= e(base_path()) ?>/assets/js/app.js" defer></script>
</head>
<body class="<?= e($bodyClass) ?>">
<div class="page-shell">
    <header class="site-header">
        <div class="container header-inner">
            <a class="brand" href="<?= e(base_path()) ?>/index.php">
                <span class="brand-mark" aria-hidden="true">UNN</span>
                <span class="brand-text">
                    <strong>Lecturer Overview</strong>
                    <small>University of Nigeria, Nsukka</small>
                </span>
            </a>
            <button class="nav-toggle" type="button" aria-label="Toggle navigation" data-nav-toggle>
                <span></span><span></span><span></span>
            </button>
            <nav class="site-nav" data-nav>
                <a href="<?= e(base_path()) ?>/index.php" class="<?= $activeNav === 'home' ? 'is-active' : '' ?>">Home</a>
                <?php if ($user): ?>
                    <a href="<?= e(dashboard_url_for_role($user['role'])) ?>" class="<?= $activeNav === 'dashboard' ? 'is-active' : '' ?>">Dashboard</a>
                    <?php if ($user['role'] === 'student'): ?>
                        <a href="<?= e(base_path()) ?>/student/overview.php" class="<?= $activeNav === 'overview' ? 'is-active' : '' ?>">Overview</a>
                        <a href="<?= e(base_path()) ?>/student/enroll.php" class="<?= $activeNav === 'enroll' ? 'is-active' : '' ?>">My courses</a>
                        <a href="<?= e(base_path()) ?>/student/assess.php" class="<?= $activeNav === 'assess' ? 'is-active' : '' ?>">Assess</a>
                        <a href="<?= e(base_path()) ?>/student/profile.php" class="<?= $activeNav === 'profile' ? 'is-active' : '' ?>">Profile</a>
                        <a href="<?= e(base_path()) ?>/student/feedback.php" class="<?= $activeNav === 'feedback' ? 'is-active' : '' ?>">Feedback</a>
                    <?php elseif ($user['role'] === 'admin'): ?>
                        <a href="<?= e(base_path()) ?>/admin/manage_users.php" class="<?= $activeNav === 'users' ? 'is-active' : '' ?>">Users</a>
                        <a href="<?= e(base_path()) ?>/admin/manage_courses.php" class="<?= $activeNav === 'courses' ? 'is-active' : '' ?>">Courses</a>
                        <a href="<?= e(base_path()) ?>/admin/manage_enrollments.php" class="<?= $activeNav === 'enrollments' ? 'is-active' : '' ?>">Enrolments</a>
                        <a href="<?= e(base_path()) ?>/admin/manage_lecturers.php" class="<?= $activeNav === 'lecturers' ? 'is-active' : '' ?>">Lecturers</a>
                        <a href="<?= e(base_path()) ?>/admin/manage_feedback.php" class="<?= $activeNav === 'feedback' ? 'is-active' : '' ?>">Feedback</a>
                    <?php endif; ?>
                    <span class="nav-user"><?= e($user['full_name']) ?></span>
                    <a class="btn btn-ghost btn-sm" href="<?= e(base_path()) ?>/logout.php">Logout</a>
                <?php else: ?>
                    <a href="<?= e(base_path()) ?>/login.php" class="<?= $activeNav === 'login' ? 'is-active' : '' ?>">Login</a>
                    <a class="btn btn-gold btn-sm" href="<?= e(base_path()) ?>/register.php">Register</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <?php if ($flash): ?>
        <div class="container">
            <div class="flash flash-<?= e($flash['type']) ?>" role="alert">
                <?= e($flash['message']) ?>
            </div>
        </div>
    <?php endif; ?>

    <main class="site-main">
