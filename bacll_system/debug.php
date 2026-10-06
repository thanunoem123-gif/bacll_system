<?php
header('Content-Type: text/plain');

echo "=== Environment Variables ===\n";
echo "MYSQLHOST:     " . (getenv('MYSQLHOST') ?: '(not set)') . "\n";
echo "MYSQLPORT:     " . (getenv('MYSQLPORT') ?: '(not set)') . "\n";
echo "MYSQLUSER:     " . (getenv('MYSQLUSER') ?: '(not set)') . "\n";
echo "MYSQLPASSWORD: " . (getenv('MYSQLPASSWORD') ? '(set, length=' . strlen(getenv('MYSQLPASSWORD')) . ')' : '(not set)') . "\n";
echo "MYSQLDATABASE: " . (getenv('MYSQLDATABASE') ?: '(not set)') . "\n";
echo "MYSQL_URL:     " . (getenv('MYSQL_URL') ? '(set)' : '(not set)') . "\n";
echo "DATABASE_URL:  " . (getenv('DATABASE_URL') ? '(set)' : '(not set)') . "\n";

echo "\n=== Direct Connection Test ===\n";

$host = getenv('MYSQLHOST');
$port = (int) getenv('MYSQLPORT');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$db   = getenv('MYSQLDATABASE');

echo "Attempting: $user@$host:$port/$db\n";

$conn = @new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    echo "CONNECTION FAILED: " . $conn->connect_error . "\n";
} else {
    echo "CONNECTION OK!\n";

    $result = $conn->query("SHOW TABLES");
    echo "Tables: ";
    if ($result) {
        $tables = [];
        while ($row = $result->fetch_array()) $tables[] = $row[0];
        echo empty($tables) ? "(none)" : implode(", ", $tables);
    }
    echo "\n";
    $conn->close();
}