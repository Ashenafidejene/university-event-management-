<?php
include("../../../Connection/connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $eventId = $_POST['eventId'];

    $query = "DELETE FROM uevent WHERE EventID=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $eventId);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>