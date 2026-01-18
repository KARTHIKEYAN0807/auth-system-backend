<?php
// ================= CORS (MUST BE FIRST) =================
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Session-Id");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
// =======================================================

header("Content-Type: application/json");

// ================= LOAD CONFIG =================
require_once __DIR__ . '/config/mysql.php';
require_once __DIR__ . '/config/redis.php';

// ================= READ JSON INPUT =================
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Email and password required"
    ]);
    exit;
}

// ================= FETCH USER =================
$stmt = $conn->prepare(
    "SELECT id, password FROM users WHERE email = ? LIMIT 1"
);
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    http_response_code(401);
    echo json_encode([
        "status" => "error",
        "message" => "Invalid credentials"
    ]);
    exit;
}

// ================= VERIFY PASSWORD =================
if (!password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode([
        "status" => "error",
        "message" => "Invalid credentials"
    ]);
    exit;
}

// ================= CREATE SESSION =================
$sessionId = bin2hex(random_bytes(32)); // strong token

// Store session in Redis (1 hour)
$redis->setex("session:$sessionId", 3600, (string)$user['id']);

// ================= SUCCESS RESPONSE =================
echo json_encode([
    "status" => "success",
    "session_id" => $sessionId
]);
