<?php

// ===== CORS =====
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Session-Id");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
header('Content-Type: application/json');
// =================

require_once __DIR__ . '/config/redis.php';
require_once __DIR__ . '/config/mongodb.php';
require_once __DIR__ . '/config/mysql.php';

/* ======================
   SESSION CHECK (REDIS)
====================== */
$headers = array_change_key_case(getallheaders(), CASE_LOWER);
$sessionId = $headers['session-id'] ?? '';

if (!$sessionId) {
    echo json_encode(['status' => 'error', 'message' => 'No session']);
    exit;
}

$userId = $redis->get("session:$sessionId");

if (!$userId) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid session']);
    exit;
}

/* ======================
   FETCH EMAIL (MYSQL)
====================== */
$stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();

if (!$user) {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
    exit;
}

$email = $user['email'];

/* ======================
   GET PROFILE
====================== */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $profile = getProfileByUserId($userId);

    // If profile doesn't exist → create EMPTY profile (NO email)
    if (!$profile) {
        updateProfile($userId, [
            'name'  => '',
            'phone' => '',
            'age'   => 0,
            'city'  => '',
            'bio'   => ''
        ]);

        $profile = getProfileByUserId($userId);
    }

    echo json_encode([
        'status' => 'success',
        'profile' => [
            'name'  => $profile->name ?? '',
            'email' => $email, // ✅ ALWAYS FROM MYSQL
            'phone' => $profile->phone ?? '',
            'age'   => $profile->age ?? 0,
            'city'  => $profile->city ?? '',
            'bio'   => $profile->bio ?? ''
        ]
    ]);
    exit;
}

/* ======================
   UPDATE PROFILE
====================== */
$data = json_decode(file_get_contents("php://input"), true);

updateProfile($userId, [
    'name'  => $data['name'] ?? '',
    'phone' => $data['phone'] ?? '',
    'age'   => (int)($data['age'] ?? 0),
    'city'  => $data['city'] ?? '',
    'bio'   => $data['bio'] ?? ''
]);

echo json_encode([
    'status' => 'success',
    'message' => 'Profile updated'
]);
