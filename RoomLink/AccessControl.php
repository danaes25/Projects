<?php
// AccessControl.php - Strategy Pattern for role-based access control

interface AccessStrategy {
    public function checkAccess($currentPage);
    public function getRedirectPage();
}

class AdminAccessStrategy implements AccessStrategy {
    public function checkAccess($currentPage) {
        return true; // Admin can access everything
    }
    
    public function getRedirectPage() {
        return 'adminHomepage.php';
    }
}

class StaffAccessStrategy implements AccessStrategy {
    private $allowedPages = [
        'staffHomepage.php', 
        'addroom.php', 
        'staffroom.php',
        'nonroom.php',
        'adminroom.php', 
        'housekeeper.php',
        'hotel.php',
        'headerb.php'
    ];
    
    public function checkAccess($currentPage) {
        return in_array($currentPage, $this->allowedPages);
    }
    
    public function getRedirectPage() {
        return 'staffHomepage.php';
    }
}

class GuestAccessStrategy implements AccessStrategy {
    private $allowedPages = [
        'guestHomepage.php', 
        'hotel.php', 
        'guestroom.php', 
        'favorite.php', 
        'history.php',
        'payReservation.php', 
        'payment.php',
        'headerg.php'
    ];
    
    public function checkAccess($currentPage) {
        return in_array($currentPage, $this->allowedPages);
    }
    
    public function getRedirectPage() {
    return '/Roomlink/views/guestHomepage.php';
}

}

class AccessController {
    private $strategy;
    
    public function __construct(AccessStrategy $strategy) {
        $this->strategy = $strategy;
    }
    
    public function validateAccess($currentPage) {
        if (!$this->strategy->checkAccess($currentPage)) {
            header("Location: " . $this->strategy->getRedirectPage());
            exit();
        }
    }
}

class AccessStrategyFactory {
    public static function getStrategy($userType) {
        switch($userType) {
            case 'admin':
                return new AdminAccessStrategy();
            case 'staff':
                return new StaffAccessStrategy();
            case 'guest':
            default:
                return new GuestAccessStrategy();
        }
    }
}
?>