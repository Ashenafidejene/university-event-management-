<?php
include("../../../Connection/connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form inputs
    $eventName = $_POST['eventName'];
    $eventDescription = $_POST['eventDescription'];
    $eventDateTime = $_POST['eventDateTime'];
    $location = $_POST['location'];
    $category = $_POST['category'];
    $status = $_POST['status'];
    $writerUsername = $_POST['writerUsername'];
    $eventImage = NULL;

    // Handle file upload
    if (isset($_FILES['Imag']) && $_FILES['Imag']['error'] == 0) {
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($_FILES["Imag"]["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if the file is an actual image or a fake image
        $check = getimagesize($_FILES["Imag"]["tmp_name"]);
        if ($check === false) {
            die("File is not an image.");
        }

        // Check file size (e.g., limit to 5MB)
        if ($_FILES["Imag"]["size"] > 5000000) {
            die("Sorry, your file is too large.");
        }

        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
            die("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
        }

        if (move_uploaded_file($_FILES["Imag"]["tmp_name"], $targetFile)) {
            $eventImage = $targetFile;
        } else {
            die("Sorry, there was an error uploading your file.");
        }
    }

    // Insert into the database
    $query = "INSERT INTO uevent (EventName, EventDescription, eventimage, EventDateTime, Location, Category, Status, WriterUsername) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        die("Error preparing the statement: " . $conn->error);
    }
    if (!$stmt->bind_param("ssssssss", $eventName, $eventDescription, $eventImage, $eventDateTime, $location, $category, $status, $writerUsername)) {
        die("Error binding parameters: " . $stmt->error);
    }

    if ($stmt->execute()) {
        echo "New event created successfully";
        // Redirect to fullInfo page or any other page if needed
        header("Location: /path/to/fullInfoPage.php");
        exit();
    } else {
        die("Error executing the statement: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
}
?>