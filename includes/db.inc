<?php
declare(strict_types=1);
require_once __DIR__ . '/../settings.php';
require_once __DIR__ . '/functions.inc';

function db(): mysqli
{
    static $connection = null;
    if ($connection instanceof mysqli) {
        return $connection;
    }
    global $host, $user, $pwd, $sql_db;
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    try {
        $connection = new mysqli($host, $user, $pwd, $sql_db);
        $connection->set_charset('utf8mb4');
        return $connection;
    } catch (mysqli_sql_exception $e) {
        error_log('FABWEB database error: ' . $e->getMessage());
        http_response_code(503);
        exit('Database unavailable. Import fabweb_db.sql using phpMyAdmin and check settings.php.');
    }
}

