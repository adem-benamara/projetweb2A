<?php
require_once('C:/xampp/htdocs/events_project/libs/qrcodes/qrlib.php'); // Include QR code library

// Function to generate a random date
function generateRandomDate() {
    $start = strtotime("2023-01-01"); // Start date
    $end = strtotime("2025-12-31"); // End date
    $randomTimestamp = mt_rand($start, $end); // Generate random timestamp
    return date("Y-m-d", $randomTimestamp); // Return date format (YYYY-MM-DD)
}

// Generate random event data
$event_name = "Event " . rand(1, 1000); // Random event name
$event_date = generateRandomDate(); // Generate random event date

// QR code content based on random event
$qr_code_data = "Event Name: $event_name, Date: $event_date";

// Define the directory and ensure it exists
$dir = 'C:/xampp/htdocs/events_project/public/qrcodes'; // Full path for directory
if (!is_dir($dir)) {
    mkdir($dir, 0777, true); // Create the directory with proper permissions if it doesn't exist
}

// Generate a unique filename for the QR code based on the event name
$filename = $dir . '/event_' . $event_name . '_' . time() . '.png'; // Add timestamp for uniqueness

// Debug: Check if the directory exists and is writable
if (!is_writable($dir)) {
    echo "Directory is not writable! Please check the folder permissions.";
} else {
    // Generate the QR code image and save it
    if (QRcode::png($qr_code_data, $filename)) {
        echo "QR Code saved successfully to: $filename";
    } else {
        echo "Failed to generate QR Code.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Random QR Code</title>
</head>
<body>

    <h1>Click the button to generate a random QR code and save it in the public/qrcodes directory</h1>

    <!-- Button to reload the page and generate a new QR code -->
    <form method="POST" action="">
        <input type="submit" value="Generate Random Event QR Code">
    </form>

    <h3>Generated QR Code for Event:</h3>
    <p>Event Name: <?php echo $event_name; ?></p>
    <p>Event Date: <?php echo $event_date; ?></p>

    <!-- Display the generated QR code image -->
    <img src="public/qrcodes/<?php echo basename($filename); ?>" alt="QR Code">

    <h3>QR Code Saved to: <?php echo $filename; ?></h3>

</body>
</html>
