<?php
require_once 'db.php';

class Room {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($hotelId, $roomType, $price, $picture) {
        $stmt = $this->db->prepare(
            "INSERT INTO rooms (hotel_id, type, price, picture)
             VALUES (?, ?, ?, ?)"
        );
        return $stmt->execute([$hotelId, $roomType, $price, $picture]);
    }

    public function getRoomsByHotel($hotelId) {
        $stmt = $this->db->prepare(
            "SELECT * FROM rooms WHERE hotel_id = ?"
        );
        $stmt->execute([$hotelId]);
        return $stmt->fetchAll();
    }

    public function delete($roomId) {
        $stmt = $this->db->prepare(
            "DELETE FROM rooms WHERE room_id = ?"
        );
        return $stmt->execute([$roomId]);
    }
}
