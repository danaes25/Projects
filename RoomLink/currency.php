<?php
session_start();
include "../models/db.php";
$db = Database::getInstance();
$conn = $db->getConnection();
$userId = $_SESSION['user_id'] ?? null;

// Handle currency selection
if (isset($_POST['currency'])) {
    $currency = mysqli_real_escape_string($conn, $_POST['currency']);
    mysqli_query($conn, "UPDATE users SET preferred_currency='$currency' WHERE user_id=$userId");
    header("Location: ../views/guestHomepage.php");
    exit;
}

// Get current user currency
$res = mysqli_query($conn, "SELECT preferred_currency FROM users WHERE user_id=$userId");
$user = mysqli_fetch_assoc($res);
$currentCurrency = $user['preferred_currency'] ?? 'USD';
?>

<!DOCTYPE html>
<html>
<head>
  <title>Change Currency</title>
  <link rel="stylesheet" href="../roomlink.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&family=Cormorant+Garamond:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body class="body">
<?php include "../views/headerg.php"; ?>

<div class="currency-page">
  <h1><b>Change Currency</b></h1>
  <p>Select your preferred currency. Prices will be converted individually for your account.</p>
  
  <div class="currency-container">
    <h2><b>Choose currency</b></h2>
    
    <form method="post" class="currency-form">
      <div class="currency-dropdown-container">
        <select class="currency-dropdown" name="currency" required>
          <?php
          $currencies = [
            'USD' => 'US Dollar',
            'EUR' => 'Euro', 
            'GBP' => 'British Pound',
            'JPY' => 'Japanese Yen',
            'EGP' => 'Egyptian Pound',
            'AUD' => 'Australian Dollar',
            'CAD' => 'Canadian Dollar',
            'CHF' => 'Swiss Franc'
          ];
          
          foreach($currencies as $code => $name) {
            $selected = ($currentCurrency == $code) ? 'selected' : '';
            echo "<option value='$code' $selected>$code - $name</option>";
          }
          ?>
        </select>
      </div>
      
      <button type="submit" class="currency-submit-btn">
        <i class="fas fa-exchange-alt"></i> Apply Changes
      </button>
    </form>
    
    <?php if(isset($currentCurrency)): ?>
    <div class="currency-current">
      <h3>Current Selected Currency</h3>
      <div class="current-currency-badge">
        <?php echo $currentCurrency; ?>
      </div>
      <p>Prices will be displayed in <?php echo $currencies[$currentCurrency] ?? $currentCurrency; ?></p>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php include "../views/footer.php"; ?>
</body>
</html>