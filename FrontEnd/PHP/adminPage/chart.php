<?php
include("../../../Connection/connection.php");

$query = "
SELECT 
    uevent.EventName, 
    eventmetrics.EventID, 
    eventmetrics.TotalViews, 
    eventmetrics.TotalAttendees, 
    eventmetrics.TotalLikes, 
    eventmetrics.TotalDislikes, 
    eventmetrics.TotalComments 
FROM EventMetrics
INNER JOIN uevent ON eventmetrics.EventID = uevent.EventID
";
$result = $conn->query($query);

$events = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}

$conn->close();

echo json_encode($events);
?>