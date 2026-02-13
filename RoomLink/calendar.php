<?php
session_start();
require_once "../models/db.php";

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['user_type'])) {
    die("Access denied");
}

if ($_SESSION['user_type'] === 'admin') {
    include "../views/header.php";
} elseif ($_SESSION['user_type'] === 'staff') {
    include "../views/headers.php";
} else {
    die("Unauthorized");
}

$db   = Database::getInstance();
$conn = $db->getConnection();

/* ================= MONTH NAVIGATION ================= */
$year  = isset($_GET['year'])  ? (int)$_GET['year']  : date('Y');
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');

$month = max(1, min(12, $month));

$prevMonth = $month - 1;
$prevYear  = $year;
if ($prevMonth < 1) {
    $prevMonth = 12;
    $prevYear--;
}

$nextMonth = $month + 1;
$nextYear  = $year;
if ($nextMonth > 12) {
    $nextMonth = 1;
    $nextYear++;
}

$firstDayOfMonth = sprintf('%04d-%02d-01', $year, $month);
$daysInMonth     = date('t', strtotime($firstDayOfMonth));
$startDay        = date('w', strtotime($firstDayOfMonth));

/* ================= FETCH BOOKINGS ================= */
$bookings = [];

$sql = "
    SELECT 
        r.checkin,
        r.checkout,
        rooms.name  AS room_name,
        hotels.name AS hotel_name
    FROM reservations r
    JOIN rooms  ON r.room_id = rooms.room_id
    JOIN hotels ON rooms.hotel_id = hotels.hotel_id
    WHERE r.status IN ('active','paid','pending_payment')
    AND (
        r.checkin <= LAST_DAY('$firstDayOfMonth')
        AND r.checkout >= '$firstDayOfMonth'
    )
";

$res = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($res)) {
    $current = strtotime($row['checkin']);
    $end     = strtotime($row['checkout']);

    while ($current <= $end) {
        $day = date('j', $current);
        $bookings[$day][] = [
            'room'  => $row['room_name'],
            'hotel' => $row['hotel_name']
        ];
        $current = strtotime("+1 day", $current);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Booking Calendar</title>
<link rel="stylesheet" href="../roomlink.css">
</head>

<body class="body">
<?php $_SESSION['user_type'] = 'admin'; // OR 'staff' ?>

<div class="calendar">

  <!-- NAVIGATION -->
  <div class="calendar-nav">
    <a href="?month=<?= $prevMonth ?>&year=<?= $prevYear ?>">&laquo;</a>

    <div class="calendar-title">
      <?= date('F Y', strtotime($firstDayOfMonth)) ?>
    </div>

    <a href="?month=<?= $nextMonth ?>&year=<?= $nextYear ?>">&raquo;</a>
  </div>

  <!-- DAYS HEADER -->
  <div class="calendar-header">
    <div>Sun</div>
    <div>Mon</div>
    <div>Tue</div>
    <div>Wed</div>
    <div>Thu</div>
    <div>Fri</div>
    <div>Sat</div>
  </div>

  <!-- CALENDAR GRID -->
  <div class="calendar-grid">

    <!-- EMPTY DAYS -->
    <?php for ($i = 0; $i < $startDay; $i++): ?>
      <div class="day empty"></div>
    <?php endfor; ?>

    <!-- DAYS -->
    <?php for ($day = 1; $day <= $daysInMonth; $day++): ?>
      <div class="day">
        <div class="day-number"><?= $day ?></div>

        <?php if (!empty($bookings[$day])): ?>
          <?php foreach ($bookings[$day] as $b): ?>
            <div class="booking">
              <div class="room"><?= htmlspecialchars($b['room']) ?></div>
              <div class="hotel"><?= htmlspecialchars($b['hotel']) ?></div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>

      </div>
    <?php endfor; ?>

  </div>
</div>

<?php include "../views/footer.php"; ?>
</body>
</html>
