<?php
session_start();
include "../models/db.php";
$db = Database::getInstance();
$conn = $db->getConnection();
require_once "../controllers/Auth.php";
checkPageAccess('housekeeper.php');

$today = date('Y-m-d');
$soon = date('Y-m-d', strtotime('+7 days')); // Optional: show next week arrivals

// Fetch rooms that need housekeeping
$res = mysqli_query($conn, "
    SELECT r.*, rooms.name, rooms.photo, rooms.housekeeping_status
    FROM reservations r
    JOIN rooms ON r.room_id = rooms.room_id
    WHERE r.user_id IS NOT NULL
    AND r.status='active'
    AND rooms.housekeeping_status='needed'
    AND r.checkin <= '$today'
    AND r.checkout >= '$today'
    ORDER BY r.checkin ASC
");
?>

<!DOCTYPE html>
<html>
<head>
  <title>HouseKeeper</title>
  <link rel="stylesheet" href="../roomlink.css">
</head>
<body>

<?php include "../views/headers.php"; ?>

<h2 class="headtitlee"><b>Rooms in Need of Housekeeper</b></h2>

<?php if(mysqli_num_rows($res) == 0): ?>
    <p style="text-align:center; margin-top:50px;">No rooms currently need housekeeping.</p>
<?php else: ?>
<figure class="imgg">
<?php while ($r = mysqli_fetch_assoc($res)): ?>
  <div class="img">
    <a href="../controllers/staffroom.php?room_id=<?= $r['room_id'] ?>" class="imglink">
      <img src="../<?= htmlspecialchars($r['photo']) ?>" alt="<?= htmlspecialchars($r['name']) ?>">
      <figcaption><b><?= htmlspecialchars($r['name']) ?></b></figcaption>
      <figcaption>Check-in: <?= date("d M Y", strtotime($r['checkin'])) ?></figcaption>
      <figcaption>Check-out: <?= date("d M Y", strtotime($r['checkout'])) ?></figcaption>
      <figcaption class="HK"><b>Housekeeper Requested</b></figcaption>
      <br>
      <button class="buttonc">View</button>
    </a>
  </div>
<?php endwhile; ?>
</figure>
<?php endif; ?>

<?php include "../views/footer.php"; ?>
</body>
</html>
