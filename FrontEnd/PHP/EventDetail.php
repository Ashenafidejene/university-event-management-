
<?php
session_start();
include("../../connection/connection.php");

if (!isset($_GET['event_id'])) {
    echo "Invalid event ID.";
    exit();
}

$event_id = intval($_GET['event_id']);

if (!isset($_SESSION['user_username'])) {
    echo "Please log in to view this page.";
    exit();
}

$username = $_SESSION['user_username'];

// Increment view count
$increment_view_sql = "UPDATE uevent SET ViewCount = ViewCount + 1 WHERE EventID = ?";
$increment_stmt = $conn->prepare($increment_view_sql);
if ($increment_stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$increment_stmt->bind_param("i", $event_id);
$increment_stmt->execute();
$increment_stmt->close();

// Retrieve event details
$sql = "SELECT * FROM uevent WHERE EventID = $event_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $event = $result->fetch_assoc();
} else {
    echo "Event not found.";
    exit();
}

// Retrieve comments for the event
$comment_sql = "SELECT * FROM eventcomment WHERE EventID = $event_id AND ParentCommentID IS NULL ORDER BY CommentDateTime DESC";
$comment_result = $conn->query($comment_sql);
$comments = [];
if ($comment_result->num_rows > 0) {
    while ($comment = $comment_result->fetch_assoc()) {
        $comments[] = $comment;
    }
}

$conn->close();

$eventDate = new DateTime($event['EventDateTime']);
$formattedDate = $eventDate->format('F j');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($event['EventName']); ?></title>
    <link rel="stylesheet" href="../styles/EventDetail.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="event-detail-container">
        <div class="event-header">
            <div class="event-detail-header">
                <h1><?php echo htmlspecialchars($event['EventName']); ?></h1>
                <h1><?php echo $formattedDate; ?></h1>
            </div>
            <p><?php echo htmlspecialchars($event['EventDescription']); ?></p>
        </div>
        <div class="event-detail">
            <div class="event-image">
             <?php echo '<img src="../Html(PHP)/adminPage/' . htmlspecialchars($event['eventImage']) . '" alt="Event Image">'; ?>
            </div>
            <div class="event-info">
                <table>
                    <tr>
                        <td><i class="fas fa-calendar-alt"></i> <strong>Date and Time:</strong></td>
                        <td><?php echo htmlspecialchars($event['EventDateTime']); ?></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-map-marker-alt"></i> <strong>Location:</strong></td>
                        <td><?php echo htmlspecialchars($event['Location']); ?></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-tags"></i> <strong>Category:</strong></td>
                        <td><?php echo htmlspecialchars($event['Category']); ?></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-info-circle"></i> <strong>Status:</strong></td>
                        <td><?php echo htmlspecialchars($event['Status']); ?></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-eye"></i> <strong>Views:</strong></td>
                        <td><?php echo htmlspecialchars($event['ViewCount']); ?></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-users"></i> <strong>Attendees:</strong></td>
                        <td><?php echo htmlspecialchars($event['AttendeeCount']); ?></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-thumbs-up"></i> <strong>Likes:</strong></td>
                        <td id="like-count"><?php echo htmlspecialchars($event['LikeCount']); ?></td>
                    </tr>
<tr>
                        <td><i class="fas fa-thumbs-down"></i> <strong>Dislikes:</strong></td>
                        <td id="dislike-count"><?php echo htmlspecialchars($event['DislikeCount']); ?></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-user"></i> <strong>Writer:</strong></td>
                        <td><?php echo htmlspecialchars($event['WriterUsername']); ?></td>
                    </tr>
                </table>
                <div class="like-dislike-buttons">
                    <button id="like-button" data-event-id="<?php echo $event_id; ?>">I'm interested</button>
                    <button id="dislike-button" data-event-id="<?php echo $event_id; ?>">Not interested</button>
                </div>
                <div id="error-message" class="error-message"></div>
            </div>
        </div>

        <!-- Comment Section -->
        <div class="comment-section">
            <h2>Comments</h2>
            <button id="show-comments-button">Show Comments</button>
            <div id="comment-form" style="display: none;">
                <form action="submitComment.php" method="POST">
                    <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
                    <label for="comment">Comment:</label>
                    <textarea id="comment" name="comment" rows="4" required></textarea>
                    <button type="submit">Submit Comment</button>
                </form>
            </div>
            <div id="comments" class="comments" style="display: none;">
                <?php foreach ($comments as $comment): ?>
                    <div class="comment">
                        <p><strong><i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($comment['UserUsername']); ?></strong> <em><?php echo htmlspecialchars($comment['CommentDateTime']); ?></em></p>
                        <p><?php echo htmlspecialchars_decode(htmlspecialchars($comment['Comment'])); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Book an Event Section -->
        <div class="booking-section">
            <h2>Book your spot for <?php echo htmlspecialchars($event['EventName']); ?></h2>
            <form action="bookEvent.php" method="POST">
                <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                <label for="phone">Phone Number:</label>
                <input type="tel" id="phone" name="phone" required>
                <button type="submit">Book Now</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../javascript/EventDetail.js"></script>
</body>
</html>