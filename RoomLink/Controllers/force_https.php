<?php
// Skip redirect for localhost (development)
if ($_SERVER['HTTP_HOST'] !== 'localhost' && (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off')) {
    $redirect = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header("Location: $redirect");
    exit();
}
?>
