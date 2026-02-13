<?php
session_start();
include "../models/db.php";
require_once '../controllers/auth.php';
checkPageAccess('adminprofile.php');

$db = Database::getInstance();
$conn = $db->getConnection();

// Make sure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("<h3>Please login</h3>");
}

$adminId = $_SESSION['user_id'];
$errors = [];
$success = '';

/* ================= UPLOAD PROFILE PIC ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic'])) {
    $file = $_FILES['profile_pic'];
    if ($file['error'] === 0) {
        $allowed = ['jpg','jpeg','png','webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $newName = "pfp_" . $adminId . "_" . time() . "." . $ext;
            $path = "uploads/" . $newName;
            if (move_uploaded_file($file['tmp_name'], "../" . $path)) {
                mysqli_query($conn, "UPDATE users SET user_profilepic='$path' WHERE user_id=$adminId");
                $success = "Profile picture updated successfully!";
            }
        }
    }
}

/* ================= ADD STAFF ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_staff'])) {
    $firstName = mysqli_real_escape_string($conn, $_POST['first_name']);
    $lastName = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Validation
    if (strlen($password) < 8 || !preg_match('/[0-9]/', $password) || !preg_match('/[\W]/', $password)) {
        $errors[] = "Password must be at least 8 characters, include 1 number and 1 special character.";
    }
    $checkEmail = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND user_type='staff'");
    if (mysqli_num_rows($checkEmail) > 0) {
        $errors[] = "Email is already used by another staff member.";
    }

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        mysqli_query($conn, "INSERT INTO users (first_name,last_name,email,password,user_type) VALUES ('$firstName','$lastName','$email','$hashedPassword','staff')");
        $success = "Staff member added successfully!";
    }
}

/* ================= EDIT STAFF ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $userId = (int)$_POST['user_id'];
    $firstName = mysqli_real_escape_string($conn, $_POST['first_name']);
    $lastName = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Email unique check
    $checkEmail = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND user_type='staff' AND user_id<>$userId");
    if (mysqli_num_rows($checkEmail) > 0) {
        $errors[] = "Email is already used by another staff member.";
    }

    // Password validation only if provided
    if (!empty($password)) {
        if (strlen($password) < 8 || !preg_match('/[0-9]/', $password) || !preg_match('/[\W]/', $password)) {
            $errors[] = "Password must be at least 8 characters, include 1 number and 1 special character.";
        }
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    }

    if (empty($errors)) {
        $updateSql = "UPDATE users SET first_name='$firstName', last_name='$lastName', email='$email'";
        if (!empty($password)) $updateSql .= ", password='$hashedPassword'";
        $updateSql .= " WHERE user_id=$userId";
        mysqli_query($conn, $updateSql);
        $success = "Staff member updated successfully!";
    }
}

/* ================= DELETE CONFIRMATION ================= */
$confirmDelete = false;
$userToDelete = 0;
$deleteType = '';
$deleteName = '';

if (isset($_GET['confirm_delete'])) {
    $userId = (int)$_GET['confirm_delete'];
    if ($userId !== $adminId) {
        mysqli_query($conn, "DELETE FROM users WHERE user_id=$userId");
        $success = "User deleted successfully!";
    }
} elseif (isset($_GET['delete_user'])) {
    $userId = (int)$_GET['delete_user'];
    if ($userId !== $adminId) {
        $userQuery = mysqli_query($conn, "SELECT first_name, last_name, user_type FROM users WHERE user_id=$userId");
        if (mysqli_num_rows($userQuery) > 0) {
            $user = mysqli_fetch_assoc($userQuery);
            $confirmDelete = true;
            $userToDelete = $userId;
            $deleteType = $user['user_type'];
            $deleteName = $user['first_name'] . ' ' . $user['last_name'];
        }
    }
}

/* ================= FETCH ADMIN INFO ================= */
$adminRes = mysqli_query($conn, "SELECT * FROM users WHERE user_id=$adminId");
$adminRow = mysqli_fetch_assoc($adminRes);
$profilePic = $adminRow['user_profilepic'] ?: 'uploads/Unknounk pfp.jpg';
$fullName = $adminRow['first_name'] . ' ' . $adminRow['last_name'];
$email = $adminRow['email'];
$userType = ucfirst($adminRow['user_type']);

/* ================= FETCH USERS ================= */
$staffRes = mysqli_query($conn, "SELECT * FROM users WHERE user_type='staff'");
$guestRes = mysqli_query($conn, "SELECT * FROM users WHERE user_type='guest'");

$staffData = [];
while ($staff = mysqli_fetch_assoc($staffRes)) $staffData[] = $staff;

// Reset pointer
$staffRes = mysqli_query($conn, "SELECT * FROM users WHERE user_type='staff'");
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Profile</title>
<link rel="stylesheet" href="../roomlink.css">
</head>
<body class="body">

<?php include "../views/header.php"; ?>

<!-- MESSAGES SECTION -->
<?php if (!empty($errors)): ?>
<div class="error-messages">
    <h3>Please fix the following errors:</h3>
    <ul>
        <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<?php if (!empty($success)): ?>
<div class="success-message">
    <p><?= htmlspecialchars($success) ?></p>
</div>
<?php endif; ?>

<!-- DELETE CONFIRMATION -->
<?php if ($confirmDelete): ?>
<div class="confirmation-popup">
    <div class="confirmation-box">
        <h2>Confirm Delete</h2>
        <p>Are you sure you want to delete <?= $deleteType ?> "<?= htmlspecialchars($deleteName) ?>"?</p>
        <p>This action cannot be undone.</p>
        <div class="confirmation-buttons">
            <a href="?confirm_delete=<?= $userToDelete ?>" class="buttonc delete-btn">Yes, Delete</a>
            <a href="adminprofile.php" class="buttonc">Cancel</a>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- PROFILE PIC + INFO -->
<div class="profile-wrapper">
    <div style="position: relative;">
        <img src="../<?= htmlspecialchars($profilePic) ?>" class="profile-pic">
        <form method="post" enctype="multipart/form-data" id="profilePicForm">
            <label class="add-pic">+
                <input type="file" name="profile_pic" accept="image/*" id="profilePicInput">
            </label>
        </form>
    </div>
    <div class="user-info">
        <p><b>Name:</b> <?= htmlspecialchars($fullName) ?></p>
        <p><b>Email:</b> <?= htmlspecialchars($email) ?></p>
        <p><b>Type:</b> <?= htmlspecialchars($userType) ?></p>
    </div>
</div>

<!-- USERS LIST -->
<h1 class="headtitlee"><b>Users</b></h1>

<h2 class="headtitlee"><b>Staff</b></h2>
<figure class="imgg">
    <!-- ADD STAFF BUTTON -->
    <div class="img">
        <a href="#addStaffPopup" class="imglink">
            <img src="../plus.jpg" alt="Add Staff"><br>
            <figcaption><b>Add Staff</b></figcaption><br>
        </a>
    </div>

    <!-- STAFF LIST -->
    <?php while ($staff = mysqli_fetch_assoc($staffRes)): ?>
    <div class="img">
        <img src="../<?= htmlspecialchars($staff['user_profilepic'] ?: 'uploads/Unknounk pfp.jpg') ?>" alt="<?= htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']) ?>">
        <figcaption><b><?= htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']) ?></b></figcaption>
        <figcaption><?= htmlspecialchars($staff['email']) ?></figcaption>
        <figcaption class="user-type">Staff</figcaption>
        <br>
        <a href="#editUserPopup<?= $staff['user_id'] ?>" class="buttonc">Edit</a>
        <a href="?delete_user=<?= $staff['user_id'] ?>" class="buttonc delete-btn">Delete</a>
    </div>
    <?php endwhile; ?>
</figure>

<h2 class="headtitlee"><b>Guests</b></h2>
<figure class="imgg">
    <?php while ($guest = mysqli_fetch_assoc($guestRes)): ?>
    <div class="img">
        <img src="../<?= htmlspecialchars($guest['user_profilepic'] ?: 'uploads/Unknounk pfp.jpg') ?>" alt="<?= htmlspecialchars($guest['first_name'] . ' ' . $guest['last_name']) ?>">
        <figcaption><b><?= htmlspecialchars($guest['first_name'] . ' ' . $guest['last_name']) ?></b></figcaption>
        <figcaption><?= htmlspecialchars($guest['email']) ?></figcaption>
        <figcaption class="user-type">Guest</figcaption>
        <br>
        <a href="?delete_user=<?= $guest['user_id'] ?>" class="buttonc delete-btn">Block</a>
    </div>
    <?php endwhile; ?>
</figure>

<!-- ADD STAFF POPUP -->
<div id="addStaffPopup" class="popup">
  <div class="popup-box">
    <h2>Add New Staff</h2>
    <form method="POST">
      <input type="hidden" name="add_staff" value="1">
      <label>First Name</label>
      <input type="text" name="first_name" required>
      <label>Last Name</label>
      <input type="text" name="last_name" required>
      <label>Email</label>
      <input type="email" name="email" required>
      <label>Password</label>
      <input type="password" name="password" required>
      <div class="popup-buttons">
        <button type="submit" class="book-btn">Add Staff</button>
        <a href="#" class="close-btn">Cancel</a>
      </div>
    </form>
  </div>
</div>

<!-- EDIT STAFF POPUPS -->
<?php foreach ($staffData as $staff): ?>
<div id="editUserPopup<?= $staff['user_id'] ?>" class="popup">
  <div class="popup-box">
    <h2>Edit Staff</h2>
    <form method="POST">
      <input type="hidden" name="edit_user" value="1">
      <input type="hidden" name="user_id" value="<?= $staff['user_id'] ?>">
      <label>First Name</label>
      <input type="text" name="first_name" value="<?= htmlspecialchars($staff['first_name']) ?>" required>
      <label>Last Name</label>
      <input type="text" name="last_name" value="<?= htmlspecialchars($staff['last_name']) ?>" required>
      <label>Email</label>
      <input type="email" name="email" value="<?= htmlspecialchars($staff['email']) ?>" required>
      <label>Password (leave blank to keep current)</label>
      <input type="password" name="password" placeholder="Enter new password">
      <div class="popup-buttons">
        <button type="submit" class="book-btn">Save Changes</button>
        <a href="#" class="close-btn">Cancel</a>
      </div>
    </form>
  </div>
</div>
<?php endforeach; ?>

<?php include "../views/footer.php"; ?>

</body>
</html>