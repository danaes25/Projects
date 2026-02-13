<?php
require_once "../models/Hotel.php";
require_once "../models/db.php";
$db = Database::getInstance();
$conn = $db->getConnection();

require_once "auth.php";

checkPageAccess('addhotel.php');


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {

    /* ================= BASIC VALIDATION ================= */

    $name        = trim($_POST['name']);
    $city        = trim($_POST['city']);
    $country     = trim($_POST['country']);
    $description = trim($_POST['description']);

    if ($name === '' || $city === '' || $country === '' || $description === '') {
        die("All fields are required.");
    }

    if (!isset($_FILES['picture']) || $_FILES['picture']['error'] !== 0) {
        die("Hotel image is required.");
    }

    /* ================= DUPLICATE CITY CHECK ================= */

    $cityCheck = mysqli_prepare($conn, "SELECT hotel_id FROM hotels WHERE city = ?");
    mysqli_stmt_bind_param($cityCheck, "s", $city);
    mysqli_stmt_execute($cityCheck);
    mysqli_stmt_store_result($cityCheck);

    if (mysqli_stmt_num_rows($cityCheck) > 0) {
        die("A hotel already exists in this city.");
    }

    /* ================= IMAGE VALIDATION ================= */

    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
    $imageType = mime_content_type($_FILES['picture']['tmp_name']);

    if (!in_array($imageType, $allowedTypes)) {
        die("Only JPG, PNG, or WEBP images are allowed.");
    }

    /* ================= IMAGE UPLOAD ================= */

    $uploadDir = "../uploads/hotels/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = time() . "_" . basename($_FILES['picture']['name']);
    $target   = $uploadDir . $fileName;

    // Web path saved in DB
    $dbPath = "uploads/hotels/" . $fileName;

    /* ================= DUPLICATE IMAGE CHECK ================= */

    $imgCheck = mysqli_prepare($conn, "SELECT hotel_id FROM hotels WHERE picture = ?");
    mysqli_stmt_bind_param($imgCheck, "s", $dbPath);
    mysqli_stmt_execute($imgCheck);
    mysqli_stmt_store_result($imgCheck);

    if (mysqli_stmt_num_rows($imgCheck) > 0) {
        die("This image is already used by another hotel.");
    }

    /* ================= MOVE + INSERT ================= */

    if (!move_uploaded_file($_FILES['picture']['tmp_name'], $target)) {
        die("Image upload failed.");
    }

    $hotel = new Hotel();
$hotel->create($name, $city, $country, $description, $dbPath);

header("Location: ../views/adminHomepage.php");
exit;


} else {
    header("Location: ../views/adminHomepage.php");
    exit;
}
