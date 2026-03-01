<?php
/**
 * CYVE - Operational Calendar
 */
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Fetch all missions
$query = "SELECT e.*, u.username as creator FROM events e 
          LEFT JOIN users u ON e.created_by = u.id 
          WHERE e.status = 'approved' OR e.created_by = $user_id
          ORDER BY e.event_date ASC";
$result = $conn->query($query);
$events = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar - CYVE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles.css">
    <style>
        .dashboard-container { max-width: 900px; margin: 4rem auto; padding: 0 2rem; }
        .timeline { border-left: 2px solid #333; margin-left: 1rem; padding-left: 2rem; }
        .timeline-item { position: relative; margin-bottom: 3rem; }
        .timeline-item::before { content: ''; position: absolute; left: -2.7rem; top: 0.5rem; width: 12px; height: 12px; background: var(--color-1); border: 2px solid var(--color-2); border-radius: 50%; }
        .timeline-date { color: var(--color-2); font-weight: 700; font-size: 0.8rem; text-transform: uppercase; margin-bottom: 0.5rem; display: block; }
        .timeline-card { background: #141414; border: 1px solid #333; padding: 1.5rem; border-radius: 12px; }
        .timeline-card h3 { margin-bottom: 0.5rem; }
    </style>
</head>
<body>
    <nav class="navigation">
        <a href="../index.html" class="logo">CYVE</a>
        <ul class="nav-links">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="roadmaps.php">Roadmap</a></li>
            <li><a href="calendar.php">Calendar</a></li>
        </ul>
        <a href="dashboard.php?logout" class="btn-login" style="font-size: 1rem; padding: 0.5rem 1rem;">Logout</a>
    </nav>

    <div class="dashboard-container">
        <h1 style="font-size: 3rem; margin-bottom: 3rem;">Operational, Calendar</h1>

        <div class="timeline">
            <?php if (empty($events)): ?>
                <p style="color: #888;">No missions scheduled.</p>
            <?php
else: ?>
                <?php foreach ($events as $event): ?>
                    <div class="timeline-item">
                        <span class="timeline-date"><?php echo date('l, F d, Y @ H:i', strtotime($event['event_date'])); ?></span>
                        <div class="timeline-card">
                            <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                            <p style="color: #888; font-size: 0.9rem;"><?php echo htmlspecialchars($event['description']); ?></p>
                            <div style="margin-top: 1rem; font-size: 0.8rem; color: var(--color-2); text-transform: uppercase; font-weight: 700;">
                                📍 <?php echo htmlspecialchars($event['location'] ? $event['location'] : 'Remote'); ?>
                            </div>
                        </div>
                    </div>
                <?php
    endforeach; ?>
            <?php
endif; ?>
        </div>
    </div>
</body>
</html>
