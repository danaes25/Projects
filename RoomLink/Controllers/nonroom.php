<?php
require_once "../models/db.php";
$db = Database::getInstance();
$conn = $db->getConnection();

// No need to checkPageAccess for guests, anyone can view
// Don't require login here

// 1. Get room ID
if (!isset($_GET['room_id'])) die("Room not found");
$roomId = (int)$_GET['room_id'];

// 2. Fetch room info
$sql = "SELECT rooms.*, hotels.name AS hotel_name FROM rooms
        JOIN hotels ON rooms.hotel_id = hotels.hotel_id
        WHERE rooms.room_id=$roomId";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result)==0) die("Room not found");
$room = mysqli_fetch_assoc($result);

// 3. Fetch reviews (unchanged)
$reviews=[];
$reviewSql="SELECT r.comment, u.first_name, u.last_name
            FROM reviews r
            JOIN users u ON r.user_id=u.user_id
            WHERE r.room_id=$roomId
            ORDER BY r.created_at DESC";
$reviewResult = mysqli_query($conn,$reviewSql);
while($r=mysqli_fetch_assoc($reviewResult)) $reviews[]=$r;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($room['name']) ?></title>
<link rel="stylesheet" href="../roomlink.css">
</head>
<body>
<?php include "../views/headerb.php"; ?>

<div class="room-container">
  <div class="room-name"><?= htmlspecialchars($room['name']) ?></div>
  <div class="room-price">$<?= number_format($room['price'],2) ?> per night</div>
  <img src="../<?= htmlspecialchars($room['photo']) ?>" class="room-photo" alt="<?= htmlspecialchars($room['name']) ?>">

  <div class="bottom-section">
    <div class="room-description">
      <p>Hotel: <?= htmlspecialchars($room['hotel_name']) ?></p>
      <?= nl2br(htmlspecialchars($room['description'])) ?>

      <div class="button-row">
        <!-- Instead of real reservation/favorite, trigger sign-in popup -->
        <a href="#signin" class="book-btn">Reserve Now</a>
        <a href="#signin" class="fav-btn">Favorite ♡</a>
      </div>
    </div>

    <div class="comments">
      <h3>Guest Reviews</h3>
      <?php if(count($reviews)>0): ?>
        <?php foreach($reviews as $rev): ?>
          <div class="comment"><b><?= htmlspecialchars($rev['first_name'].' '.$rev['last_name']) ?>:</b> <?= htmlspecialchars($rev['comment']) ?></div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="comment">No reviews yet</div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- SIGN IN POPUP -->
<div class="popup" id="signin">
  <div class="popup-box">
    <h2>Sign In</h2>
    <form method="post" action="../controllers/signin.php">
      <label>Email</label>
      <input type="email" name="email" required>
      <label>Password</label>
      <input type="password" name="password" required>
      <div class="popup-buttons">
        <a href="#signup" style="color:  #1d1f46ff; align-items: center; text-decoration: none;"><b>Don't have Account?</b></a>
        <br>
        <button class="book-btn">Sign In</button>
        <a href="#" class="close-btn">Cancel</a>
      </div>
    </form>
  </div>
</div>

<!-- SIGN UP POPUP -->
<div class="popup" id="signup">
  <div class="popup-box">
    <h2>Sign Up</h2>
    <form method="post" action="../controllers/signup.php">
      <label>First Name</label>
      <input type="text" name="fname" required>
      <label>Last Name</label>
      <input type="text" name="lname" required>
      <label>Email</label>
      <input type="email" name="email" required>
      <label>Password</label>
      <input type="password" name="password" required>
      <label>Confirm Password</label>
      <input type="password" name="confirm" required>
      <div class="popup-buttons">
        <button class="book-btn">Create Account</button>
        <a href="#" class="close-btn">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?php include "../views/footer.php"; ?>
</body>
</html>
