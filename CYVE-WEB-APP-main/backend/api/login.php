<?php
header("Content-Type: application/json");
include '../config.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email']) || !isset($data['password'])) {
    send_response(false, 'Missing required fields');
}

$identity = sanitize($data['email']); // The field is named 'email' in JSON but carries identity
$password = $data['password'];

$stmt = $conn->prepare("SELECT id, username, password, role, full_name, email FROM users WHERE email = ? OR username = ?");
$stmt->bind_param("ss", $identity, $identity);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        log_activity($user['id'], 'login', 'User logged in via API');
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['username'];

        send_response(true, 'Access granted. Welcome to CYVE HQ.', [
            'user' => [
                'id' => $user['id'],
                'email' => $user['email'],
                'name' => $user['full_name'] ? $user['full_name'] : $user['username'],
                'role' => $user['role']
            ]
        ]);
    }
    else {
        // Log failed password attempt
        log_activity($user['id'], 'failed_login', 'Invalid password attempt via API');
        send_response(false, 'Invalid credentials. Please verify your password.', [], 401);
    }
}
else {
    send_response(false, 'Identity not recognized. No such agent in database.', [], 404);
}
?>
