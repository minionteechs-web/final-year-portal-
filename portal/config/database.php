<?php
/**
 * Database configuration — mysqli connection
 * University of Nigeria, Nsukka — Lecturer Overview Portal
 */

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'lecturer_overview');

/**
 * @return mysqli
 */
function db(): mysqli
{
    static $conn = null;

    if ($conn instanceof mysqli) {
        return $conn;
    }

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        http_response_code(500);
        die('Database connection failed. Import portal/sql/schema.sql in phpMyAdmin, then refresh.');
    }

    $conn->set_charset('utf8mb4');

    return $conn;
}
