<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uni-Event</title>
    <link rel="stylesheet" href="../styles/FrontPage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<nav class="navigation-bar">
        <div class="navigation-logo-container">
            <img src="../images/logo3.jpg" alt="Logo">
            <div class="message-container">
                <h1>Welcome to the University Event Management System</h1>
                <p>Manage and discover exciting events happening on campus. Don't miss the events taking place on your campus.</p>
                <div>
                    <button onclick="window.location.href='events.php'">Explore Events</button>
                </div>
            </div>
        </div>
        <div class="navigation-links-container">
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#about">About Us</a></li>
                <li><a href="#contact">Contact Us</a></li>
                <?php
                if (isset($_SESSION['user_username'])) {
                    echo '<li><a href="profile.php"> <i class="fa-solid fa-user"></i> ' . htmlspecialchars($_SESSION['user_username']) . '</a></li>';
                   
                } else {
                    echo '<li><a href="SignIn.php">Login</a></li>';
                    echo '<li><a href="SignUp.php">Register</a></li>';
                }
                ?>
            </ul>
        </div>
    </nav>

    <div class="status-container">
        <div class="status">
            <p class="number">325+</p> 
            <p>CLIENTS <br> WORLD-WIDE</p>
        </div>
        <div class="status">
            <p class="number">325+</p> 
            <p>PROJECTS <br> COMPLETED</p>
        </div>
        <div class="status">
            <p class="number">325+</p> 
            <p>TEAM <br> MEMBERS</p>
        </div>
        <div class="status">
            <p class="number">325+</p> 
            <p>REVENUE <br> GENERATED</p>
        </div>
    </div>

    <div class="main-container">
        <div class="main-container-header">
            <h1>Explore Different Events on Campus</h1>
            <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Non modi et numquam ad. Deleniti ducimus cum eos voluptatibus, itaque architecto enim quibusdam fuga, veniam doloremque numquam quisquam laborum dolorum dolore!</p>
        </div>
        <div class="event-container">
        <?php
include("../../connection/connection.php");

$sql = "SELECT * FROM uevent ORDER BY EventDateTime DESC LIMIT 6";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<div class="event">';
        echo '<a class="event-link" href="eventDetail.php?event_id=' . htmlspecialchars($row['EventID']) . '">';
        echo "<img src=\"../Html(PHP)/adminPage/{$row['eventImage']}\">"; // Replace with actual event image
        
        // Limit the length of the EventDescription
        $eventDescription = htmlspecialchars($row['EventDescription']);
        $maxDescriptionLength = 100; // Set the maximum length of the description
        if (strlen($eventDescription) > $maxDescriptionLength) {
            $eventDescription = substr($eventDescription, 0, $maxDescriptionLength) . "...";
        }
        echo '<h3>' . $eventDescription . '</h3>';
        
        echo '<div class="event-time-location">';
        echo '<p> <i class="fa-solid fa-calendar-days icon"></i>' . htmlspecialchars($row['EventDateTime']) . '</p>';
        echo '<p> <i class="fa-solid fa-location-dot icon"></i>' . htmlspecialchars($row['Location']) . '</p>';
        echo '</div>';
        echo '<button>Learn More</button>';
        echo '</a>';
        echo '</div>';
    }
} else {
    echo "<p>No events found</p>";
}

$conn->close();
?>
        </div>
    </div>
    <div class="about-container" id="about">
        <div class="about-header">
            <h1>About Us</h1>
            <p>Welcome to the University Event Management System. Our mission is to help you manage and discover exciting events on campus with ease.</p>
        </div>
        <div class="about-content">
            <div class="about-section">
                <h2>Our Mission</h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam scelerisque leo at vehicula ultrices. Vivamus id sapien sit amet magna convallis lacinia.</p>
            </div>
            <div class="about-section">
                <h2>Our Team</h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam scelerisque leo at vehicula ultrices. Vivamus id sapien sit amet magna convallis lacinia.</p>
            </div>
            <div class="about-section">
                <h2>Our Values</h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam scelerisque leo at vehicula ultrices. Vivamus id sapien sit amet magna convallis lacinia.</p>
            </div>
        </div>
    </div>


    <div class="contact-us-container" id="contact">
        <div class="contact-header">
            <h1>Contact Us</h1>
            <p>If you have any questions or inquiries, feel free to reach out to us using the form below.</p>
        </div>
        <div class="contact-content">
            <form action="submitContact.php" method="POST">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>

                <label for="message">Message</label>
                <textarea id="message" name="message" rows="6" required></textarea>

                <button type="submit">Submit</button>
            </form>
        </div>
    </div>
    <footer class="footer">
    <div class="footer-content">
        <div class="footer-section about">
            <h1>About Uni-Event</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam scelerisque leo at vehicula ultrices. Vivamus id sapien sit amet magna convallis lacinia.</p>
            <div class="contact">
                <span><i class="fa-solid fa-envelope icon"></i> Email: contact@uni-event.com</span>
                <span><i class="fa-solid fa-phone icon"></i> Phone: +1234567890</span>
            </div>
            <div class="socials">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
        <div class="footer-section links">
            <h2>Quick Links</h2>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#about">About Us</a></li>
                <li><a href="#contact">Contact Us</a></li>
                <li><a href="#">Events</a></li>
                <li><a href="#">Terms of Service</a></li>
                <li><a href="#">Privacy Policy</a></li>
            </ul>
        </div>
        <div class="footer-section contact-form">
            <h2>Contact Us</h2>
            <form action="submitContact.php" method="POST">
                <input type="text" name="name" class="text-input contact-input" placeholder="Your Name">
                <input type="email" name="email" class="text-input contact-input" placeholder="Your Email">
                <textarea name="message" class="text-input contact-input" placeholder="Your Message"></textarea>
                <button type="submit" class="btn btn-big contact-btn">
                    <i class="fa-solid fa-envelope"></i>
                    Send
                </button>
            </form>
        </div>
    </div>
    <div class="footer-bottom">
        &copy; 2024 Uni-Event. All rights reserved.
    </div>
</footer>
</body>
</html>
