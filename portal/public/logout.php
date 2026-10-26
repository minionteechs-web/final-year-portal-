<?php
require_once __DIR__ . '/includes/auth.php';

$redirect = base_path() . '/login.php';
logout_user();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
set_flash('success', 'You have been logged out.');
header('Location: ' . $redirect);
exit;
