<?php
include '../config.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['token']) || !isset($data['password'])) {
    send_response(false, 'Token and new password are required', [], 400);
}

$token = $data['token'];
$passwordRaw = $data['password'];

if (strlen($passwordRaw) < 8) {
    send_response(false, 'Password must be at least 8 characters', [], 400);
}

$stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expiry > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $passwordHash = password_hash($passwordRaw, PASSWORD_BCRYPT);

    $update = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE id = ?");
    $update->bind_param("si", $passwordHash, $user['id']);

    if ($update->execute()) {
        send_response(true, 'Password has been reset successfully. You can now login.');
    }
    else {
        send_response(false, 'Error updating password', [], 500);
    }
}
else {
    send_response(false, 'Invalid or expired token', [], 400);
}

$stmt->close();
$conn->close();
?>
