<?php
header('Content-Type: application/json');

require_once __DIR__ . '/config/redis.php';
require_once __DIR__ . '/config/mongodb.php';
require_once __DIR__ . '/config/mysql.php'; // gives $conn (mysqli)

/* ======================
   SESSION CHECK
====================== */
$headers = getallheaders();
$sessionId = $headers['Session-Id'] ?? '';

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
   GET PROFILE
====================== */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    // Try MongoDB first
    $profile = getProfileByUserId($userId);

    // 🔥 If profile not exists → create with ONLY EMAIL
    if (!$profile) {

        // Fetch email from MySQL
        $stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user) {
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
            exit;
        }

        // Create profile in MongoDB
        updateProfile($userId, [
            'name'  => '',                 // ❌ DO NOT AUTO FILL
            'email' => $user['email'],     // ✅ ONLY EMAIL
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
            'email' => $profile->email ?? '',
            'phone' => $profile->phone ?? '',
            'age'   => $profile->age ?? '',
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

// ❌ EMAIL IS NOT UPDATED FROM PROFILE PAGE
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
