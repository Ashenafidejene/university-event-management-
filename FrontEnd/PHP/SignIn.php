<?php
session_start();
include("../../Connection/connection.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST["user_Id"];
    $password = $_POST["password"];

    if (!empty($user_id) && !empty($password)) {
        $stmt = $conn->prepare("SELECT * FROM Uuser WHERE UserId = ? LIMIT 1");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                $user_data = mysqli_fetch_assoc($result);
                if ($user_data["Password"] === $password) {
                    $_SESSION['user_username'] = $user_data['Username']; 
                    header("Location: FrontPage.php");
                    exit();
                } else {
                    $error = "Incorrect password, please try again";
                }
            } else {
                $error = "User not found with that username";
            }
        } else {
            $error = "Query failed";
        }
    } else {
        $error = "Please enter both username and password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign-In</title>
    <link rel="stylesheet" href="../styles/SignIn.css">
</head>
<body>
    <div class="container"> 
        <div class="form-container">
            <h1>Sign In</h1>
            <h3>Enter your credentials</h3>
            <?php if (!empty($error)) : ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <form method="POST">
                <label for="user_Id">Id</label> <br> 
                <input 
                    type="text" 
                    placeholder="Id..."
                    name="user_Id"
                > <br>
                <label for="password">Password</label> <br> 
                <input 
                    type="password" 
                    placeholder="Password..."
                    name="password"
                > 
                <div class="button">
                    <button type="submit">Sign In</button>
                </div>
            </form>
            <div class="form-footer">
                <p>Don't have an account?</p>
                <a href="SignUp.php">Sign Up instead</a>
            </div>
        </div>
        <img src="https://www.compassroseevents.com/wp-content/uploads/2015/04/iStock-476949098-1800x700.jpg" alt="Event Image">
    </div>
</body>
</html>
