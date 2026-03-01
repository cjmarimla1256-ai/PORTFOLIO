<?php
// Use __DIR__ to ensure correct path resolution regardless of CWD
$configPath = __DIR__ . '/../config.php';
$sqlPath = __DIR__ . '/../../database/cyve.sql';

if (!file_exists($configPath)) {
    die("Config file not found at: $configPath");
}
if (!file_exists($sqlPath)) {
    die("SQL file not found at: $sqlPath");
}

include $configPath;

$sql = file_get_contents($sqlPath);

if ($conn->multi_query($sql)) {
    echo "Database setup successfully!";
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());
}
else {
    echo "Error setting up database: " . $conn->error;
}

$conn->close();
?>
