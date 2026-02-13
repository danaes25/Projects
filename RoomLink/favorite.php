<?php
session_start();
include "../models/db.php";
$db = Database::getInstance();
$conn = $db->getConnection();
require_once "../models/UserFactory.php";

if (!isset($_SESSION['user_id'])) {
    die("<h3>Please login to see favorites</h3>");
}

$userId = $_SESSION['user_id'];

/* REMOVE FROM FAVORITES */
if (isset($_GET['fav_action'], $_GET['room_id']) && $_GET['fav_action'] === 'remove') {

    $roomId = (int)$_GET['room_id'];

    $favRes = mysqli_query($conn, "SELECT rooms_favorite FROM users WHERE user_id = $userId");
    $favRow = mysqli_fetch_assoc($favRes);

    $favRooms = array_filter(explode(',', $favRow['rooms_favorite'] ?? ''));

    // Remove room
    $favRooms = array_diff($favRooms, [$roomId]);

    $favStr = implode(',', $favRooms);
    mysqli_query($conn, "UPDATE users SET rooms_favorite='$favStr' WHERE user_id=$userId");

    // 🔁 Stay on favorite.php
    header("Location: favorite.php");
    exit;
}

// Get favorite room IDs
$favRes = mysqli_query($conn, "SELECT rooms_favorite FROM users WHERE user_id = $userId");
$favRow = mysqli_fetch_assoc($favRes);
$favRooms = array_filter(explode(',', $favRow['rooms_favorite'] ?? ''));

?>
<!DOCTYPE html>
<html>
<head>
  <title>Favorite Rooms</title>
  <link rel="stylesheet" href="../roomlink.css">
</head>


<body class="body">

<?php include "../views/headerg.php"; ?>

<h1 class="headtitlee"><b>Favorite Rooms</b></h1>

<?php
if (empty($favRooms)) {
    echo "<h3 style='text-align:center; margin-top:50px;'>No favorite rooms yet. Start exploring and add your favorites!</h3>";
} else {

    $favIds = implode(',', $favRooms);

    $sql = "SELECT rooms.*, hotels.name AS hotel_name
            FROM rooms
            JOIN hotels ON rooms.hotel_id = hotels.hotel_id
            WHERE rooms.room_id IN ($favIds)";
    $result = mysqli_query($conn, $sql);
?>

<figure class="imgg">
<?php while ($room = mysqli_fetch_assoc($result)): ?>
  <div class="img">

    <a class="imglink" href="guestroom.php?room_id=<?= $room['room_id'] ?>">
      <img src="../<?= htmlspecialchars($room['photo']) ?>" alt="<?= htmlspecialchars($room['name']) ?>">
      <figcaption><b><?= htmlspecialchars($room['name']) ?></b></figcaption>
    </a>

    <br>

<button class="buttonc"
        onclick="window.location.href='favorite.php?fav_action=remove&room_id=<?= $room['room_id'] ?>'">
    <b>Remove</b>
</button>



  </div>
<?php endwhile; ?>
</figure>

<?php include "../views/footer.php"; ?>
</body>
</html>
<?php }?>