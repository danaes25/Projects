<?php
session_start();
require_once "../models/db.php";
require_once "../controllers/Auth.php";

checkPageAccess('search.php');



include $headerFile;

$db = Database::getInstance();
$conn = $db->getConnection();


$headerFile = "../views/headerg.php";

if (isset($_SESSION['user_type'])) {
    if ($_SESSION['user_type'] === 'admin') {
        $headerFile = "../views/header.php";
    } elseif ($_SESSION['user_type'] === 'staff') {
        $headerFile = "../views/headers.php";
    }
}

/* ================= GET SEARCH INPUT ================= */
$destination = trim($_GET['destination'] ?? '');
$checkin     = $_GET['checkin'] ?? '';
$checkout    = $_GET['checkout'] ?? '';
$adults      = max(1, (int)($_GET['adults'] ?? 1));
$children    = max(0, (int)($_GET['children'] ?? 0));
$roomsCount  = max(1, (int)($_GET['rooms'] ?? 1));

if (!$destination || !$checkin || !$checkout) {
    die("Missing search data");
}

/* ================= SEARCH QUERY ================= */
/*
  1️⃣ Match hotel by city / country / name
  2️⃣ Exclude rooms already booked in selected dates
*/

$sql = "
SELECT 
    rooms.*, 
    hotels.name AS hotel_name,
    hotels.city,
    hotels.country
FROM rooms
JOIN hotels ON rooms.hotel_id = hotels.hotel_id
WHERE
(
    hotels.city LIKE ?
    OR hotels.country LIKE ?
    OR hotels.name LIKE ?
)
AND rooms.room_id NOT IN (
    SELECT room_id FROM reservations
    WHERE status != 'canceled'
    AND (
        checkin < ?
        AND checkout > ?
    )
)
";

$stmt = $conn->prepare($sql);

$like = "%$destination%";
$stmt->bind_param(
    "sssss",
    $like,
    $like,
    $like,
    $checkout,
    $checkin
);

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Search Results</title>
  <link rel="stylesheet" href="../roomlink.css">
</head>
<body class="body">

<?php $_SESSION['user_type'] = $user['user_type']; ?>

<h1 class="headtitlee">
  <b>Available Rooms in “<?= htmlspecialchars($destination) ?>”</b>
</h1>

<p style="text-align:center;">
  <?= date("d M Y", strtotime($checkin)) ?> →
  <?= date("d M Y", strtotime($checkout)) ?>
</p>

<figure class="imgg">
<?php if ($result->num_rows > 0): ?>
    <?php while ($room = $result->fetch_assoc()): ?>
        <div class="img">
            <a class="imglink" href="../controllers/guestroom.php?room_id=<?= $room['room_id'] ?>">
                <img src="../<?= htmlspecialchars($room['photo']) ?>" alt="<?= htmlspecialchars($room['name']) ?>">

                <figcaption>
                    <b><?= htmlspecialchars($room['name']) ?></b>
                </figcaption>

                <figcaption style="font-size:14px; color:#555;">
                    <?= htmlspecialchars($room['hotel_name']) ?> <br>
                    <?= htmlspecialchars($room['city']) ?>, <?= htmlspecialchars($room['country']) ?>
                </figcaption>

                <br>
                <button class="buttonc">View</button>
            </a>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p style="text-align:center; font-size:18px; color:red;">
        No available rooms found.
    </p>
<?php endif; ?>
</figure>

<?php include "../views/footer.php"; ?>
</body>
</html>
