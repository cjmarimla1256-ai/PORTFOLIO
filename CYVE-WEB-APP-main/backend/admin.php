<?php
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

// Fetch statistics
$user_count = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$event_count = $conn->query("SELECT COUNT(*) as count FROM events")->fetch_assoc()['count'];
$roadmap_count = $conn->query("SELECT COUNT(*) as count FROM roadmaps")->fetch_assoc()['count'];
$note_count = $conn->query("SELECT COUNT(*) as count FROM notes")->fetch_assoc()['count'];

// Fetch recent audit logs
$audit_logs = $conn->query("SELECT a.*, u.username FROM audit_log a LEFT JOIN users u ON a.user_id = u.id ORDER BY a.timestamp DESC LIMIT 20");

// Fetch pending events for approval
$pending_events = $conn->query("SELECT * FROM events WHERE status = 'pending' ORDER BY created_at DESC");

// Fetch all users
$users = $conn->query("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - CYVE</title>
    <link rel="stylesheet" href="../public/styles.css">
    <style>
        .stats { display: flex; gap: 1rem; margin-bottom: 2rem; }
        .stat { background: #1d1b20; padding: 1rem; border-radius: 8px; text-align: center; }
        .stat h3 { margin: 0; color: #f5be1e; }
        .stat p { margin: 0.5rem 0 0; font-size: 2rem; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 2rem; }
        th, td { padding: 0.5rem; border: 1px solid #333; text-align: left; }
        th { background: #1d1b20; }
    </style>
</head>
<body>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="roadmaps.php">Roadmaps</a>
        <a href="calendar.php">Calendar</a>
        <a href="admin.php">Admin</a>
        <a href="logout.php">Logout</a>
    </nav>

    <div class="container">
        <h1>Admin Panel</h1>

        <h2>Statistics</h2>
        <div class="stats">
            <div class="stat">
                <h3>Total Users</h3>
                <p><?php echo $user_count; ?></p>
            </div>
            <div class="stat">
                <h3>Total Events</h3>
                <p><?php echo $event_count; ?></p>
            </div>
            <div class="stat">
                <h3>Total Roadmaps</h3>
                <p><?php echo $roadmap_count; ?></p>
            </div>
            <div class="stat">
                <h3>Total Notes</h3>
                <p><?php echo $note_count; ?></p>
            </div>
        </div>

        <h2>Pending Events for Approval</h2>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Created By</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($event = $pending_events->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($event['title']); ?></td>
                        <td><?php echo htmlspecialchars($event['created_by']); ?></td>
                        <td><?php echo $event['event_date']; ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $event['id']; ?>">
                                <button type="submit" name="approve_event">Approve</button>
                            </form>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $event['id']; ?>">
                                <button type="submit" name="reject_event">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h2>All Users</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo $user['role']; ?></td>
                        <td><?php echo $user['created_at']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h2>Recent Audit Logs</h2>
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($log = $audit_logs->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($log['username']); ?></td>
                        <td><?php echo htmlspecialchars($log['action']); ?></td>
                        <td><?php echo htmlspecialchars($log['details']); ?></td>
                        <td><?php echo $log['timestamp']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
// Handle approvals/rejections
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['approve_event'])) {
        $id = (int)$_POST['id'];
        $stmt = $conn->prepare("UPDATE events SET status = 'approved', approved_by = ? WHERE id = ?");
        $stmt->bind_param("ii", $_SESSION['user_id'], $id);
        $stmt->execute();
        log_activity($_SESSION['user_id'], 'approve_event', "Approved event ID: $id");
        $stmt->close();
        header('Location: admin.php');
        exit();
    } elseif (isset($_POST['reject_event'])) {
        $id = (int)$_POST['id'];
        $stmt = $conn->prepare("UPDATE events SET status = 'rejected', approved_by = ? WHERE id = ?");
        $stmt->bind_param("ii", $_SESSION['user_id'], $id);
        $stmt->execute();
        log_activity($_SESSION['user_id'], 'reject_event', "Rejected event ID: $id");
        $stmt->close();
        header('Location: admin.php');
        exit();
    }
}
?>
