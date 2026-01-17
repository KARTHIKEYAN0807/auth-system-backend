<?php
// ===== CORS (MUST BE FIRST) =====
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Session-Id");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
// ===============================

header('Content-Type: application/json');

require_once __DIR__ . '/config/mysql.php';

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

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert user
$stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
$stmt->bind_param("ss", $email, $hashedPassword);

if ($stmt->execute()) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Registration successful'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Email already exists'
    ]);
}

$stmt->close();
$conn->close();
