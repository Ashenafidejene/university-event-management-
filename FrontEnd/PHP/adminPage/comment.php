<?php
include("../../../Connection/connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['comment'])) {
        $eventID = $_POST['event_id'];
        $userUsername = $_POST['user_username'];
        $commentText = $_POST['comment'];

        $query = "INSERT INTO eventcomment (EventID, Comment, UserUsername) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iss", $eventID, $commentText, $userUsername);

        if ($stmt->execute()) {
            header("Location: adminPage.php?event_id=$eventID");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    if (isset($_POST['reply'])) {
        $commentID = $_POST['comment_id'];
        $adminUsername = $_POST['admin_username'];
        $replyText = $_POST['reply'];

        $query = "INSERT INTO adminreply (CommentID, Reply, AdminUsername) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iss", $commentID, $replyText, $adminUsername);

        if ($stmt->execute()) {
            // Retrieve the event ID for redirection
            $query = "SELECT EventID FROM eventcomment WHERE CommentID = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $commentID);
            $stmt->execute();
            $stmt->bind_result($eventID);
            $stmt->fetch();
            $stmt->close();

            header("Location: adminPage.php?event_id=$eventID");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>