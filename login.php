<?php
header('Content-Type: application/json');

require_once __DIR__ . '/config/mysql.php';
require_once __DIR__ . '/config/redis.php';

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (!$email || !$password) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Email and password required'
    ]);
    exit;
}

// Check user in MySQL
$stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || !password_verify($password, $user['password'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid credentials'
    ]);
    exit;
}

// Create session token
$sessionId = bin2hex(random_bytes(16));

// Store session in Redis (1 hour)
$redis->setex("session:$sessionId", 3600, $user['id']);

echo json_encode([
    'status' => 'success',
    'session_id' => $sessionId
]);
