<meta charset="UTF-8">
<link rel="stylesheet" href="../roomlink.css">
<?php
require_once __DIR__ . '/../controllers/force_https.php';
?>
<header class="nav">
  <a href="../views/Homepage.php" style="text-decoration:none; color:white;">
    <b>RoomLink</b>
  </a>
  <div class="nav-auth">
    <a href="#signin" class="auth-btn">Sign In</a>
    <a href="#signup" class="auth-btn signup">Sign Up</a>
  </div>
</header>

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