<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$message = '';

// Handle event creation (similar to dashboard)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_event'])) {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $event_date = $_POST['event_date'];
    $location = sanitize($_POST['location']);

    $stmt = $conn->prepare("INSERT INTO events (title, description, event_date, location, created_by) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $title, $description, $event_date, $location, $user_id);
    if ($stmt->execute()) {
        $message = 'Event created successfully.';
        log_activity($user_id, 'create_event', "Created event: $title");
    } else {
        $message = 'Failed to create event.';
    }
    $stmt->close();
}

// Fetch upcoming events
$query = "SELECT e.*, u.username as creator FROM events e LEFT JOIN users u ON e.created_by = u.id WHERE e.status = 'approved' AND e.event_date >= NOW() ORDER BY e.event_date ASC";
$result = $conn->query($query);
$upcoming_events = $result->fetch_all(MYSQLI_ASSOC);

// Fetch all events for calendar view
$query = "SELECT * FROM events WHERE status = 'approved' ORDER BY event_date ASC";
$result = $conn->query($query);
$all_events = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar - CYVE</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navigation">
        <a href="index.html" class="logo">CYVE</a>
        <ul class="nav-links">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="roadmaps.php">Roadmaps</a></li>
            <li><a href="calendar.php">Calendar</a></li>
        </ul>
        <a href="dashboard.php?logout" class="btn-login">Logout</a>
    </nav>

    <div class="container">
        <h2>Calendar</h2>
        <?php if ($message) echo "<p class='message'>$message</p>"; ?>

        <h3>Create Event</h3>
        <form method="POST">
            <input type="text" name="title" placeholder="Event Title" required>
            <textarea name="description" placeholder="Description"></textarea>
            <input type="datetime-local" name="event_date" required>
            <input type="text" name="location" placeholder="Location">
            <button type="submit" name="create_event">Create Event</button>
        </form>

        <h3>Upcoming Events</h3>
        <div class="event-list">
            <?php if (empty($upcoming_events)): ?>
                <p>No upcoming events.</p>
            <?php else: ?>
                <?php foreach ($upcoming_events as $event): ?>
                <div class="event-item">
                    <h4><?php echo htmlspecialchars($event['title']); ?></h4>
                    <p><?php echo htmlspecialchars($event['description']); ?></p>
                    <p><strong>Date:</strong> <?php echo date('F j, Y g:i A', strtotime($event['event_date'])); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
                    <p><strong>Created by:</strong> <?php echo htmlspecialchars($event['creator']); ?></p>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <h3>All Events</h3>
        <div id="calendar"></div>
    </div>

    <script>
        // Simple calendar implementation
        const events = <?php echo json_encode($all_events); ?>;
        const calendarEl = document.getElementById('calendar');

        // Group events by date
        const eventsByDate = {};
        events.forEach(event => {
            const date = new Date(event.event_date).toDateString();
            if (!eventsByDate[date]) eventsByDate[date] = [];
            eventsByDate[date].push(event);
        });

        // Display events
        Object.keys(eventsByDate).sort().forEach(date => {
            const dateDiv = document.createElement('div');
            dateDiv.className = 'calendar-date';
            dateDiv.innerHTML = `<h4>${date}</h4>`;
            eventsByDate[date].forEach(event => {
                const eventDiv = document.createElement('div');
                eventDiv.className = 'calendar-event';
                eventDiv.innerHTML = `<p><strong>${event.title}</strong> - ${event.location}</p>`;
                dateDiv.appendChild(eventDiv);
            });
            calendarEl.appendChild(dateDiv);
        });
    </script>
</body>
</html>
