<?php
/**
 * CYVE - Secure User Registration
 * This script handles user signups and saves information securely in the database.
 */
include 'config.php';

$message = '';
$messageType = ''; // 'success' or 'error'

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullName = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $passwordRaw = $_POST['password'];

    // Server-side validation
    if (empty($fullName) || empty($email) || empty($passwordRaw)) {
        $message = "Please fill in all required fields.";
        $messageType = "error";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
        $messageType = "error";
    }
    elseif (strlen($passwordRaw) < 8) {
        $message = "Password must be at least 8 characters long.";
        $messageType = "error";
    }
    else {
        // Generate a clean username from email
        $username = strtolower(preg_replace('/[^A-Za-z0-9]/', '', strstr($email, '@', true)));
        if (empty($username)) {
            $username = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $fullName));
        }

        // Check if email or username already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "An account with this email or identity already exists.";
            $messageType = "error";
        }
        else {
            // Hash password using BCRYPT (industry standard)
            $passwordHash = password_hash($passwordRaw, PASSWORD_BCRYPT);

            // Insert new user with full_name field
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, role) VALUES (?, ?, ?, ?, 'user')");
            $stmt->bind_param("ssss", $username, $email, $passwordHash, $fullName);

            if ($stmt->execute()) {
                $message = "Welcome to CYVE! Your account has been created successfully. <a href='login.php' style='color: #f5be1e;'>Login now</a>";
                $messageType = "success";

                // Log activity
                log_activity($stmt->insert_id, 'signup', 'New user registered via signup.php');
            }
            else {
                $message = "Sorry, something went wrong during registration. Please try again.";
                $messageType = "error";
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join CYVE - Cybersecurity Learning Platform</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #f5be1e;
            --primary-dark: #d4a017;
            --bg: #0a0a0a;
            --card-bg: #141414;
            --text: #ffffff;
            --text-dim: #a0a0a0;
            --error: #ff4d4d;
            --success: #2ecc71;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            background-image: radial-gradient(circle at 50% 50%, #1a1a1a 0%, #0a0a0a 100%);
            color: var(--text);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .auth-card {
            background-color: var(--card-bg);
            border: 1px solid #333;
            border-radius: 12px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
        }

        .auth-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .auth-header h1 {
            color: var(--primary);
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 10px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .auth-header p {
            color: var(--text-dim);
            font-size: 14px;
            margin: 0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-dim);
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            background-color: #1a1a1a;
            border: 1px solid #333;
            border-radius: 6px;
            color: var(--text);
            font-size: 14px;
            box-sizing: border-box;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(245, 190, 30, 0.2);
        }

        .btn-signup {
            width: 100%;
            padding: 14px;
            background-color: var(--primary);
            color: #000;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.1s;
            margin-top: 10px;
        }

        .btn-signup:hover {
            background-color: var(--primary-dark);
        }

        .btn-signup:active {
            transform: scale(0.98);
        }

        .message {
            padding: 12px;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 20px;
            text-align: center;
        }

        .message.error {
            background-color: rgba(255, 77, 77, 0.1);
            color: var(--error);
            border: 1px solid var(--error);
        }

        .message.success {
            background-color: rgba(46, 204, 113, 0.1);
            color: var(--success);
            border: 1px solid var(--success);
        }

        .auth-footer {
            margin-top: 25px;
            text-align: center;
            font-size: 14px;
            color: var(--text-dim);
        }

        .auth-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-header">
            <h1>CYVE Enrollment</h1>
            <p>Secure your future in cybersecurity</p>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php
endif; ?>

        <form method="POST" action="signup.php">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="email@example.com" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Min 8 characters" required minlength="8">
            </div>

            <button type="submit" class="btn-signup">Create Account</button>
        </form>

        <div class="auth-footer">
            Already have an account? <a href="login.php">Login</a>
        </div>
    </div>
</body>
</html>
