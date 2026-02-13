<?php
session_start();
require_once '../models/booking.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php?error=" . urlencode("Please login to make a booking"));
    exit();
}

$booking = new Booking();

try {
    $userId   = $_SESSION['user_id'];
    $roomId   = $_POST['room_id'] ?? null;
    $checkIn  = $_POST['check_in'] ?? null;
    $checkOut = $_POST['check_out'] ?? null;

    if (!$roomId || !$checkIn || !$checkOut) {
        throw new Exception("All fields are required.");
    }

    // Create booking (backend validation inside create)
    $booking->create($userId, $roomId, $checkIn, $checkOut);

    // Success
    header("Location: ../views/guestHomepage.php?success=" . urlencode("Reservation successful!"));
    exit();

} catch (Exception $e) {
    // Error
    header("Location: ../views/guestHomepage.php?error=" . urlencode($e->getMessage()));
    exit();
}