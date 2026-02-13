<?php
include "../models/db.php";
require_once "auth.php";

checkPageAccess('addroom.php');

$db   = Database::getInstance();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_room'])) {

    /* ================= BASIC VALIDATION ================= */

    $name        = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price       = trim($_POST['price']);
    $hotel_id    = intval($_POST['hotel_id']);

    if ($name === '' || $description === '' || $price === '' || $hotel_id <= 0) {
        die("All fields are required.");
    }

    if (!is_numeric($price) || $price <= 0) {
        die("Invalid room price.");
    }

    if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== 0) {
        die("Room image is required.");
    }

    /* ================= DUPLICATE ROOM NAME (SAME HOTEL) ================= */

    $nameCheck = mysqli_prepare(
        $conn,
        "SELECT room_id FROM rooms WHERE hotel_id = ? AND name = ?"
    );
    mysqli_stmt_bind_param($nameCheck, "is", $hotel_id, $name);
    mysqli_stmt_execute($nameCheck);
    mysqli_stmt_store_result($nameCheck);

    if (mysqli_stmt_num_rows($nameCheck) > 0) {
        die("A room with this name already exists in this hotel.");
    }

    /* ================= IMAGE VALIDATION ================= */

    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
    $imageType = mime_content_type($_FILES['photo']['tmp_name']);

    if (!in_array($imageType, $allowedTypes)) {
        die("Only JPG, PNG, or WEBP images are allowed.");
    }

    /* ================= IMAGE UPLOAD ================= */

    $uploadDir = "../uploads/rooms/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = time() . "_" . basename($_FILES['photo']['name']);
    $target   = $uploadDir . $fileName;

    // Web path saved in DB
    $dbPath = "uploads/rooms/" . $fileName;

    /* ================= DUPLICATE IMAGE CHECK ================= */

    $imgCheck = mysqli_prepare(
        $conn,
        "SELECT room_id FROM rooms WHERE photo = ?"
    );
    mysqli_stmt_bind_param($imgCheck, "s", $dbPath);
    mysqli_stmt_execute($imgCheck);
    mysqli_stmt_store_result($imgCheck);

    if (mysqli_stmt_num_rows($imgCheck) > 0) {
        die("This image is already used by another room.");
    }

    /* ================= MOVE FILE + INSERT ================= */

    if (!move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
        die("Room image upload failed.");
    }

    $stmt = mysqli_prepare($conn,
        "INSERT INTO rooms 
        (hotel_id, name, description, photo, price, housekeeping_status)
        VALUES (?, ?, ?, ?, ?, 'available')"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "isssd",
        $hotel_id,
        $name,
        $description,
        $dbPath,
        $price
    );

    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../views/adminHomepage.php");
        exit;
    } else {
        die("Database error. Please try again.");
    }

} else {
    header("Location: ../views/adminHomepage.php");
    exit;
}
