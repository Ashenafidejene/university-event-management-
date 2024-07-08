<?php
    session_start();
    session_destroy();
    header("Location: SignIn.php");
    exit();
?>
