<?php
/**
 * Database configuration — mysqli connection
 * University of Nigeria, Nsukka — Lecturer Overview Portal
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'lecturer_overview');

/**
 * @return mysqli
 */
function db(): mysqli
{
    static $conn = null;

    if ($conn instanceof mysqli) {
        return $conn;
    }

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $conn->set_charset('utf8mb4');
    } catch (mysqli_sql_exception $e) {
        http_response_code(500);
        die('Database connection failed. Import portal/sql/schema.sql in phpMyAdmin, then refresh.');
    }

    return $conn;
}
