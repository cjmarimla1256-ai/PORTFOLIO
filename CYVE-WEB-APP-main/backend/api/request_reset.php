<?php
include '../config.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email'])) {
    send_response(false, 'Email is required', [], 400);
}

$email = trim($data['email']);

$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $token = bin2hex(random_bytes(32));
    $expiry = date('Y-m-d H:i:s', time() + 3600); // 1 hour expiry

    $update = $conn->prepare("UPDATE users SET reset_token = ?, reset_expiry = ? WHERE id = ?");
    $update->bind_param("ssi", $token, $expiry, $user['id']);

    if ($update->execute()) {
        // SIMULATION: Log the reset link to a file instead of sending email
        $resetLink = "http://localhost:3000/reset-password?token=" . $token;
        file_put_contents(__DIR__ . '/../../logs/password_resets.log',
            date('[Y-m-d H:i:s]') . " Reset link for $email: $resetLink\n",
            FILE_APPEND);

        send_response(true, 'If an account exists with that email, a reset link has been sent.');
    }
    else {
        send_response(false, 'Error processing request', [], 500);
    }
}
else {
    // For security, don't reveal if email exists
    send_response(true, 'If an account exists with that email, a reset link has been sent.');
}

$stmt->close();
$conn->close();
?>
