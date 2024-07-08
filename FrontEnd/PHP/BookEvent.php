<?php
session_start();
include("../../connection/connection.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_id = intval($_POST['event_id']);
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);

    if (!isset($_SESSION['user_username'])) {
        echo "<script>alert('Please log in to book an event.'); window.location.href='SignIn.php';</script>";
        exit();
    }
    $user_username = $_SESSION['user_username'];
    $sql = "INSERT INTO eventbooking (EventID, UserID, BookingDateTime) VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $user_query = "SELECT UserID FROM uuser WHERE Username = ?";
    $user_stmt = $conn->prepare($user_query);
    $user_stmt->bind_param("s", $user_username);
    $user_stmt->execute();
    $user_stmt->bind_result($user_id);
    $user_stmt->fetch();
    $user_stmt->close();

    if ($user_id) {
        $stmt->bind_param("ii", $event_id, $user_id);
        if ($stmt->execute()) {
            echo "<script>alert('Booking successful!'); window.location.href='EventDetail.php?event_id=$event_id';</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "'); window.location.href='EventDetail.php?event_id=$event_id';</script>";
        }
    } else {
        echo "<script>alert('User not found.'); window.location.href='EventDetail.php?event_id=$event_id';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
