<?php
require_once "../models/db.php";
$db = Database::getInstance();
$conn = $db->getConnection();
require_once 'auth.php';
checkPageAccess('adminroom.php');

// 1. Get room ID from URL
if (!isset($_GET['room_id'])) {
    die("Room not found");
}
$roomId = (int)$_GET['room_id'];

// 2. Fetch room info
$sql = "SELECT rooms.*, hotels.name AS hotel_name 
        FROM rooms 
        JOIN hotels ON rooms.hotel_id = hotels.hotel_id 
        WHERE rooms.room_id = $roomId";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    die("Room not found");
}

$room = mysqli_fetch_assoc($result);

// 3. Handle update
$showPopup = true; // default show popup
if (isset($_POST['save'])) {
    $name = trim($_POST['room_name']);
    $price = trim($_POST['price']);
    $description = trim($_POST['description']);
    $hotel_id = $room['hotel_id'];

    // ================= BASIC VALIDATION =================
    if ($name === '' || $description === '' || $price === '') {
        die("All fields are required.");
    }
    if (!is_numeric($price) || $price <= 0) {
        die("Invalid room price.");
    }

    // ================= DUPLICATE ROOM NAME CHECK =================
    $nameCheck = mysqli_prepare(
        $conn,
        "SELECT room_id FROM rooms WHERE hotel_id = ? AND name = ? AND room_id != ?"
    );
    mysqli_stmt_bind_param($nameCheck, "isi", $hotel_id, $name, $roomId);
    mysqli_stmt_execute($nameCheck);
    mysqli_stmt_store_result($nameCheck);
    if (mysqli_stmt_num_rows($nameCheck) > 0) {
        die("A room with this name already exists in this hotel.");
    }

    // ================= IMAGE UPLOAD =================
    $imagePath = $room['photo']; // keep old image if no new upload
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = "../uploads/rooms/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = time() . "_" . basename($_FILES['image']['name']);
        $target = $uploadDir . $fileName;
        $dbPath = "uploads/rooms/" . $fileName;

        // ================= DUPLICATE IMAGE CHECK =================
        $imgCheck = mysqli_prepare($conn, "SELECT room_id FROM rooms WHERE photo = ? AND room_id != ?");
        mysqli_stmt_bind_param($imgCheck, "si", $dbPath, $roomId);
        mysqli_stmt_execute($imgCheck);
        mysqli_stmt_store_result($imgCheck);
        if (mysqli_stmt_num_rows($imgCheck) > 0) {
            die("This image is already used by another room.");
        }

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            die("Room image upload failed.");
        }

        $imagePath = $dbPath; // set new image path
    }

    // ================= UPDATE ROOM =================
    $updateSql = "UPDATE rooms 
                  SET name='$name', description='$description', price='$price', photo='$imagePath' 
                  WHERE room_id=$roomId";
    mysqli_query($conn, $updateSql);

    // Refresh room data after update
    $result = mysqli_query($conn, $sql);
    $room = mysqli_fetch_assoc($result);

    $showPopup = false; // hide popup after save
}

// 4. Handle delete
if (isset($_POST['delete'])) {
    mysqli_query($conn, "DELETE FROM rooms WHERE room_id=$roomId");
    echo "<script>alert('Room deleted'); window.location='../views/adminHomepage.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin - Manage Room</title>
  <link rel="stylesheet" href="../roomlink.css">
</head>
<body>
<?php include "../views/header.php"; ?>

<div class="room-container">
  <div class="room-name"><?= htmlspecialchars($room['name']) ?></div>
  <div class="room-price">$<?= $room['price'] ?> per night</div>
  <img src="../<?= $room['photo'] ?>" class="room-photo" alt="<?= htmlspecialchars($room['name']) ?>">
  <div class="bottom-section">

    <div class="room-description">
      <p>Hotel: <?= htmlspecialchars($room['hotel_name']) ?></p>
      <h2>Room Description</h2>
      <?= nl2br(htmlspecialchars($room['description'])) ?>

      <div class="admin-buttons">
        <a href="#update" class="admin-btn update-btn">Update</a>

        <form method="post" style="display:inline-block;">
          <button name="delete" class="admin-btn delete-btn" onclick="return confirm('Are you sure you want to delete this room?');">Delete</button>
        </form>
      </div>
    </div>

    <div class="comments">
      <h3>Guest Reviews</h3>
      <div class="comment"><b>Sarah M.</b> Amazing sea view!</div>
      <div class="comment"><b>Ali S.</b> Very clean room.</div>
    </div>

  </div>
</div>

<!-- UPDATE POPUP -->
<?php if ($showPopup): ?>
<div class="popup" id="update">
  <div class="popup-box">
    <h2>Update Room</h2>

    <form method="post" enctype="multipart/form-data">
      <label>Room Name</label>
      <input type="text" name="room_name" value="<?= htmlspecialchars($room['name']) ?>" required>

      <label>Description</label>
      <textarea name="description" required><?= htmlspecialchars($room['description']) ?></textarea>

      <label>Price</label>
      <input type="number" name="price" value="<?= $room['price'] ?>" step="0.01" required>

      <label>Photo</label>
      <input type="file" name="image">

      <div class="popup-buttons">
        <button name="save" class="admin-btn update-btn">Save</button>
        <a href="#" class="admin-btn">Cancel</a>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<?php include "../views/footer.php"; ?>
</body>
</html>
