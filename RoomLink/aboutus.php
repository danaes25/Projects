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

<h1><b>About Us</b></h1>
<div class="contact">
<h2><b>Luxury & Experience-Focused</b></h2>
<b>Experience hospitality redefined at RoomLink, a premier collection of hotels 
    where luxury meets sophistication.
    Every property is designed to deliver unforgettable moments, 
    with top-tier amenities, exceptional dining,
    and impeccable service. Our mission is simple: to create stays that inspire, refresh, 
    and delight, making every guest feel valued and cared for. 
    Step into Roomlink, where every visit is more than a stay—it’s an experience.</b>
</div>
<?php require_once "../views/footer.php"; ?>

</body>
</html>