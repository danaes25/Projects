<?php
require_once '../models/db.php';
require_once '../controllers/auth.php';

checkPageAccess('staffHomepage.php');

$conn = Database::getInstance()->getConnection();
$today = date('Y-m-d');
$soon  = date('Y-m-d', strtotime('+3 days'));
?>

<!DOCTYPE html>
<html>
<head>
  <title>Staff Homepage</title>
  <link rel="stylesheet" href="../roomlink.css">
</head>
<body>

<?php include "headers.php"; ?>

<!-- ================= CHECK-IN SOON ================= -->
<h2 class="headtitlee"><b>Check-in Soon</b></h2>

<figure class="imgg">
<?php
$res = mysqli_query($conn, "
    SELECT r.*, rooms.name, rooms.photo
    FROM reservations r
    JOIN rooms ON r.room_id = rooms.room_id
    WHERE r.checkin BETWEEN '$today' AND '$soon'
    AND r.status = 'active'
");

while ($r = mysqli_fetch_assoc($res)):
?>
  <div class="img">
    <a href="../controllers/staffroom.php?room_id=<?= $r['room_id'] ?>" class="imglink">
      <img src="../<?= htmlspecialchars($r['photo']) ?>" alt="<?= htmlspecialchars($r['name']) ?>">
      <figcaption><b><?= htmlspecialchars($r['name']) ?></b></figcaption>
      <figcaption>Arrival: <?= date("d M Y", strtotime($r['checkin'])) ?></figcaption>
      <br>
      <button class="buttonc">View</button>
    </a>
  </div>
<?php endwhile; ?>
</figure>

<!-- ================= CHECKOUT SOON ================= -->
<h2 class="headtitlee"><b>Checkout Soon</b></h2>

<figure class="imgg">
<?php
$res = mysqli_query($conn, "
    SELECT r.*, rooms.name, rooms.photo
    FROM reservations r
    JOIN rooms ON r.room_id = rooms.room_id
    WHERE r.checkout BETWEEN '$today' AND '$soon'
    AND r.status = 'active'
");

while ($r = mysqli_fetch_assoc($res)):
?>
  <div class="img">
    <a href="../controllers/staffroom.php?room_id=<?= $r['room_id'] ?>" class="imglink">
      <img src="../<?= htmlspecialchars($r['photo']) ?>" alt="<?= htmlspecialchars($r['name']) ?>">
      <figcaption><b><?= htmlspecialchars($r['name']) ?></b></figcaption>
      <figcaption>Departure: <?= date("d M Y", strtotime($r['checkout'])) ?></figcaption>
      <br>
      <button class="buttonc">View</button>
    </a>
  </div>
<?php endwhile; ?>
</figure>

<!-- ================= MODIFIED ================= -->
<h2 class="headtitlee"><b>Modified</b></h2>

<figure class="imgg">
<?php
$res = mysqli_query($conn, "
    SELECT r.*, rooms.name, rooms.photo
    FROM reservations r
    JOIN rooms ON r.room_id = rooms.room_id
    WHERE r.status = 'modified'
");

while ($r = mysqli_fetch_assoc($res)):
?>
  <div class="img">
    <a href="../controllers/staffroom.php?room_id=<?= $r['room_id'] ?>" class="imglink">
      <img src="../<?= htmlspecialchars($r['photo']) ?>" alt="<?= htmlspecialchars($r['name']) ?>">
      <figcaption><b><?= htmlspecialchars($r['name']) ?></b></figcaption>
      <figcaption style="color:orange;"><b>Modified</b></figcaption>
      <br>
      <button class="buttonc">View</button>
    </a>
  </div>
<?php endwhile; ?>
</figure>

<!-- ================= CANCELED ================= -->
<h2 class="headtitlee"><b>Canceled</b></h2>

<figure class="imgg">
<?php
$res = mysqli_query($conn, "
    SELECT r.*, rooms.name, rooms.photo
    FROM reservations r
    JOIN rooms ON r.room_id = rooms.room_id
    WHERE r.status = 'canceled'
");

while ($r = mysqli_fetch_assoc($res)):
?>
  <div class="img">
    <a href="../controllers/staffroom.php?room_id=<?= $r['room_id'] ?>" class="imglink">
      <img src="../<?= htmlspecialchars($r['photo']) ?>" alt="<?= htmlspecialchars($r['name']) ?>">
      <figcaption><b><?= htmlspecialchars($r['name']) ?></b></figcaption>
      <figcaption style="color:red;"><b>Canceled</b></figcaption>
      <br>
      <button class="buttonc">View</button>
    </a>
  </div>
<?php endwhile; ?>
</figure>

<?php include "footer.php"; ?>
</body>
</html>
