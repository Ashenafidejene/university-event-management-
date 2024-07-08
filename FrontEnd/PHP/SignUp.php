<?php
    session_start();

    include("../../Connection/connection.php");

    $error = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $full_name = $_POST["full_name"];
        $user_name = $_POST["user_name"];
        $email = $_POST["email"];
        $user_id = $_POST["user_id"];
        $password = $_POST["password"];
        $confirm_password = $_POST["confirm_password"];

        if (!empty($full_name) && !empty($user_name) && !empty($email) && !empty($user_id) && !empty($password) && !empty($confirm_password)) {
            $email_query = "SELECT * FROM uuser WHERE Email = '$email' LIMIT 1";
            $result = mysqli_query($conn, $email_query);
            if (mysqli_num_rows($result) > 0) {
                $error = "The user with this email already exists. Please try again with a different email.";
            } elseif ($password !== $confirm_password) {
                $error = "The password is incorrect. Please try again.";
            } else {
                $query = "INSERT INTO uuser (UserId, Username, FullName, Email, Password) VALUES ('$user_id', '$user_name', '$full_name', '$email', '$password')";
                mysqli_query($conn, $query);
                header("Location: SignIn.php");
                exit();
            }
        }  else {
            $error = "Something went wrong. Please enter valid information.";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign-Up</title>
    <link rel="stylesheet" href="../styles/SignUp.css">
</head>
<body>
    <div class="container"> 
        <div class="form-container">
            <h1>Sign Up</h1>
            <h3>Create your account</h3>
            <?php if (!empty($error)) : ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <form method="POST" action="">
                <label for="full_name">Full name</label> <br>
                <input type="text" placeholder="full name..." name="full_name"> <br>
                <label for="user_name">User name</label> <br>
                <input type="text" placeholder="user name..." name="user_name"> <br>
                <label for="email">Email</label> <br>
                <input type="text" placeholder="email..." name="email"> <br>
                <label for="user_id">Id</label> <br>
                <input type="text" placeholder="id..." name="user_id"> <br>
                <label for="password">Password</label> <br>
                <input type="password" placeholder="password..." name="password"> <br>
                <label for="confirm_password">Confirm password</label> <br>
                <input type="password" placeholder="confirm password..." name="confirm_password"> <br>
                <div class="button">
                    <button type="submit">Sign Up</button>
                </div>
            </form>
            <div class="form-footer">
                <p>Have an account?</p>
                <a href="SignIn.php">Login instead</a>
            </div>
        </div>
        <img src="https://www.compassroseevents.com/wp-content/uploads/2015/04/iStock-476949098-1800x700.jpg" alt="">
    </div>
</body>
</html>