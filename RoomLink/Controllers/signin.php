<?php
session_start();
require_once "../models/db.php";
require_once "../models/UserFactory.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $db = Database::getInstance()->getConnection();
    $stmt = mysqli_prepare($db, "SELECT * FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $userData = mysqli_fetch_assoc($result);

    if ($userData && password_verify($password, $userData['password'])) {
        // ✅ Use Factory Pattern
        $user = UserFactory::createUser($userData);

        $_SESSION['user_id'] = $user->getUserId();
        $_SESSION['user_type'] = $user->getUserType();

        // Redirect to the correct homepage
        header("Location: " . $user->getHomepage());
        exit();
    } else {
        echo "<p style='color:red; text-align:center;'>Invalid email or password</p>";
    }
}
?>
