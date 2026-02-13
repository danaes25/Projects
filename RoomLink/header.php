<!-- Hidden checkbox to toggle sidebar -->
<?php
require_once __DIR__ . '/../controllers/force_https.php';
?>

<input type="checkbox" id="menuToggle" hidden>

<header class="nav">
  <a href="../views/adminHomepage.php" class="logo"><b>RoomLink</b></a>

  <form method="get" action="../controllers/search.php" class="search-form">
    <input class="search-aands" type="text" name="q" placeholder="Search..." required>
    <button type="submit" class="search-btn">
      <svg width="20" height="20" viewBox="0 0 24 24">
        <circle cx="11" cy="11" r="7" stroke="#1d1f46ff" stroke-width="2" fill="none"/>
        <line x1="16" y1="16" x2="22" y2="22" stroke="#1d1f46ff" stroke-width="2"/>
      </svg>
    </button>
  </form>

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
    <li>
      <a href="../controllers/adminprofile.php">
        <svg width="20" height="20" viewBox="0 0 24 24">
          <circle cx="12" cy="8" r="4" stroke="#1d1f46ff" stroke-width="2" fill="none"/>
          <path d="M4 20c0-4 16-4 16 0" stroke="#1d1f46ff" stroke-width="2" fill="none"/>
        </svg>
        Profile
      </a>
    </li>

    <!-- calendar -->
    <li>
      <a href="../controllers/calendar.php">
        <svg width="20" height="20" viewBox="0 0 24 24">
          <rect x="3" y="5" width="18" height="16" stroke="#1d1f46ff" stroke-width="2" fill="none"/>
          <line x1="3" y1="9" x2="21" y2="9" stroke="#1d1f46ff" stroke-width="2"/>
        </svg>
        Calendar
      </a>
    </li>

    <!-- Generate Reports -->
    <li>
      <a href="../controllers/reports.php">
        <svg width="20" height="20" viewBox="0 0 24 24">
          <rect x="4" y="3" width="16" height="18" stroke="#1d1f46ff" stroke-width="2" fill="none"/>
          <line x1="8" y1="7" x2="16" y2="7" stroke="#1d1f46ff" stroke-width="2"/>
          <line x1="8" y1="11" x2="16" y2="11" stroke="#1d1f46ff" stroke-width="2"/>
          <line x1="8" y1="15" x2="14" y2="15" stroke="#1d1f46ff" stroke-width="2"/>
        </svg>
        Generate Reports
      </a>
    </li>

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
