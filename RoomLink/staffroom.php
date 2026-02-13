<?php
require_once "../models/db.php";
require_once 'auth.php';
checkPageAccess('staffroom.php');

$db = Database::getInstance();
$conn = $db->getConnection();

/* 1. Get room ID from URL */
if (!isset($_GET['room_id'])) {
    die("Room not found");
}
$roomId = (int)$_GET['room_id'];

/* 2. Fetch room info */
$sql = "SELECT rooms.*, hotels.name AS hotel_name 
        FROM rooms 
        JOIN hotels ON rooms.hotel_id = hotels.hotel_id 
        WHERE rooms.room_id = $roomId";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) == 0) die("Room not found");
$room = mysqli_fetch_assoc($result);

/* 3. Handle housekeeping toggle */
if (isset($_POST['toggle_housekeeping'])) {
    $newStatus = $room['housekeeping_status'] === 'done' ? 'needed' : 'done';
    mysqli_query($conn, "UPDATE rooms SET housekeeping_status='$newStatus' WHERE room_id=$roomId");
    header("Location: staffroom.php?room_id=$roomId");
    exit;
}

/* 4. Handle staff reply */
if (isset($_POST['reply'])) {
    $reply = mysqli_real_escape_string($conn, $_POST['reply']);
    mysqli_query($conn, "UPDATE rooms SET staff_reply='$reply' WHERE room_id=$roomId");
    header("Location: staffroom.php?room_id=$roomId");
    exit;
}

// Refresh room data
$result = mysqli_query($conn, $sql);
$room = mysqli_fetch_assoc($result);

?>
<!DOCTYPE html>
<html>
<head>
  <title>Staff - Manage Room</title>
  <link rel="stylesheet" href="../roomlink.css">
</head>
<body>
<?php include "../views/headers.php"; ?>

<div class="room-container">

  <div class="room-name"><?= htmlspecialchars($room['name']) ?></div>
  <div class="room-price">$<?= $room['price'] ?> per night</div>
  <p><strong>Hotel:</strong> <?= htmlspecialchars($room['hotel_name']) ?></p>

  <img src="../<?= $room['photo'] ?>" class="room-photo" alt="<?= htmlspecialchars($room['name']) ?>">

  <div class="room-description">
      <?= nl2br(htmlspecialchars($room['description'])) ?>
  </div>

  <!-- Housekeeping Status -->
  <div class="housekeeping-status <?= $room['housekeeping_status'] === 'done' ? 'status-done' : 'status-needed' ?>">
      <?= $room['housekeeping_status'] === 'done' ? 'Housekeeping Completed' : 'Housekeeping Needed' ?>
  </div>

  <form method="post">
      <button type="submit" name="toggle_housekeeping" class="toggle-btn">
          Toggle Housekeeping
      </button>
  </form>

  <!-- Staff Reply -->
  <div class="comments">
      <h3>Guest Reviews</h3>
      <?php if (!empty($room['guest_comments'])): ?>
          <?php foreach (explode("\n", $room['guest_comments']) as $comment): ?>
              <div class="comment"><?= htmlspecialchars($comment) ?></div>
          <?php endforeach; ?>
      <?php else: ?>
          <div class="comment">No guest reviews yet.</div>
      <?php endif; ?>

      <?php if (!empty($room['staff_reply'])): ?>
          <div class="reply"><strong>Staff Reply:</strong> <?= htmlspecialchars($room['staff_reply']) ?></div>
      <?php endif; ?>

      <form method="post" class="reply-box">
          <textarea name="reply" rows="3" placeholder="Write a reply..." required></textarea>
          <button type="submit" class="reply-btn">Send Reply</button>
      </form>
  </div>

</div>

<?php include "../views/footer.php"; ?>
</body>
</html>
