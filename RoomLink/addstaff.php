<?php
session_start();
include "../models/db.php";
require_once 'auth.php';
checkPageAccess('addstaff.php'); // only admin can access

$db = Database::getInstance();
$conn = $db->getConnection();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    die("Access denied");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_staff'])) {

    $firstName = mysqli_real_escape_string($conn, $_POST['first_name']);
    $lastName  = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email     = mysqli_real_escape_string($conn, $_POST['email']);
    $password  = $_POST['password'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $profilePicPath = 'uploads/Unknounk pfp.jpg';

    // handle profile pic upload
    if (isset($_FILES['user_profilepic']) && $_FILES['user_profilepic']['error'] === 0) {
        $allowed = ['jpg','jpeg','png','webp'];
        $ext = strtolower(pathinfo($_FILES['user_profilepic']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $newName = "pfp_" . time() . "_" . rand(1000,9999) . "." . $ext;
            $uploadDir = "../uploads/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            if (move_uploaded_file($_FILES['user_profilepic']['tmp_name'], $uploadDir . $newName)) {
                $profilePicPath = "uploads/" . $newName;
            }
        }
    }

    // check if email already exists
    $checkEmail = mysqli_query($conn, "SELECT user_id FROM users WHERE email='$email'");
    if (mysqli_num_rows($checkEmail) > 0) {
        echo "<script>alert('Email already exists'); window.history.back();</script>";
        exit;
    }

    // insert staff user
    $insert = mysqli_query($conn, "
        INSERT INTO users (first_name, last_name, email, password, user_type, user_profilepic)
        VALUES ('$firstName', '$lastName', '$email', '$hashedPassword', 'staff', '$profilePicPath')
    ");

    if ($insert) {
        echo "<script>alert('Staff added successfully'); window.location='../controllers/adminprofile.php';</script>";
    } else {
        echo "<script>alert('Error adding staff'); window.history.back();</script>";
    }
} else {
    die("Invalid request");
}
?>
