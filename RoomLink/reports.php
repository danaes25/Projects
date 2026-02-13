<?php
session_start();
include "../models/db.php";

$db = Database::getInstance();
$conn = $db->getConnection();

/* ================= HEADER BY ROLE ================= */
$headerFile = "../views/headerg.php";

if (isset($_SESSION['user_type'])) {
    if ($_SESSION['user_type'] === 'admin') {
        $headerFile = "../views/header.php";
    } elseif ($_SESSION['user_type'] === 'staff') {
        $headerFile = "../views/headers.php";
    }
}
include $headerFile;

/* ================= MONTH FILTER ================= */
$month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');

/* ================= CONVERSION RATE ================= */
$egpToUsd = 0.032; // 1 EGP = 0.032 USD

/* ================= TOTAL BOOKINGS ================= */
$qBookings = mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM reservations
    WHERE DATE_FORMAT(checkin, '%Y-%m') = '$month'
");
$totalBookings = mysqli_fetch_assoc($qBookings)['total'] ?? 0;

/* ================= TOTAL REVENUE (CALCULATED) ================= */
$qRevenue = mysqli_query($conn, "
    SELECT SUM(DATEDIFF(r.checkout, r.checkin) * rm.price) AS revenue
    FROM reservations r
    JOIN rooms rm ON r.room_id = rm.room_id
    WHERE DATE_FORMAT(r.checkin, '%Y-%m') = '$month'
      AND r.status IN ('active','completed')
");
$totalRevenueEgp = mysqli_fetch_assoc($qRevenue)['revenue'] ?? 0;
$totalRevenueUsd = $totalRevenueEgp * $egpToUsd;

/* ================= BOOKINGS BY HOTEL ================= */
$qHotels = mysqli_query($conn, "
    SELECT h.name AS hotel_name,
           COUNT(r.reservation_id) AS bookings,
           SUM(DATEDIFF(r.checkout, r.checkin) * rm.price) AS revenue
    FROM reservations r
    JOIN rooms rm ON r.room_id = rm.room_id
    JOIN hotels h ON rm.hotel_id = h.hotel_id
    WHERE DATE_FORMAT(r.checkin, '%Y-%m') = '$month'
    GROUP BY h.hotel_id
    ORDER BY bookings DESC
");

/* ================= DAILY BOOKINGS ================= */
$qDaily = mysqli_query($conn, "
    SELECT DATE(checkin) AS day,
           COUNT(*) AS total
    FROM reservations
    WHERE DATE_FORMAT(checkin, '%Y-%m') = '$month'
    GROUP BY day
    ORDER BY day
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Reports</title>
<link rel="stylesheet" href="../roomlink.css">
</head>

<body class="body">

<h1 class="headtitlee"><b>Booking & Revenue Report</b></h1>

<!-- MONTH FILTER -->
<form method="get" style="text-align:center;">
    <input class="filterrr" type="month" name="month" value="<?= $month ?>">
    <button class="buttonc">Filter</button>
</form>

<!-- SUMMARY -->
<div class="report-box report-grid">
    <div class="stat">
        <h2>$<?= number_format($totalRevenueUsd, 2) ?></h2>
        <p>Total Revenue (USD)</p>
    </div>

    <div class="stat">
        <h2><?= $totalBookings ?></h2>
        <p>Total Bookings</p>
    </div>
</div>

<!-- BOOKINGS BY HOTEL -->
<div class="report-box">
    <h2>Bookings & Revenue by Hotel</h2>
    <table>
        <tr>
            <th>Hotel</th>
            <th>Bookings</th>
            <th>Revenue (USD)</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($qHotels)): ?>
        <tr>
            <td><?= htmlspecialchars($row['hotel_name']) ?></td>
            <td><?= $row['bookings'] ?></td>
            <td>$<?= number_format($row['revenue'] * $egpToUsd, 2) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<!-- DAILY BOOKINGS -->
<div class="report-box">
    <h2>Daily Booking Trend</h2>
    <table>
        <tr>
            <th>Date</th>
            <th>Bookings</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($qDaily)): ?>
        <tr>
            <td><?= $row['day'] ?></td>
            <td><?= $row['total'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include "../views/footer.php"; ?>
</body>
</html>
