<?php
// Railway provides MySQL credentials as MYSQLHOST, MYSQLPORT, MYSQLUSER,
// MYSQLPASSWORD, MYSQLDATABASE. Also supports a single DATABASE_URL / MYSQL_URL
// if one is provided. Falls back to XAMPP defaults otherwise.

$databaseUrl = getenv('DATABASE_URL') ?: getenv('MYSQL_URL');

if ($databaseUrl) {
    $database = parse_url($databaseUrl);

    $host   = $database['host'] ?? 'localhost';
    $port   = $database['port'] ?? 3306;
    $user   = $database['user'] ?? 'root';
    $pass   = $database['pass'] ?? '';
    $dbname = isset($database['path']) ? ltrim($database['path'], '/') : 'bac_system';
} else {
    $host   = getenv('MYSQLHOST')     ?: getenv('DB_HOST') ?: 'localhost';
    $port   = getenv('MYSQLPORT')     ?: getenv('DB_PORT') ?: 3306;
    $user   = getenv('MYSQLUSER')     ?: getenv('DB_USER') ?: 'root';
    $pass   = getenv('MYSQLPASSWORD') ?: getenv('DB_PASS') ?: '';
    $dbname = getenv('MYSQLDATABASE') ?: getenv('DB_NAME') ?: 'bac_system';
}

$conn = new mysqli($host, $user, $pass, $dbname, (int) $port);

if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>