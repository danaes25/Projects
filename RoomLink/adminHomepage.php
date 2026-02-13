<?php
require_once '../controllers/auth.php';
checkPageAccess('adminHomepage.php');
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin Homepage</title>
  <link rel="stylesheet" href="../roomlink.css">
</head>

<body class="body">

<?php 
include "header.php";
$conn = Database::getInstance()->getConnection();
?>

<h1 class="headtitlee"><b>Hotels Branches</b></h1>

<figure class="imgg">

  <!-- ADD HOTEL BLOCK -->
  <div class="img">
    <a href="#addHotelPopup" class="imglink">
      <img src="../plus.jpg" alt="Add Hotel">
      <br>
      <figcaption><b>Add Hotel</b></figcaption>
      <br>
    </a>
  </div>

  <!-- Example existing hotels -->
<?php
$sql = "SELECT * FROM hotels";
$result = mysqli_query($conn, $sql);

while ($hotel = mysqli_fetch_assoc($result)):
?>
  <div class="img">
    <a href="../controllers/hotel1.php?id=<?= $hotel['hotel_id'] ?>" class="imglink">
      <img src="../<?= $hotel['picture'] ?>" alt="<?= $hotel['name'] ?>">
      <figcaption>
        <b><?= $hotel['city'] ?> - <?= $hotel['country'] ?></b>
      </figcaption>
      <br>
      <button class="buttonc">View</button>
    </a>
  </div>
<?php endwhile; ?>
</figure>

<!-- ADD HOTEL POPUP -->
<div id="addHotelPopup" class="popup">
  <div class="popup-box">
    <h2>Add New Hotel</h2>
    <form method="POST" action="../controllers/addhotel.php" enctype="multipart/form-data">
      <label>Hotel Name</label>
      <input type="text" name="name" required>

      <label>City</label>
      <input type="text" name="city" required>

      <label>Country</label>
      <input type="text" name="country" required>

      <label>Description</label>
      <textarea name="description" rows="4" required></textarea>

      <label>Photo</label>
      <input type="file" name="picture" accept="image/*" required>

      <div class="popup-buttons">
        <button type="submit" name="add" class="book-btn">Add Hotel</button>
        <a href="#" class="close-btn">Cancel</a>
      </div>
    </form>
  </div>
</div>



<h1 class="headtitlee"><b>Hotels Rooms</b></h1>

<figure class="imgg">

  <!-- ADD ROOM BLOCK -->
  <div class="img">
    <a href="#addRoomPopup" class="imglink">
      <img src="../plus.jpg" alt="Add Room">
      <br>
      <figcaption><b>Add Room</b></figcaption>
      <br>
    </a>
  </div>

  <!-- Existing rooms -->
<?php
$sql = "SELECT rooms.*, hotels.name AS hotel_name FROM rooms 
        JOIN hotels ON rooms.hotel_id = hotels.hotel_id";
$result = mysqli_query($conn, $sql);

while ($room = mysqli_fetch_assoc($result)):
?>
  <div class="img">
    <a class="imglink" href="../controllers/adminroom.php?room_id=<?= $room['room_id'] ?>">
      <img src="../<?= $room['photo'] ?>" alt="<?= htmlspecialchars($room['name']) ?>">
      <figcaption>
        <b><?= htmlspecialchars($room['name']) ?></b>
      </figcaption>
      <br>
      <button class="buttonc">View</button>
    </a>
  </div>
<?php endwhile; ?>
</figure>


<!-- ADD ROOM POPUP -->
<div id="addRoomPopup" class="popup">
  <div class="popup-box">
    <h2>Add New Room</h2>
    <form method="POST" action="../controllers/addroom.php" enctype="multipart/form-data">
      <label>Room Name</label>
      <input type="text" name="name" required>

      <label>Description</label>
      <textarea name="description" rows="4" required></textarea>

      <label>Photo</label>
      <input type="file" name="photo" accept="image/*" required>

      <label>Price</label>
      <input type="number" name="price" step="0.01" required>

      <label>Hotel</label>
      <select name="hotel_id" required>
        <option value="">Select Hotel</option>
        <?php
        $hotels = mysqli_query($conn, "SELECT * FROM hotels");
        while($h = mysqli_fetch_assoc($hotels)):
        ?>
          <option value="<?= $h['hotel_id'] ?>"><?= $h['name'] ?> (<?= $h['city'] ?>)</option>
        <?php endwhile; ?>
      </select>

      <div class="popup-buttons">
        <button type="submit" name="add_room" class="book-btn">Add Room</button>
        <a href="#" class="close-btn">Cancel</a>
      </div>
    </form>
  </div>
</div>



<?php include "footer.php"; ?>

</body>
</html>