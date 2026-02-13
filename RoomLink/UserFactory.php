<?php
// UserFactory.php

// Base User class
abstract class User {
    protected $userId;
    protected $firstName;
    protected $lastName;
    protected $email;
    protected $userType;

    public function __construct($userData) {
        $this->userId = $userData['user_id'];
        $this->firstName = $userData['first_name'];
        $this->lastName = $userData['last_name'];
        $this->email = $userData['email'];
        $this->userType = $userData['user_type'];
    }

    public function getUserId() { return $this->userId; }
    public function getFullName() { return $this->firstName . ' ' . $this->lastName; }
    public function getEmail() { return $this->email; }
    public function getUserType() { return $this->userType; }
    
    // Abstract methods - each role implements differently
    abstract public function getHomepage();
    abstract public function getAllowedPages();
}

// Admin user
class AdminUser extends User {
    public function getHomepage() {
        return '../views/adminHomepage.php';
    }
    
    public function getAllowedPages() {
        return ['../views/adminHomepage.php', '../controllers/addhotel.php', '../controllers/addroom.php', 
                '../controllers/hotel.php', '../controllers/adminroom.php', '../views/header.php' ];
    }
    
    public function canManageHotels() { return true; }
    public function canManageRooms() { return true; }
    public function canViewAllBookings() { return true; }
}

// Staff user
class StaffUser extends User {
    public function getHomepage() {
        return '../views/staffHomepage.php';
    }
    
    public function getAllowedPages() {
        return ['../views/staffHomepage.php', '../controllers/addroom.php', '../controllers/adminroom.php', 
                '../controllers/hotel.php', '../views/headers.php'];
    }
    
    public function canManageRooms() { return true; }
    public function canViewBookings() { return true; }
}

// Guest user
class GuestUser extends User {
    public function getHomepage() {
        return '../views/guestHomepage.php';
    }
    
    public function getAllowedPages() {
        return ['../views/guestHomepage.php', '../controllers/hotel.php', '../controllers/guestroom.php', 
                '../controllers/favorite.php', '../controllers/history.php', '../views/headerg.php'];
    }
    
    public function canBookRooms() { return true; }
    public function canViewOwnBookings() { return true; }
}

// Factory class
class UserFactory {
    public static function createUser($userData) {
        switch($userData['user_type']) {
            case 'admin':
                return new AdminUser($userData);
            case 'staff':
                return new StaffUser($userData);
            case 'guest':
            default:
                return new GuestUser($userData);
        }
    }
}
?>