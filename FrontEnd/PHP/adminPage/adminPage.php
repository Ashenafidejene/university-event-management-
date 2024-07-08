<?php
session_start();
include("../../../Connection/connection.php");

// Define the upload directory
define('UPLOAD_DIR', 'uploads/');

// Check if the form was submitted
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
        $targetDir = UPLOAD_DIR;
        $targetFile = $targetDir . basename($_FILES["Imag"]["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if the file is an actual image
        $check = getimagesize($_FILES["Imag"]["tmp_name"]);
        if ($check === false) {
            die("File is not an image.");
        }

        // Check file size
        if ($_FILES["Imag"]["size"] > 5000000) {
            die("Sorry, your file is too large.");
        }

        // Allow certain file formats
        if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
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
        header("Location: adminPage.php?event_id=" . $conn->insert_id);
        exit();
    } else {
        die("Error executing the statement: " . $stmt->error);
    }

    $stmt->close();
}

// Fetch comments and replies if event_id is set
$comments = [];
$replies = [];

if (isset($_GET['event_id'])) {
    $eventID = $_GET['event_id'];

    // Fetch comments for the event
    $commentQuery = "SELECT * FROM eventcomment WHERE EventID = ? ORDER BY CommentDateTime DESC";
    $stmt = $conn->prepare($commentQuery);
    $stmt->bind_param("i", $eventID);
    $stmt->execute();
    $commentResult = $stmt->get_result();
    $comments = $commentResult->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Fetch replies for the comments
    $replyQuery = "SELECT * FROM adminreply WHERE CommentID IN (SELECT CommentID FROM eventcomment WHERE EventID = ?)";
    $stmt = $conn->prepare($replyQuery);
    $stmt->bind_param("i", $eventID);
    $stmt->execute();
    $replyResult = $stmt->get_result();

    while ($row = $replyResult->fetch_assoc()) {
        $replies[$row['CommentID']][] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <link rel="stylesheet" href="../../styles/adminPage.css">
    <script defer src="https://use.fontawesome.com/releases/v6.0.0/js/all.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../../javascript/adminPage.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            fetch('chart.php')
                .then(response => response.json())
                .then(data => {
                    var dataArray = [['Event Name', 'Views', 'Attendees', 'Likes', 'Dislikes', 'Comments']];
                    data.forEach(event => {
                        dataArray.push([
                            event.EventName + ' (ID: ' + event.EventID + ')',
                            parseInt(event.TotalViews),
                            parseInt(event.TotalAttendees),
                            parseInt(event.TotalLikes),
                            parseInt(event.TotalDislikes),
                            parseInt(event.TotalComments)
                        ]);
                    });

                    var dataTable = google.visualization.arrayToDataTable(dataArray);

                    var options = {
                        title: 'Event Metrics',
                        hAxis: {title: 'Event Name'},
                        vAxis: {title: 'Metrics'},
                        seriesType: 'bars',
                        series: {5: {type: 'line'}}
                    };

                    var chart = new google.visualization.ComboChart(document.getElementById('chart_div'));
                    chart.draw(dataTable, options);
                });
        }
    </script>
    <script>
        function filterTable(event) {
            const filter = event.target.value.toLowerCase();
            const rows = document.querySelectorAll('#eventTable tbody tr');

            rows.forEach(row => {
                const title = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                if (title.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('search-input').addEventListener('input', filterTable);
        });
    </script>

<body>
    </head>

    <body>
        <header class="header">
            <div class="logo">
                <img class="logo-img" src="../../images/logo3.jpg" alt="Logo">
            </div>
            <div class="searchBar">
                <form method="POST">
                    <i class="fa fa-search i" aria-hidden="true"></i>
                    <input id="search-input" class="searchBar-input" type="text" placeholder="Search">
                </form>
            </div>
            <div class="addEventButton">
                <button class="addEventButton-btn">ADD Event</button>
            </div>
            <div class="contactInfo">
                <img class="AdminPhoto" src="../../images/user.png" alt="Admin Photo">
                <label> <?php echo htmlspecialchars($_SESSION['user_username']); ?></label>
            </div>
        </header>
        <div class="container">
            <section class="VerticalNav">
                <a href="#" class="toggleBox">
                    <span class="icon"></span>
                </a>
                <ul class="navItems">
                    <li>

                        <a class="home" href="#">
                            <i class="fa fa-home" style="--i:1"></i>
                        </a>
                        <span style="--g:1">Home</span>
                    </li>
                    <li>
                        <a class="Expire" href="#">
                            <i class="fa fa-times-circle" style="--i:2"></i>
                        </a>
                        <span style="--g:2">Expire</span>
                    </li>
                    <li>
                        <a class="upcoming" href="#">
                            <i class="fa fa-spinner" style="--i:3"></i>
                        </a>
                        <span style="--g:3">Upcoming</span>
                    </li>
                    <li>
                        <a class="summary" href="#">
                            <i class="fa fa-pie-chart" aria-hidden="true" style="--i:4"></i>
                        </a>
                        <span style="--g:4">Summary</span>
                    </li>
                    <li>
                        <a class="logout" href="http://localhost/website/groupWork/University-Event-Management-System2/FrontEnd/Html(PHP)/FrontPage.php">
                            <i class="fa fa-sign-out" aria-hidden="true" style="--i:5"></i>
                        </a>
                        <span style="--g:5">Logout</span>
                    </li>
                </ul>
            </section>
            <section class="right">
                <div class="fullInfo table enactive">
                    <div class="table_header">
                        <h1>Info About</h1>
                    </div>
                    <div class="table_body">
                        <table id="eventTable">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Expire Date</th>
                                    <th>Photo</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Fetch data from the database and display in the table
                                $query = "SELECT * FROM uevent";
                                $result = $conn->query($query);

                                if (!$result) {
                                    // Query failed, handle error
                                    die("Query failed: " . $conn->error);
                                }

                                if ($result->num_rows > 0) {
                                    // Output data of each row
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>{$row['EventName']}</td>";
                                        echo "<td>{$row['EventDateTime']}</td>";

                                        // Display the image
                                        $imagePath = !empty($row['eventImage']) ? $row['eventImage'] : './uploads/photo_2023-08-29_21-23-45.jpg';
                                        echo "<td><img src='./{$imagePath}' alt='Event Image' width='50'></td>";

                                        echo "<td>
            <a class='edit' href='javascript:void(0);' data-id='{$row['EventID']}' data-name='{$row['EventName']}' data-description='{$row['EventDescription']}' data-datetime='{$row['EventDateTime']}' data-location='{$row['Location']}' data-category='{$row['Category']}' data-status='{$row['Status']}' data-writer='{$row['WriterUsername']}'>
                <i class='fa fa-pencil-square' aria-hidden='true'></i>
            </a>
            <a class='delete' href='javascript:void(0);' data-id='{$row['EventID']}'>
                <i class='fa fa-trash' aria-hidden='true'></i>
            </a>
        </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    // No records found
                                    echo "<tr><td colspan='4'>No records found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="ADDEvent">
                    <form class="form-container" method="post" enctype="multipart/form-data">
                        <h2>Create Event</h2>
                        <label class="form-label" for="eventName">Event Name:</label>
                        <input class="form-input" type="text" id="eventName" name="eventName" required>
                        <label class="form-label" for="eventImage">Image of Event:</label>
                        <input class="form-input" type="file" name="Imag">
                        <label class="form-label" for="eventDescription">Event Description:</label>
                        <textarea id="eventDescription" name="eventDescription" required></textarea>
                        <label class="form-label" for="eventDateTime">Event Date and Time:</label>
                        <input class="form-input" type="datetime-local" id="eventDateTime" name="eventDateTime" required>
                        <label class="form-label" for="location">Location:</label>
                        <input class="form-input" type="text" id="location" name="location" required>
                        <label class="form-label" for="category">Category:</label>
                        <input class="form-input" type="text" id="category" name="category" required>
                        <label class="form-label" for="status">Status:</label>
                        <select id="status" name="status" class="form-input">
                            <option value="draft">Draft</option>
                            <option value="published" selected>Published</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <label class="form-label" for="writerUsername">Writer Username:</label>
                        <input class="form-input" type="text" id="writerUsername" name="writerUsername" required>
                        <button class="btn-submit" type="submit">Create Event</button>
                    </form>
                </div>
                <div class="expireEvent table enactive">
                    <div class="table_header">
                        <h2>Expire Event</h2>
                    </div>
                    <div class="table_body">
                        <table>
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Expire Date</th>
                                    <th>Photo</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Fetch expired events and display in the table
                                $query = "SELECT * FROM uevent WHERE EventDateTime < NOW()";
                                $result = $conn->query($query);
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>{$row['EventName']}</td>";
                                        echo "<td>{$row['EventDateTime']}</td>";

                                        // Display the image
                                        $imagePath = !empty($row['eventImage']) ? $row['eventImage'] : './uploads/photo_2023-08-29_21-23-45.jpg';
                                        echo "<td><img src='./{$imagePath}' alt='Event Image' width='50'></td>";

                                        echo "<td>
                                        <a class='edit' href='javascript:void(0);' data-id='{$row['EventID']}' data-name='{$row['EventName']}' data-description='{$row['EventDescription']}' data-datetime='{$row['EventDateTime']}' data-location='{$row['Location']}' data-category='{$row['Category']}' data-status='{$row['Status']}' data-writer='{$row['WriterUsername']}'>
                                            <i class='fa fa-pencil-square' aria-hidden='true'></i>
                                        </a>
                                        <a class='delete' href='javascript:void(0);' data-id='{$row['EventID']}'>
                                            <i class='fa fa-trash' aria-hidden='true'></i>
                                        </a>
                                    </td>";
                                        echo "</tr>";
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="UpcomingEvent table enactive">
                    <div class="table_header">
                        <h2>Upcoming Event</h2>
                    </div>
                    <div class="table_body">
                        <table>
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Expire Date</th>
                                    <th>Photo</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Fetch upcoming events and display in the table
                                $query = "SELECT * FROM uevent WHERE EventDateTime >= NOW()";
                                $result = $conn->query($query);
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>{$row['EventName']}</td>";
                                        echo "<td>{$row['EventDateTime']}</td>";
                                        // Display the image
                                        $imagePath = !empty($row['eventImage']) ? $row['eventImage'] : './uploads/photo_2023-08-29_21-23-45.jpg';
                                        echo "<td><img src='./{$imagePath}' alt='Event Image' width='50'></td>";
                                        echo "<td>
                                        <a class='edit' href='javascript:void(0);' data-id='{$row['EventID']}' data-name='{$row['EventName']}' data-description='{$row['EventDescription']}' data-datetime='{$row['EventDateTime']}' data-location='{$row['Location']}' data-category='{$row['Category']}' data-status='{$row['Status']}' data-writer='{$row['WriterUsername']}'>
                                            <i class='fa fa-pencil-square' aria-hidden='true'></i>
                                        </a>
                                        <a class='delete' href='javascript:void(0);' data-id='{$row['EventID']}'>
                                            <i class='fa fa-trash' aria-hidden='true'></i>
                                        </a>
                                    </td>";
                                        echo "</tr>";
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="comment-section">
                    <?php foreach ($comments as $comment) : ?>
                        <p><?php echo htmlspecialchars($comment['Comment']); ?></p>
                        <span>By: <?php echo htmlspecialchars($comment['UserUsername']); ?> at <?php echo htmlspecialchars($comment['CommentDateTime']); ?></span>
                        <div class="replies">
                            <?php if (isset($replies[$comment['CommentID']])) : ?>
                                <?php foreach ($replies[$comment['CommentID']] as $reply) : ?>
                                    <div class="reply">
                                        <p><?php echo htmlspecialchars($reply['Reply']); ?></p>
                                        <span>By: <?php echo htmlspecialchars($reply['AdminUsername']); ?> at <?php echo htmlspecialchars($reply['ReplyDateTime']); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <form action="" method="POST" class="reply-form">
                            <textarea name="reply" placeholder="Write a reply..."></textarea>
                            <input type="hidden" name="comment_id" value="<?php echo $comment['CommentID']; ?>">
                            <input type="hidden" name="admin_username" value="adminUsername">
                            <button type="submit">Reply</button>
                        </form>
                    <?php endforeach; ?>
                </div>
                <!-- Chart section -->
                <div class="Chart enactive">
                    <div class="metrics-box">
                        <h2>Event Summary</h2>
                        <div id="metrics-container" class="metrics-container">
                            <!-- PHP code for fetching and displaying metrics data -->
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
";;
                            $result = $conn->query($query);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo '<div class="event-box">';
                                    echo '<h3>' . $row['EventName'] . ' (Event ID: ' . $row['EventID'] . ')</h3>';
                                    echo '<p><span class="metric-title">Total Views:</span> <span class="metric-value">' . number_format($row['TotalViews']) . '</span></p>';
                                    echo '<p><span class="metric-title">Total Attendees:</span> <span class="metric-value">' . number_format($row['TotalAttendees']) . '</span></p>';
                                    echo '<p><span class="metric-title">Total Likes:</span> <span class="metric-value">' . number_format($row['TotalLikes']) . '</span></p>';
                                    echo '<p><span class="metric-title">Total Dislikes:</span> <span class="metric-value">' . number_format($row['TotalDislikes']) . '</span></p>';
                                    echo '<p><span class="metric-title">Total Comments:</span> <span class="metric-value">' . number_format($row['TotalComments']) . '</span></p>';
                                    echo '</div>';
                                }
                            } else {
                                echo '<p>No metrics available.</p>';
                            }

                            $conn->close();
                            ?>
                            <div id="chart_div" ></div>
                        </div>
                        <a href="generate.php" ><button class="generate-report-button">Download Text Report</button> </a> 
                    </div>
                </div>
            </section>
        </div>
        <!-- Edit Event Modal -->
        <div id="editEventModal" class="modal">
            <div class="modal-content">
                <h2>Edit the Event</h2>
                <span class="close">&times;</span>
                <form id="editEventForm">
                    <input type="hidden" id="editEventId" name="eventId">
                    <div class="form-group">
                        <label for="editEventName">Event Name:</label>
                        <input type="text" id="editEventName" name="eventName" required>
                    </div>
                    <div class="form-group">
                        <label for="editEventDescription">Event Description:</label>
                        <textarea id="editEventDescription" name="eventDescription" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editEventDateTime">Event Date and Time:</label>
                        <input type="datetime-local" id="editEventDateTime" name="eventDateTime" required>
                    </div>
                    <div class="form-group">
                        <label for="editLocation">Location:</label>
                        <input type="text" id="editLocation" name="location" required>
                    </div>
                    <div class="form-group">
                        <label for="editCategory">Category:</label>
                        <input type="text" id="editCategory" name="category" required>
                    </div>
                    <div class="form-group">
                        <label for="editStatus">Status:</label>
                        <input type="text" id="editStatus" name="status" required>
                    </div>
                    <div class="form-group">
                        <label for="editWriterUsername">Writer Username:</label>
                        <input type="text" id="editWriterUsername" name="writerUsername" required>
                    </div>
                    <button type="submit" class="sum_btw">Save Changes</button>
                </form>
            </div>
        </div>
        </div>
    </body>
</html>
<!----# --->