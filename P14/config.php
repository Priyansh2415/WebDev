<?php
declare(strict_types=1);

const DB_HOST = '127.0.0.1';
const DB_NAME = 'practical14_db';
const DB_USER = 'root';
const DB_PASS = ''; // Typical XAMPP default. Set your own credentials if changed.

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    exit('Database connection failed. Check config.php and import database.sql.');
}
