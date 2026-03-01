<?php
include '../config.php';
header("Content-Type: application/json");

if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT id, email, full_name, username, role FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        send_response(true, 'Session active', [
            'user' => [
                'id' => $user['id'],
                'email' => $user['email'],
                'name' => $user['full_name'] ? $user['full_name'] : $user['username'],
                'role' => $user['role']
            ]
        ]);
    }
}
send_response(false, 'Not logged in');
?>
