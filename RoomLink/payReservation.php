<?php
session_start();
require_once '../models/db.php';
require_once 'Auth.php';
checkPageAccess('payReservation.php');

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD']!=='POST' || !isset($_POST['pay'])) {
    header("Location: history.php");
    exit;
}

$userId = $_SESSION['user_id'];
$reservationId = intval($_POST['reservation_id']);
$cardNumber = preg_replace('/\s+/', '', $_POST['card_number']);
$expiry     = $_POST['expiry'];
$cvv        = $_POST['cvv'];
$phone      = preg_replace('/\s+/', '', $_POST['phone']);

// Validation
if (!preg_match('/^[0-9]{16,19}$/', $cardNumber)) die("Invalid card number");
if (!preg_match('/^(0[1-9]|1[0-2])\/[0-9]{2}$/', $expiry)) die("Invalid expiry");
if (!preg_match('/^[0-9]{3,4}$/', $cvv)) die("Invalid CVV");
if (preg_match('/^0[0-9]{10}$/', $phone)) $phone = '+20'.substr($phone,1);
elseif (!preg_match('/^\+[0-9]{8,15}$/', $phone)) die("Invalid phone");

$db = Database::getInstance();
$conn = $db->getConnection();

// Verify reservation
$res = mysqli_query($conn, "SELECT * FROM reservations WHERE reservation_id=$reservationId AND user_id=$userId AND status!='paid'");
if (mysqli_num_rows($res)===0) die("Invalid reservation.");

// Update status to paid
$update = mysqli_prepare($conn,"UPDATE reservations SET status='paid' WHERE reservation_id=?");
mysqli_stmt_bind_param($update,"i",$reservationId);
mysqli_stmt_execute($update);

header("Location: history.php?payment=success");
exit;
?>
