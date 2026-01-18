<?php

$host = getenv("MYSQLHOST");
$user = getenv("MYSQLUSER");
$pass = getenv("MYSQLPASSWORD");
$db   = getenv("MYSQLDATABASE");
$port = getenv("MYSQLPORT");

if (!$host || !$user || !$db || !$port) {
    http_response_code(500);
    die("MySQL env vars missing");
}

$conn = new mysqli($host, $user, $pass, $db, (int)$port);

if ($conn->connect_error) {
    http_response_code(500);
    die("MySQL connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
