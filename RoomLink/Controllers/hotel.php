<?php
session_start();
require_once "../models/db.php";

$db = Database::getInstance();
$conn = $db->getConnection();

/* ================= USER ROLE ================= */
$isLoggedIn = isset($_SESSION['user_id']);

/* ================= HEADER & ROOM LINK ================= */
$headerFile = "../views/headerb.php";
$roomController = "nonroom.php";
$canManage = false;


/* ================= HOTEL ID ================= */
if (!isset($_GET['id'])) {
    die("Hotel not found");
}
$hotelId = (int)$_GET['id'];

/* ================= FETCH HOTEL ================= */
$hotelSql = "SELECT * FROM hotels WHERE hotel_id = $hotelId";
$hotelRes = mysqli_query($conn, $hotelSql);
if (mysqli_num_rows($hotelRes) == 0) die("Hotel not found");
$hotel = mysqli_fetch_assoc($hotelRes);

/* ================= UPDATE HOTEL (ADMIN ONLY) ================= */
$showHotelPopup = false;
if ($canManage && isset($_POST['save_hotel'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $imagePath = $hotel['picture'];
    if (!empty($_FILES['picture']['name'])) {
        $dir = "../uploads/hotels/";
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        $file = time() . "_" . basename($_FILES['picture']['name']);
        move_uploaded_file($_FILES['picture']['tmp_name'], $dir . $file);
        $imagePath = "uploads/hotels/" . $file;
    }

    mysqli_query($conn, "
        UPDATE hotels SET
        name='$name',
        city='$city',
        country='$country',
        description='$description',
        picture='$imagePath'
        WHERE hotel_id=$hotelId
    ");

    header("Location: hotel.php?id=$hotelId");
    exit;
}

/* ================= DELETE HOTEL (ADMIN ONLY) ================= */
if ($canManage && isset($_POST['delete_hotel'])) {
    mysqli_query($conn, "DELETE FROM hotels WHERE hotel_id=$hotelId");
    header("Location: ../views/adminHomepage.php");
    exit;
}

/* ================= ROOMS ================= */
$roomRes = mysqli_query($conn, "SELECT * FROM rooms WHERE hotel_id=$hotelId");

/* ================= DELETE ROOM (ADMIN ONLY) ================= */
if ($canManage && isset($_POST['delete_room'])) {
    $roomId = (int)$_POST['room_id'];
    mysqli_query($conn, "DELETE FROM rooms WHERE room_id=$roomId");
    header("Location: hotel.php?id=$hotelId");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title><?= htmlspecialchars($hotel['name']) ?></title>
  <link rel="stylesheet" href="../roomlink.css">
</head>
<body>

<?php include $headerFile; ?>

<div class="room-container">

  <div class="room-name"><?= htmlspecialchars($hotel['name']) ?></div>
  <img src="../<?= htmlspecialchars($hotel['picture']) ?>" class="room-photo" alt="Hotel">
  <div class="room-description">
      <?= nl2br(htmlspecialchars($hotel['description'])) ?>
  </div>

  <!-- ADMIN HOTEL BUTTONS -->
  <?php if ($canManage): ?>
  <div class="admin-buttons">
    <a href="#updateHotel" class="admin-btn update-btn">Update Hotel</a>
    <form method="post" style="display:inline;">
      <button name="delete_hotel" class="admin-btn delete-btn"
              onclick="return confirm('Delete this hotel?')">
        Delete Hotel
      </button>
    </form>
  </div>
  <?php endif; ?>

  <h1 class="headtitlee"><b>Rooms</b></h1>

  <figure class="imgg">
  <?php while ($room = mysqli_fetch_assoc($roomRes)): ?>
    <div class="img">

      <a class="imglink"
         href="<?= $roomController ?>?room_id=<?= $room['room_id'] ?>">
        <img src="../<?= htmlspecialchars($room['photo']) ?>">
        <figcaption><b><?= htmlspecialchars($room['name']) ?></b></figcaption>
      </a>

      <br>

      <button class="buttonc"
        onclick="window.location.href='<?= $roomController ?>?room_id=<?= $room['room_id'] ?>'">
        View
      </button>

      <!-- ADMIN ROOM BUTTONS -->
      <?php if ($canManage): ?>
      <div class="admin-buttons">
        <a href="../controllers/adminroom.php?room_id=<?= $room['room_id'] ?>"
           class="admin-btn update-btn">Update Room</a>

        <form method="post" style="display:inline;">
          <input type="hidden" name="room_id" value="<?= $room['room_id'] ?>">
          <button name="delete_room" class="admin-btn delete-btn"
                  onclick="return confirm('Delete room?')">
            Delete Room
          </button>
        </form>
      </div>
      <?php endif; ?>

    </div>
  <?php endwhile; ?>
  </figure>

</div>

<!-- UPDATE HOTEL POPUP -->
<?php if ($canManage): ?>
<div class="popup" id="updateHotel">
  <div class="popup-box">
    <h2>Update Hotel</h2>
    <form method="post" enctype="multipart/form-data">

      <label>Name</label>
      <input type="text" name="name" value="<?= htmlspecialchars($hotel['name']) ?>" required>

      <label>City</label>
      <input type="text" name="city" value="<?= htmlspecialchars($hotel['city']) ?>" required>

      <label>Country</label>
      <input type="text" name="country" value="<?= htmlspecialchars($hotel['country']) ?>" required>

      <label>Description</label>
      <textarea name="description" required><?= htmlspecialchars($hotel['description']) ?></textarea>

      <label>Photo</label>
      <input type="file" name="picture">

      <div class="popup-buttons">
        <button name="save_hotel" class="admin-btn update-btn">Save</button>
        <a href="#" class="admin-btn">Cancel</a>
      </div>

    </form>
  </div>
</div>
<?php endif; ?>

<?php include "../views/footer.php"; ?>
</body>
</html>
