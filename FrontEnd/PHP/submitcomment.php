<?php
session_start();
include("../../connection/connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_id = intval($_POST['event_id']);
    $username = $_SESSION['user_username'];
    $comment = htmlspecialchars($_POST['comment']);

    if (!empty($comment)) {
        $sql = "INSERT INTO eventcomment (EventID, Comment, UserUsername, CommentDateTime) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $event_id, $comment, $username);

        if ($stmt->execute()) {
            echo "Comment added successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Please fill in all fields.";
    }

    $stmt->close();
    $conn->close();
    header("Location: EventDetail.php?event_id=$event_id");
    exit();
} else {
    echo "Invalid request method.";
}
?>
