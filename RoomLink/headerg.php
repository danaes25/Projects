<!-- Hidden checkbox to toggle sidebar -->
<input type="checkbox" id="menuToggle" hidden>
<?php
require_once __DIR__ . '/../controllers/force_https.php';
?>

<header class="nav">
  <a href="../views/guestHomepage.php" class="logo"><b>RoomLink</b></a>

  <!-- Hamburger -->
  <label for="menuToggle" class="hamburger">
    <div></div>
    <div></div>
    <div></div>
  </label>
</header>

<!-- Sidebar -->
<div class="side-menu">
  <!-- Close button -->
  <label for="menuToggle" class="close-sidebar">&times;</label>

  <ul>
    <!-- Profile -->
    <li>
      <a href="../controllers/guestprofile.php">
        <svg width="20" height="20" viewBox="0 0 24 24">
          <circle cx="12" cy="8" r="4" stroke="#1d1f46ff" stroke-width="2" fill="none"/>
          <path d="M4 20c0-4 16-4 16 0" stroke="#1d1f46ff" stroke-width="2" fill="none"/>
        </svg>
        Profile
      </a>
    </li>

    <!-- Favorite -->
    <li>
      <a href="../controllers/favorite.php">
        <svg width="20" height="20" viewBox="0 0 24 24">
          <path d="M12 21s-7-4.5-9-8.5C1 8 4 5 7 7c2-2 5-2 7 0 2-2 5-1 4 3.5-2 4-9 8.5-9 8.5z"
                stroke="#1d1f46ff" stroke-width="2" fill="none"/>
        </svg>
        Favorite
      </a>
    </li>

    <!-- History -->
    <li>
      <a href="../controllers/history.php">
        <svg width="20" height="20" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="9" stroke="#1d1f46ff" stroke-width="2" fill="none"/>
          <line x1="12" y1="7" x2="12" y2="12" stroke="#1d1f46ff" stroke-width="2"/>
          <line x1="12" y1="12" x2="16" y2="14" stroke="#1d1f46ff" stroke-width="2"/>
        </svg>
        History
      </a>
    </li>

    <!-- Currency -->
    <li>
      <a href="../controllers/currency.php">
        <svg width="20" height="20" viewBox="0 0 24 24">
          <path d="M4 12h16M7 9l-3 3 3 3M17 15l3-3-3-3"
                stroke="#1d1f46ff" stroke-width="2" fill="none"/>
        </svg>
        Currency
      </a>
    </li>

    <!-- Logout -->
    <li>
      <a href="../views/Homepage.php">
        <svg width="20" height="20" viewBox="0 0 24 24">
          <path d="M16 17l5-5-5-5" stroke="#1d1f46ff" stroke-width="2" fill="none"/>
          <path d="M21 12H9" stroke="#1d1f46ff" stroke-width="2" fill="none"/>
          <path d="M12 19v2H5V3h7v2" stroke="#1d1f46ff" stroke-width="2" fill="none"/>
        </svg>
        Logout
      </a>
    </li>
  </ul>
</div>
