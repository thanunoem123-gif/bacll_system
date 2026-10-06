<?php
$databaseUrl = getenv('DATABASE_URL') ?: getenv('MYSQL_URL');

if ($databaseUrl) {
    $database = parse_url($databaseUrl);

    $host = $database['host'] ?? 'localhost';
    $port = $database['port'] ?? 3306;
    $user = $database['user'] ?? 'root';
    $pass = $database['pass'] ?? '';
    $dbname = isset($database['path']) ? ltrim($database['path'], '/') : 'bac_system';
} else {
    $host = getenv('DB_HOST') ?: 'localhost';
    $port = getenv('DB_PORT') ?: 3306;
    $user = getenv('DB_USER') ?: 'root';
    $pass = getenv('DB_PASS') ?: '';
    $dbname = getenv('DB_NAME') ?: 'bac_system';
}

$conn = new mysqli($host, $user, $pass, $dbname, (int) $port);
if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>