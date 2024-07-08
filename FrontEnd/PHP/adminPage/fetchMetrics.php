<?php
include("../../../Connection/connection.php");

$query = "SELECT EventID, TotalViews, TotalAttendees, TotalLikes, TotalDislikes, TotalComments FROM eventmetrics";
$result = $conn->query($query);

$metricsData = [];
while ($row = $result->fetch_assoc()) {
    $metricsData[] = $row;
}

header('Content-Type: application/json');
echo json_encode($metricsData);
?>