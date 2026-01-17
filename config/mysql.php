<?php

$host = getenv("MYSQL_HOST");
$user = getenv("MYSQL_USER");
$pass = getenv("MYSQL_PASSWORD");
$db   = getenv("MYSQL_DATABASE");
$port = getenv("MYSQL_PORT");

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed"
    ]);
    exit;
}
