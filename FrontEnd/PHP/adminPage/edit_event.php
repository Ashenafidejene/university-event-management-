<?php
session_start();
include("../../../Connection/connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $eventId = $_POST['eventId'];
    $eventName = $_POST['eventName'];
    $eventDescription = $_POST['eventDescription'];
    $eventDateTime = $_POST['eventDateTime'];
    $location = $_POST['location'];
    $category = $_POST['category'];
    $status = $_POST['status'];
    $writerUsername = $_POST['writerUsername'];
    $eventImage = NULL;

    // If an image is uploaded, process the image
    if (isset($_FILES['Imag']) && $_FILES['Imag']['error'] == 0) {
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($_FILES["Imag"]["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if the file is an actual image
        $check = getimagesize($_FILES["Imag"]["tmp_name"]);
        if ($check === false) {
            die("File is not an image.");
        }

        // Check file size (max 5MB)
        if ($_FILES["Imag"]["size"] > 5000000) {
            die("Sorry, your file is too large.");
        }

        // Allow only certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            die("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
        }

        // Try to upload the file
        if (move_uploaded_file($_FILES["Imag"]["tmp_name"], $targetFile)) {
            $eventImage = $targetFile;
        } else {
            die("Sorry, there was an error uploading your file.");
        }
    }

    // Prepare the SQL query to update the event details
    $query = "UPDATE uevent SET EventName=?, EventDescription=?, EventDateTime=?, Location=?, Category=?, Status=?, WriterUsername=?";
    if ($eventImage !== NULL) {
        $query .= ", eventimage=?";
    }
    $query .= " WHERE EventID=?";
    $stmt = $conn->prepare($query);

    // Bind parameters to the query
    if ($eventImage !== NULL) {
        $stmt->bind_param("ssssssssi", $eventName, $eventDescription, $eventDateTime, $location, $category, $status, $writerUsername, $eventImage, $eventId);
    } else {
        $stmt->bind_param("sssssssi", $eventName, $eventDescription, $eventDateTime, $location, $category, $status, $writerUsername, $eventId);
    }

    // Execute the query and check for success
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>