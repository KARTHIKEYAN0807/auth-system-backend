<?php
// ===== CORS (MUST BE FIRST) =====
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Session-Id");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
// ===============================

header('Content-Type: application/json');

require_once __DIR__ . '/config/redis.php';

// Read headers safely (case-insensitive)
$headers = array_change_key_case(getallheaders(), CASE_LOWER);
$sessionId = $headers['session-id'] ?? '';

if (!$sessionId) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Session ID missing'
    ]);
    exit;
}

// Check session in Redis
$userId = $redis->get("session:$sessionId");

if (!$userId) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Session expired or invalid'
    ]);
    exit;
}

// Session valid
echo json_encode([
    'status' => 'success',
    'user_id' => (int)$userId
]);
