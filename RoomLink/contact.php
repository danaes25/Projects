<!DOCTYPE html>
<html>
<head>
  <title>Contact Us</title>
  <link rel="stylesheet" href="../roomlink.css">
</head>
<body class="body">

<?php
session_start();

// Check if user is logged in
if (isset($_SESSION['user_type'])) {
    if ($_SESSION['user_type'] === 'admin') {
        require_once "../views/header.php";
    } elseif ($_SESSION['user_type'] === 'staff') {
        require_once "../views/headers.php";
    } elseif ($_SESSION['user_type'] === 'guest') {
        require_once "../views/headerg.php";
    }
} else {
    // Not logged in → use guest header or redirect to login
    require_once "views/headerb.php";
    // Optional redirect:
    // header("Location: views/login.php");
    // exit();
}
?>

<h1><b>Contact Us</b></h1>

<div class="contact">
    <p><b>If you have any inquiries please contact us through email:</b></p><br>
    <a href="https://mail.google.com/mail/u/0/" class="email">RoomLink.help@gmail.com</a><br><br>

    <input class="popin" type="text" id="name" name="name" placeholder="Name"><br><br>
    <input class="popin" type="email" id="email" name="email" placeholder="Email" required><br><br>
    <input class="popin" type="tel" id="phone" name="phone" placeholder="Phone" pattern="[0-9]{11}" maxlength="11"><br><br>
    <input class="comment" type="text" id="comment" name="comment" placeholder="Comment"><br>
    <input class="contactbutton" type="submit" value="Send" 
           onclick="alert('Submit successfully'); window.location.href='homepage.html'">
</div>

<?php require_once "../views/footer.php"; ?>

</body>
</html>
