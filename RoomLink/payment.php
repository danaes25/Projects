<?php
session_start();
require_once '../models/db.php';
require_once '../controllers/Auth.php';
checkPageAccess('payment.php');

if (!isset($_SESSION['user_id'])) {
    die("Login required");
}

if (!isset($_GET['id'])) {
    die("Invalid reservation.");
}

$reservationId = intval($_GET['id']);
$userId = $_SESSION['user_id'];

$db = Database::getInstance();
$conn = $db->getConnection();

// Make sure reservation belongs to user and is unpaid
$res = mysqli_query($conn, "
    SELECT r.*, rooms.name
    FROM reservations r
    JOIN rooms ON r.room_id = rooms.room_id
    WHERE r.reservation_id = $reservationId
    AND r.user_id = $userId
    AND r.status IN ('pending_payment', 'active')

");

if (mysqli_num_rows($res) === 0) {
    die("Reservation not found or already paid.");
}

$reservation = mysqli_fetch_assoc($res);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment</title>
    <link rel="stylesheet" href="/Roomlink/roomlink.css">

</head>
<body>

<form method="post" action="../controllers/payReservation.php" class="payment-form">

    <input type="hidden" name="reservation_id" value="<?= $reservationId ?>">

    <!-- Card Number -->
    <label>Card Number</label>
    <input
        type="text"
        name="card_number"
        placeholder="1234 5678 9012 3456"
        required
        pattern="[0-9 ]{16,19}"
        minlength="16"
        maxlength="19"
        title="Card number must be 16 to 19 digits"
    >

    <!-- Expiry + CVV -->
    <div class="payment-row">
        <div>
            <label>Expiry (MM/YY)</label>
            <input
                type="text"
                name="expiry"
                placeholder="MM/YY"
                required
                pattern="(0[1-9]|1[0-2])\/[0-9]{2}"
                title="Expiry date must be in MM/YY format"
            >
        </div>

        <div>
            <label>CVV</label>
            <input
                type="password"
                name="cvv"
                placeholder="123"
                required
                pattern="[0-9]{3,4}"
                minlength="3"
                maxlength="4"
                title="CVV must be 3 or 4 digits"
            >
        </div>
    </div>

    <!-- Phone Number -->
    <label>Phone Number</label>
    <input
        type="tel"
        name="phone"
        placeholder="Phone number (e.g. 01234567890 or +44123456789)"
    required
    >

    <button type="submit" name="pay" class="pay-btn">
        Confirm Payment
    </button>
</form>

</body>
</html>

