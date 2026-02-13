<?php
session_start();
require_once "../models/db.php";
require_once '../controllers/Auth.php';
checkPageAccess('history.php');

if (!isset($_SESSION['user_id'])) die("Login required");
$userId = $_SESSION['user_id'];
$today  = date('Y-m-d');
$conn = Database::getInstance()->getConnection();

// Cancel reservation
if (isset($_GET['cancel'])) {
    mysqli_query($conn, "
        UPDATE reservations
        SET status='canceled'
        WHERE reservation_id=" . intval($_GET['cancel']) . "
        AND user_id=$userId
    ");
}

// Modify reservation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['modify'])) {
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $reservationId = intval($_POST['reservation_id']);
    $roomId = intval($_POST['room_id']);

    // Check for overlapping reservations
    $overlapQuery = "
        SELECT * FROM reservations
        WHERE room_id = $roomId
        AND reservation_id != $reservationId
        AND status = 'active'
        AND (checkin <= '$checkout' AND checkout >= '$checkin')
    ";
    $overlapResult = mysqli_query($conn, $overlapQuery);

    if (mysqli_num_rows($overlapResult) > 0) {
        echo "<script>alert('Cannot modify reservation: dates overlap with another booking.');</script>";
    } else {
        mysqli_query($conn, "
            UPDATE reservations
            SET checkin='$checkin', checkout='$checkout', status='modified'
            WHERE reservation_id=$reservationId AND user_id=$userId
        ");
    }
}

// Add comment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])) {
    $reservationId = intval($_POST['reservation_id']);
    $roomId = intval($_POST['room_id']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    mysqli_query($conn, "
        INSERT INTO reviews (reservation_id, user_id, room_id, comment)
        VALUES ($reservationId, $userId, $roomId, '$comment')
    ");
}

// Request Housekeeper
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['housekeeper_request'])) {
    $roomId = intval($_POST['room_id']);
    $reservationId = intval($_POST['reservation_id']);
    mysqli_query($conn, "UPDATE rooms SET housekeeping_status='needed' WHERE room_id=$roomId");
    mysqli_query($conn, "UPDATE reservations SET status='paid' WHERE reservation_id=$reservationId");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>History</title>
    <link rel="stylesheet" href="../roomlink.css">
    <style>
        .housekeeper-btn { background-color: #1d1f4e; color:white; border:none; padding:8px 12px; border-radius:5px; cursor:pointer;}
        .housekeeper-btn.needed { background-color: red; }
        .popup { position: fixed; top:0; left:0; width:100%; height:100%; display:none; justify-content:center; align-items:center; background: rgba(0,0,0,0.7); z-index:1000; }
        .popup:target { display:flex; }
        .popup-box { background:white; padding:20px; border-radius:10px; width:400px; max-width:90%; position:relative; }
        .popup-box .close-popup { position:absolute; top:10px; right:15px; text-decoration:none; font-size:24px; color:black; }
    </style>
</head>
<body>
<?php include "../views/headerg.php"; ?>

<h1 class="headtitlee"><b>History</b></h1>

<!-- ================= IN PROGRESS ================= -->
<h2 class="headtitlee"><b>In Progress</b></h2>
<figure class="imgg">
<?php
$res = mysqli_query($conn, "
    SELECT r.*, rooms.name, rooms.photo, rooms.housekeeping_status
    FROM reservations r
    JOIN rooms ON r.room_id = rooms.room_id
    WHERE r.user_id = $userId
    AND r.checkout >= '$today'
    AND r.status IN ('active','pending_payment','modified')
");

while ($r = mysqli_fetch_assoc($res)):
    $checkin = $r['checkin'];
    $checkout = $r['checkout'];
    $showHousekeeperBtn = ($today >= $checkin && $today < $checkout);
    $btnClass = ($r['housekeeping_status'] === 'needed') ? 'housekeeper-btn needed' : 'housekeeper-btn';
    $showPayNow = ($r['status'] !== 'paid' && $today < $checkin);
?>
<div class="img">
    <img src="../<?= htmlspecialchars($r['photo']) ?>" alt="<?= htmlspecialchars($r['name']) ?>">
    <figcaption><b><?= htmlspecialchars($r['name']) ?></b></figcaption>
    <figcaption>Check-in: <?= date("d M Y", strtotime($checkin)) ?></figcaption>
    <figcaption>Check-out: <?= date("d M Y", strtotime($checkout)) ?></figcaption>
    <figcaption>Status: 
        <?php if ($r['status'] === 'paid'): ?>
            <span style="color: green; font-weight: bold;">Paid</span>
        <?php elseif ($showPayNow): ?>
            <span style="color: orange; font-weight: bold;">Pending Payment</span>
        <?php else: ?>
            <span style="color: gray; font-weight: bold;">In Progress</span>
        <?php endif; ?>
    </figcaption>

    <br>
    <button class="modifybutt" onclick="document.getElementById('m<?= $r['reservation_id'] ?>').style.display='flex'">Modify</button>
    <a href="?cancel=<?= $r['reservation_id'] ?>" class="cancelbutt">Cancel</a>

    <?php if ($showPayNow): ?>
        <br><br>
        <a href="#paymentPopup<?= $r['reservation_id'] ?>" class="pay-btn">Pay Now</a>
        <div id="paymentPopup<?= $r['reservation_id'] ?>" class="popup">
            <div class="popup-box">
                <a href="#" class="close-popup">&times;</a>
                <form method="post" action="../controllers/payReservation.php">
                    <input type="hidden" name="reservation_id" value="<?= $r['reservation_id'] ?>">
                    <label>Card Number</label>
                    <input type="text" name="card_number" required pattern="[0-9 ]{16,19}" minlength="16" maxlength="19">
                    <label>Expiry (MM/YY)</label>
                    <input type="text" name="expiry" required pattern="(0[1-9]|1[0-2])\/[0-9]{2}">
                    <label>CVV</label>
                    <input type="password" name="cvv" required pattern="[0-9]{3,4}" minlength="3" maxlength="4">
                    <label>Phone Number</label>
                    <input type="tel" name="phone" required>
                    <button type="submit" class="pay-btn">Confirm Payment</button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($showHousekeeperBtn): ?>
        <form method="post" style="margin-top:10px;">
            <input type="hidden" name="housekeeper_request" value="1">
            <input type="hidden" name="room_id" value="<?= $r['room_id'] ?>">
            <input type="hidden" name="reservation_id" value="<?= $r['reservation_id'] ?>">
            <button type="submit" class="<?= $btnClass ?>">Request Housekeeper</button>
        </form>
    <?php endif; ?>
</div>

<!-- Modify Popup -->
<form class="popup" method="post" id="m<?= $r['reservation_id'] ?>">
    <div class="popup-box">
        <span class="close-popup" onclick="this.closest('.popup').style.display='none'">&times;</span>
        <input type="hidden" name="modify">
        <input type="hidden" name="reservation_id" value="<?= $r['reservation_id'] ?>">
        <input type="hidden" name="room_id" value="<?= $r['room_id'] ?>">
        <label>Check-in</label>
        <input type="date" name="checkin" value="<?= $checkin ?>" required>
        <label>Check-out</label>
        <input type="date" name="checkout" value="<?= $checkout ?>" required>
        <button class="buttonc">Save</button>
    </div>
</form>
<?php endwhile; ?>
</figure>

<!-- ================= COMPLETE ================= -->
<h2 class="headtitlee"><b>Complete</b></h2>
<figure class="imgg">
<?php
$res = mysqli_query($conn, "
    SELECT r.*, rooms.name, rooms.photo
    FROM reservations r
    JOIN rooms ON r.room_id = rooms.room_id
    WHERE r.user_id = $userId
    AND (r.status='paid' OR r.checkout < '$today')
    AND r.status != 'canceled'
");

while ($r = mysqli_fetch_assoc($res)):
?>
<div class="img">
    <img src="../<?= htmlspecialchars($r['photo']) ?>" alt="<?= htmlspecialchars($r['name']) ?>">
    <figcaption><b><?= htmlspecialchars($r['name']) ?></b></figcaption>
    <figcaption>Completed</figcaption>
    <figcaption>Status: <span style="color: green; font-weight:bold;">Paid</span></figcaption>
    <form method="post">
        <input type="hidden" name="comment">
        <input type="hidden" name="reservation_id" value="<?= $r['reservation_id'] ?>">
        <input type="hidden" name="room_id" value="<?= $r['room_id'] ?>">
        <textarea name="comment" placeholder="Write your review" required></textarea>
        <button class="commentbutt">Add Comment</button>
    </form>
</div>
<?php endwhile; ?>
</figure>

<?php include "../views/footer.php"; ?>
</body>
</html>
