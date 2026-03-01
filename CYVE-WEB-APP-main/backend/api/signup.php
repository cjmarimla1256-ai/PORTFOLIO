<?php
header("Content-Type: application/json");
include '../config.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['name']) || !isset($data['email']) || !isset($data['password'])) {
    send_response(false, 'Missing required fields', [], 400);
}

$fullName = trim($data['name']);
$email = trim($data['email']);
$passwordRaw = $data['password'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    send_response(false, 'Invalid email format', [], 400);
}

if (strlen($passwordRaw) < 8) {
    send_response(false, 'Password must be at least 8 characters', [], 400);
}

$username = strtolower(preg_replace('/[^A-Za-z0-9]/', '', strstr($email, '@', true)));
if (empty($username)) {
    $username = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $fullName));
}

$passwordHash = password_hash($passwordRaw, PASSWORD_BCRYPT);

$stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
$stmt->bind_param("ss", $email, $username);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    send_response(false, 'Email or username already exists', [], 409);
}

$stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, role) VALUES (?, ?, ?, ?, 'user')");
$stmt->bind_param("ssss", $username, $email, $passwordHash, $fullName);

if ($stmt->execute()) {
    $userId = $stmt->insert_id;
    $_SESSION['user_id'] = $userId;
    $_SESSION['email'] = $email;
    $_SESSION['role'] = 'user';
    $_SESSION['username'] = $username;

    send_response(true, 'Registration successful', [
        'user' => [
            'id' => $userId,
            'email' => $email,
            'name' => $fullName,
            'role' => 'user'
        ]
    ], 201);
}
else {
    send_response(false, 'Registration failed', [], 500);
}
?>
