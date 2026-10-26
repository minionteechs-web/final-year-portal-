<?php
$pageTitle = 'Home';
$activeNav = 'home';
$bodyClass = 'page-home';
require_once __DIR__ . '/includes/header.php';
$user = current_user();
?>

<section class="hero">
    <div class="hero-bg" aria-hidden="true"></div>
    <div class="container hero-content">
        <p class="hero-brand">UNN</p>
        <h1>Know your lecturers before the semester unfolds.</h1>
        <p>An online students system for preknowledge and overview of lecturers — rate eight teaching qualities and explore clear chart summaries.</p>
        <div class="hero-actions">
            <?php if ($user): ?>
                <a class="btn btn-gold" href="<?= e(dashboard_url_for_role($user['role'])) ?>">Go to Dashboard</a>
            <?php else: ?>
                <a class="btn btn-gold" href="<?= e(base_path()) ?>/register.php">Get Started</a>
                <a class="btn btn-outline" href="<?= e(base_path()) ?>/login.php">Sign In</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head">
            <h2>Built for Nsukka classrooms</h2>
            <p>Students assess teaching qualities anonymously. Lecturers see aggregated feedback. Administrators keep courses and accounts in order.</p>
        </div>
        <div class="feature-rail">
            <article class="feature">
                <h3>Eight qualities</h3>
                <p>Interactiveness, audibility, notes, class control, communication, punctuality, fairness, and approachability.</p>
            </article>
            <article class="feature">
                <h3>Visual overview</h3>
                <p>Radar and bar charts give an instant picture of lecturer strengths across courses and semesters.</p>
            </article>
            <article class="feature">
                <h3>Role-based access</h3>
                <p>Students rate and explore. Lecturers review their own feedback. Admins manage users, courses, and lecturer profiles.</p>
            </article>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
