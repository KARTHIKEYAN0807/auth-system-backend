<?php

header('Content-Type: application/json');

require_once __DIR__ . '/config/redis.php';

$headers = getallheaders();
$sessionId = $headers['Session-Id'] ?? '';

if (!$sessionId) {
    echo json_encode(['status' => 'error', 'message' => 'Session ID missing']);
    exit;
}

$userId = $redis->get("session:$sessionId");

if (!$userId) {
    echo json_encode(['status' => 'error', 'message' => 'Session expired or invalid']);
    exit;
}

echo json_encode([
    'status' => 'success',
    'user_id' => $userId
]);
