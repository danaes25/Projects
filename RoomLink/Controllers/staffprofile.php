<?php
session_start();
include "../models/db.php";
$db = Database::getInstance();
$conn = $db->getConnection();

if (!isset($_SESSION['user_id'])) {
    die("<h3>Please login</h3>");
}

$userId = $_SESSION['user_id'];

/* ================= UPLOAD PROFILE PIC ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic'])) {
    $file = $_FILES['profile_pic'];

    if ($file['error'] === 0) {
        $allowed = ['jpg','jpeg','png','webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $newName = "pfp_" . $userId . "_" . time() . "." . $ext;
            $path = "uploads/" . $newName;

            if (move_uploaded_file($file['tmp_name'], "../" . $path)) {
                mysqli_query($conn, "
                    UPDATE users 
                    SET user_profilepic='$path' 
                    WHERE user_id=$userId
                ");
            }
        }
    }

    header("Location: favorite.php");
    exit;
}

/* ================= FETCH USER INFO ================= */
$userRes = mysqli_query($conn, "
    SELECT first_name, last_name, email, user_type, user_profilepic, rooms_favorite 
    FROM users WHERE user_id=$userId
");
$userRow = mysqli_fetch_assoc($userRes);

$profilePic = $userRow['user_profilepic'] ?: 'uploads/Unknounk pfp.jpg';
$fullName = $userRow['first_name'] . ' ' . $userRow['last_name'];
$email = $userRow['email'];
$userType = ucfirst($userRow['user_type']);
$favRooms = array_filter(explode(',', $userRow['rooms_favorite'] ?? ''));
?>

<!DOCTYPE html>
<html>
<head>
<title>Profile</title>
<link rel="stylesheet" href="../roomlink.css">
</head>
<body class="body">

<?php include "../views/headers.php"; ?>

<!-- PROFILE PIC + INFO -->
<div class="profile-wrapper">
    <div style="position: relative;">
        <img src="../<?= htmlspecialchars($profilePic) ?>" class="profile-pic">
        <form method="post" enctype="multipart/form-data">
            <label class="add-pic">
                +
                <input type="file" name="profile_pic" accept="image/*" onchange="this.form.submit()">
            </label>
        </form>
    </div>
    <div class="user-info">
        <p><b>Name:</b> <?= htmlspecialchars($fullName) ?></p>
        <p><b>Email:</b> <?= htmlspecialchars($email) ?></p>
        <p><b>Type:</b> <?= htmlspecialchars($userType) ?></p>
    </div>
</div>
<?php include "../views/footer.php"; ?>
</body>
</html>