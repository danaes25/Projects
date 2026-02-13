<?php
session_start();
require_once "../models/db.php";
require_once "../models/booking.php";
require_once 'auth.php';
checkPageAccess('guestroom.php');

if (!isset($_SESSION['user_id'])) die("Please login first");
$userId = $_SESSION['user_id'];

if (!isset($_GET['room_id'])) die("Room not found");
$roomId = (int)$_GET['room_id'];

// Fetch room info
$sql = "SELECT rooms.*, hotels.name AS hotel_name 
        FROM rooms JOIN hotels ON rooms.hotel_id = hotels.hotel_id
        WHERE rooms.room_id=$roomId";
$result = mysqli_query($conn = Database::getInstance()->getConnection(), $sql);
if (mysqli_num_rows($result) == 0) die("Room not found");
$room = mysqli_fetch_assoc($result);

// User currency
$res = mysqli_query($conn, "SELECT preferred_currency FROM users WHERE user_id=$userId");
$user = mysqli_fetch_assoc($res);
$currency = $user['preferred_currency'] ?? 'USD';

$rates = ['USD'=>1,'EUR'=>0.93,'GBP'=>0.82,'JPY'=>150.25,'EGP'=>30.95,'AUD'=>1.56,'CAD'=>1.36,'CHF'=>0.91];
$symbols = ['USD'=>'$','EUR'=>'€','GBP'=>'£','JPY'=>'¥','EGP'=>'E£','AUD'=>'A$','CAD'=>'C$','CHF'=>'CHF'];

$convertedPrice = $room['price'] * ($rates[$currency] ?? 1);
$currencySymbol = $symbols[$currency] ?? '$';

// Handle reservation
// 6. Handle reservation submission (unchanged)
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['reserve'])) {
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];

    $overlapQuery = "SELECT * FROM reservations
                     WHERE room_id=$roomId AND status='active'
                     AND (checkin <= '$checkout' AND checkout >= '$checkin')";
    $overlapResult = mysqli_query($conn, $overlapQuery);

    if (mysqli_num_rows($overlapResult)>0) {
        echo "<script>alert('Sorry, this room is already booked for the selected dates.');</script>";
    } else {
        mysqli_query($conn, "INSERT INTO reservations (user_id, room_id, checkin, checkout, status)
                             VALUES ($userId,$roomId,'$checkin','$checkout','active')");
        header("Location: history.php");
        exit;
    }
}

// Favorite status
if (isset($_GET['fav_action'])) {
    $favRes = mysqli_query($conn, "SELECT rooms_favorite FROM users WHERE user_id=$userId");
    $favRow = mysqli_fetch_assoc($favRes);
    $favRooms = array_filter(explode(',', $favRow['rooms_favorite'] ?? ''));

    if ($_GET['fav_action']==='add' && !in_array($roomId,$favRooms)) $favRooms[]=$roomId;
    elseif ($_GET['fav_action']==='remove' && in_array($roomId,$favRooms)) $favRooms=array_diff($favRooms,[$roomId]);

    mysqli_query($conn, "UPDATE users SET rooms_favorite='".implode(',',$favRooms)."' WHERE user_id=$userId");
    header("Location: guestroom.php?room_id=$roomId");
    exit;
}
$favRes = mysqli_query($conn, "SELECT rooms_favorite FROM users WHERE user_id=$userId");
$favRow = mysqli_fetch_assoc($favRes);
$favRooms = array_filter(explode(',', $favRow['rooms_favorite'] ?? ''));
$isFav = in_array($roomId,$favRooms);

// Fetch reviews
$reviews = [];
$reviewResult = mysqli_query($conn, "SELECT r.comment, u.first_name, u.last_name
                                     FROM reviews r
                                     JOIN users u ON r.user_id=u.user_id
                                     WHERE r.room_id=$roomId
                                     ORDER BY r.created_at DESC");
while($r = mysqli_fetch_assoc($reviewResult)) $reviews[] = $r;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($room['name']) ?></title>
<link rel="stylesheet" href="../roomlink.css">
</head>
<body>
<?php include "../views/headerg.php"; ?>

<div class="room-container">
  <div class="room-name"><?= htmlspecialchars($room['name']) ?></div>
  <div class="room-price"><?= $currencySymbol ?><?= number_format($convertedPrice,2) ?> per night</div>
  <img src="../<?= htmlspecialchars($room['photo']) ?>" class="room-photo" alt="<?= htmlspecialchars($room['name']) ?>">



  <div class="bottom-section">
    <div class="room-description">
      <p>Hotel: <?= htmlspecialchars($room['hotel_name']) ?></p>
      <?= nl2br(htmlspecialchars($room['description'])) ?>

      <div class="button-row">
        <a href="#reserve" class="book-btn">Reserve Now</a>
        <?php if($isFav): ?>
            <a href="?room_id=<?= $roomId ?>&fav_action=remove" class="fav-btn">Unfavorite ♥</a>
        <?php else: ?>
            <a href="?room_id=<?= $roomId ?>&fav_action=add" class="fav-btn">Favorite ♡</a>
        <?php endif; ?>
      </div>
    </div>

    <div class="comments">
      <h3>Guest Reviews</h3>
      <?php if(count($reviews) > 0): ?>
        <?php foreach($reviews as $rev): ?>
          <div class="comment"><b><?= htmlspecialchars($rev['first_name'].' '.$rev['last_name']) ?>:</b> <?= htmlspecialchars($rev['comment']) ?></div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="comment">No reviews yet</div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Reservation Popup -->
<div class="reserve-popup" id="reserve">
  <div class="reserve-box">
    <h2>Reserve This Room</h2>
    <form method="post">
      <input type="hidden" name="reserve" value="1">
      <label>Check-in Date</label>
      <input type="date" name="checkin" min="<?= date('Y-m-d') ?>" required>
      <label>Check-out Date</label>
      <input type="date" name="checkout" min="<?= date('Y-m-d') ?>" required>
      <div class="popup-buttons">
        <button class="book-btn" type="submit">Confirm</button>
        <a href="#" class="close-btn">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?php include "../views/footer.php"; ?>
</body>
</html>
