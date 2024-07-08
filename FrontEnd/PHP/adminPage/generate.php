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

if ($result->num_rows > 0) {
    $reportContent = "Event Summary Report\n\n";
    while ($row = $result->fetch_assoc()) {
        $reportContent .= "Event Name: " . $row['EventName'] . " (Event ID: " . $row['EventID'] . ")\n";
        $reportContent .= "Total Views: " . number_format($row['TotalViews']) . "\n";
        $reportContent .= "Total Attendees: " . number_format($row['TotalAttendees']) . "\n";
        $reportContent .= "Total Likes: " . number_format($row['TotalLikes']) . "\n";
        $reportContent .= "Total Dislikes: " . number_format($row['TotalDislikes']) . "\n";
        $reportContent .= "Total Comments: " . number_format($row['TotalComments']) . "\n";
        $reportContent .= "---------------------------\n";
    }
} else {
    $reportContent = "No metrics available.";
}

$conn->close();

// Create a temporary file and write the report content to it
$tempFile = tempnam(sys_get_temp_dir(), 'report_');
file_put_contents($tempFile, $reportContent);

// Set headers to download the file
header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="event_metrics_report.txt"');
header('Content-Length: ' . filesize($tempFile));

// Read the file and output its contents
readfile($tempFile);

// Delete the temporary file
unlink($tempFile);
?>