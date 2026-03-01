<?php
/**
 * Page Header Component
 * 
 * Displays the main page header with title and live clock.
 * The clock updates every second showing Manila timezone.
 */
?>
<header class="page-header">
    <h1>Flight Schedule System</h1>
    <p id="currentDateTime"></p>
</header>

<script>
/**
 * Update date and time display every second
 * Shows current date and time in Manila timezone (Asia/Manila)
 * Format: "Weekday, Month Day, Year HH:MM:SS AM/PM"
 */
function updateDateTime() {
    const now = new Date();
    const options = { 
        weekday: 'long', year: 'numeric', month: 'long', 
        day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit',
        hour12: true 
    };
    const manilaTime = now.toLocaleString('en-PH', { ...options, timeZone: 'Asia/Manila' });
    document.getElementById('currentDateTime').textContent = manilaTime;
}

// Initial call to display time immediately
updateDateTime();

// Update every second (1000ms)
setInterval(updateDateTime, 1000);
</script>