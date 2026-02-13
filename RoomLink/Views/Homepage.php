<!DOCTYPE html>
<html>
<head>
  <title>Homepage</title>
  <link rel="stylesheet" href="../roomlink.css">
</head>

<body class="body">
<?php
require_once "../models/db.php";
$db = Database::getInstance();
$conn = $db->getConnection();
 include "headerb.php"; ?>

<!-- SEARCH NAV BAR -->
<div class="search-nav">
  <form method="get" action="../controllers/nonsearch.php" class="search-form">


    <!-- Destination -->
    <div class="search-box">
      <input type="text" name="destination" placeholder="Which branch?" required>
    </div>

    <!-- Dates -->
    <div class="search-box">
      <input type="date" name="checkin" required>
      <input type="date" name="checkout" required>
    </div>

    <!-- Guests -->
    <?php
   $adults   = isset($_GET['adults']) ? max(1, (int)$_GET['adults']) : 2;
   $children = isset($_GET['children']) ? max(0, (int)$_GET['children']) : 0;
   $rooms    = isset($_GET['rooms']) ? max(1, (int)$_GET['rooms']) : 1;
  ?>

  <!-- GUESTS DROPDOWN -->
<div class="search-box guests-box">

  <!-- Toggle -->
  <input type="checkbox" id="guestsToggle" hidden>

  <label for="guestsToggle" class="guests-label">
  <span class="person-icon"></span>
  <?= $adults ?> adults · <?= $children ?> children · <?= $rooms ?> room
</label>

  <!-- DROPDOWN -->
  <div class="guests-dropdown">

    <!-- Adults -->
    <div class="counter">
      <span>Adults</span>
      <div class="counter-controls">
        <button name="adults" value="<?= $adults - 1 ?>">−</button>
        <span><?= $adults ?></span>
        <button name="adults" value="<?= $adults + 1 ?>">+</button>
      </div>
    </div>

    <!-- Children -->
    <div class="counter">
      <span>Children</span>
      <div class="counter-controls">
        <button name="children" value="<?= $children - 1 ?>">−</button>
        <span><?= $children ?></span>
        <button name="children" value="<?= $children + 1 ?>">+</button>
      </div>
    </div>

    <!-- Rooms -->
    <div class="counter">
      <span>Rooms</span>
      <div class="counter-controls">
        <button name="rooms" value="<?= $rooms - 1 ?>">−</button>
        <span><?= $rooms ?></span>
        <button name="rooms" value="<?= $rooms + 1 ?>">+</button>
      </div>
    </div>

  </div>
</div>

<!-- Keep values -->
<input type="hidden" name="adults" value="<?= $adults ?>">
<input type="hidden" name="children" value="<?= $children ?>">
<input type="hidden" name="rooms" value="<?= $rooms ?>">


<!-- Hidden fields to keep values on submit -->
<input type="hidden" name="adults" value="<?= $adults ?>">
<input type="hidden" name="children" value="<?= $children ?>">
<input type="hidden" name="rooms" value="<?= $rooms ?>">



    <!-- Search Button -->
    <button type="submit" class="search-btn">Search</button>

  </form>
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



<h1 class="headtitlee"><b>Hotels Branches</b></h1>

<figure class="imgg">
<?php
$sql = "SELECT * FROM hotels";
$result = mysqli_query($conn, $sql);

while ($hotel = mysqli_fetch_assoc($result)):
?>
  <div class="img">
    <a href="../controllers/hotel.php?id=<?= $hotel['hotel_id'] ?>" class="imglink">
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


<h1 class="headtitlee"><b>Hotels Rooms</b></h1>

<figure class="imgg">
<?php
$sql = "SELECT rooms.*, hotels.name AS hotel_name 
        FROM rooms 
        JOIN hotels ON rooms.hotel_id = hotels.hotel_id";
$result = mysqli_query($conn, $sql);

while ($room = mysqli_fetch_assoc($result)):
?>
  <div class="img">
    <a class="imglink" href="../controllers/nonroom.php?room_id=<?= $room['room_id'] ?>">
      <img src="../<?= htmlspecialchars($room['photo']) ?>" alt="<?= htmlspecialchars($room['name']) ?>">
      <figcaption>
        <b><?= htmlspecialchars($room['name']) ?></b>
      </figcaption>
      <br>
      <button class="buttonc">View</button>
    </a>
  </div>
<?php endwhile; ?>
</figure>



<?php include "footer.php"; ?>

<!-- Chatbot Button -->
<div id="chatbot-icon">💬</div>

<!-- Chatbot Box -->
<div id="chatbot-box">
    <div id="chatbot-header">RoomLink Assistant</div>

    <div id="chatbot-messages"></div>

    <input type="text" id="chatbot-input" placeholder="Ask me something..." />
    <button onclick="sendMessage()">Send</button>
</div>


<script>
const icon = document.getElementById("chatbot-icon");
const box = document.getElementById("chatbot-box");
const messagesDiv = document.getElementById("chatbot-messages");

let greeted = false;

icon.onclick = () => {
    const isOpening = box.style.display !== "block";
    box.style.display = isOpening ? "block" : "none";

    // Show greeting only once
    if (isOpening && !greeted) {
        messagesDiv.innerHTML += `
            <div>
                <b>RoomLink Concierge:</b> Hello 👋 Welcome to RoomLink.
                I can help you with rooms, bookings, prices, and cancellations.
            </div>
        `;
        greeted = true;
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }
};



function sendMessage() {
    const input = document.getElementById("chatbot-input");
    const message = input.value.trim();
    if (message === "") return;

    const messagesDiv = document.getElementById("chatbot-messages");

    messagesDiv.innerHTML += `<div><b>You:</b> ${message}</div>`;
    input.value = "";

    fetch("/RoomLink/chatbot.php", {

        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "message=" + encodeURIComponent(message)
    })
    .then(response => response.text())
    .then(reply => {
       messagesDiv.innerHTML += `<div><b>RoomLink Assistant:</b> ${reply}</div>`;

        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    });
}
</script>



</body>
</html>

