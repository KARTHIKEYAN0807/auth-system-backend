<?php

$host = getenv("MYSQL_HOST");
$user = getenv("MYSQL_USER");
$pass = getenv("MYSQL_PASSWORD");
$db   = getenv("MYSQL_DATABASE");
$port = getenv("MYSQL_PORT");

if (!$host || !$user || !$db || !$port) {
    http_response_code(500);
    die("MySQL env vars missing");
}

$conn = new mysqli($host, $user, $pass, $db, (int)$port);

if ($conn->connect_error) {
    http_response_code(500);
    die("MySQL connection failed");
}

$conn->set_charset("utf8mb4");
