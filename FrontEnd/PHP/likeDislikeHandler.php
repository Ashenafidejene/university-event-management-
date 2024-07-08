<?php
session_start();
include("../../connection/connection.php");

if (!isset($_SESSION['user_username'])) {
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to like or dislike an event.']);
    exit();
}

$user_username = $_SESSION['user_username'];
$event_id = intval($_POST['event_id']);
$action = $_POST['action'];

if ($action === 'like') {
    $check_like = $conn->prepare("SELECT * FROM userlike WHERE EventID = ? AND UserUsername = ?");
    $check_like->bind_param("is", $event_id, $user_username);
    $check_like->execute();
    $result_like = $check_like->get_result();

    if ($result_like->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'You have already liked this event.']);
    } else {
        $conn->begin_transaction();
        try {
            $insert_like = $conn->prepare("INSERT INTO userlike (EventID, UserUsername) VALUES (?, ?)");
            $insert_like->bind_param("is", $event_id, $user_username);
            $insert_like->execute();

            $update_event = $conn->prepare("UPDATE uevent SET LikeCount = LikeCount + 1 WHERE EventID = ?");
            $update_event->bind_param("i", $event_id);
            $update_event->execute();

            $conn->commit();
            echo json_encode(['status' => 'success', 'message' => 'Event liked successfully.']);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['status' => 'error', 'message' => 'An error occurred. Please try again.']);
        }
    }
} elseif ($action === 'dislike') {
    $check_dislike = $conn->prepare("SELECT * FROM userdislike WHERE EventID = ? AND UserUsername = ?");
    $check_dislike->bind_param("is", $event_id, $user_username);
    $check_dislike->execute();
    $result_dislike = $check_dislike->get_result();

    if ($result_dislike->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'You have already disliked this event.']);
    } else {
        $conn->begin_transaction();
        try {
            $insert_dislike = $conn->prepare("INSERT INTO userdislike (EventID, UserUsername) VALUES (?, ?)");
            $insert_dislike->bind_param("is", $event_id, $user_username);
            $insert_dislike->execute();

            $update_event = $conn->prepare("UPDATE uevent SET DislikeCount = DislikeCount + 1 WHERE EventID = ?");
            $update_event->bind_param("i", $event_id);
            $update_event->execute();

            $conn->commit();
            echo json_encode(['status' => 'success', 'message' => 'Event disliked successfully.']);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['status' => 'error', 'message' => 'An error occurred. Please try again.']);
        }
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
}
$conn->close();
?>
