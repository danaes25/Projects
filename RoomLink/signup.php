<?php
include "../models/db.php";
require_once "../models/UserFactory.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fname    = $_POST['fname'];
    $lname    = $_POST['lname'];
    $email    = $_POST['email'];
    $rawPass  = $_POST['password'];
    $confirm  = $_POST['conifrim_password'];

    $hasError = false;

    /* ================= VALIDATION ================= */

    if (strlen($rawPass) < 8) {
        echo "<p style='color:red; text-align:center;'>Password must be at least 8 characters long.</p>";
        $hasError = true;
    }

    if (!preg_match('/\d/', $rawPass)) {
        echo "<p style='color:red; text-align:center;'>Password must contain at least one number.</p>";
        $hasError = true;
    }

    if (!preg_match('/[^A-Za-z0-9]/', $rawPass)) {
        echo "<p style='color:red; text-align:center;'>Password must contain at least one special character.</p>";
        $hasError = true;
    }

    if ($password !== $confirm) {
        echo "<p style='color:red; text-align:center;'>passwords do not matchs</p>";
        $hasError = true;
    }
    // STOP if validation failed
    if ($hasError) {
        exit;
    }

    /* ================= DATABASE ================= */

    $password = password_hash($rawPass, PASSWORD_DEFAULT);
    $db = Database::getInstance()->getConnection();

    // Check if email already exists
    $check = mysqli_query($db, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        echo "<p style='color:red; text-align:center;'>This email is already registered. Please use another.</p>";
        exit;
    }

    $sql = "INSERT INTO users (user_type, first_name, last_name, email, password)
            VALUES ('guest', '$fname', '$lname', '$email', '$password')";

    if (mysqli_query($db, $sql)) {

        $userId = mysqli_insert_id($db);
        $result = mysqli_query($db, "SELECT * FROM users WHERE user_id = $userId");
        $userData = mysqli_fetch_assoc($result);

        $user = UserFactory::createUser($userData);

        session_start();
        $_SESSION['user_id']   = $user->getUserId();
        $_SESSION['user_type'] = $user->getUserType();

        header("Location: " . $user->getHomepage());
        exit();

    } else {
        echo "<p style='color:red; text-align:center;'>Error: " . mysqli_error($db) . "</p>";
    }
}
?>
