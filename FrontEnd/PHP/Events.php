<?php
session_start();
include("../../Connection/connection.php"); 

$sql = "SELECT * FROM uevent ORDER BY EventDateTime DESC";

if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $sql = "SELECT * FROM uevent WHERE EventName LIKE '%$search%' ORDER BY EventDateTime DESC";
};

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Events - Uni-Event</title>
    <link rel="stylesheet" href="../styles/events.css"> 
</head>
<body>
    <div class="events-header">
        <div class="events-header-title">
            <h1>Upcoming Events</h1>
            <p>Explore a wide variety of events happening on campus. Don't miss out on the exciting activities and opportunities to connect with fellow students and faculty.</p>
        </div>
         
        <!-- Search Form -->
        <form action="Events.php" method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search events by name">
            <button type="submit">Search</button>
        </form>
    </div>
    
    <div class="event-container">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="event">';
                echo '<a class="event-link" href="eventDetail.php?event_id=' . htmlspecialchars($row['EventID']) . '">';
                echo "<img src=\"../Html(PHP)/adminPage/{$row['eventImage']}\">"; // Replace with actual event image
        
                // Limit the length of the EventDescription
                $eventDescription = htmlspecialchars($row['EventDescription']);
                $maxDescriptionLength = 100; // Set the maximum length of the description
                if (strlen($eventDescription) > $maxDescriptionLength) {
                    $eventDescription = substr($eventDescription, 0, $maxDescriptionLength) . "...";
                }
                echo '<h3>' . $eventDescription . '</h3>';
                echo '<div class="event-time-location">';
                echo '<p> <i class="fa-solid fa-calendar-days icon"></i>' . htmlspecialchars($row['EventDateTime']) . '</p>';
                echo '<p> <i class="fa-solid fa-location-dot icon"></i>'  . htmlspecialchars($row['Location']) . '</p>';
                echo '</div>';
                echo '<button>Learn More</button>';
                echo '</a>';
                echo '</div>';
            }
        } else {
            echo "<p>No events found</p>";
        }
        ?>
    </div>
</body>
</html>

<?php
$conn->close(); // Close database connection
?>
