<?php
require_once 'db.php';

class Booking {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($userId, $roomId, $checkIn, $checkOut) {
        $today = date('Y-m-d');

        // ❌ Check-in before today
        if ($checkIn < $today) {
            throw new Exception("Check-in date cannot be before today.");
        }

        // ❌ Check-out before or same as check-in
        if ($checkOut <= $checkIn) {
            throw new Exception("Check-out date must be after check-in date.");
        }

        // ✅ Insert reservation
        $stmt = $this->db->prepare(
            "INSERT INTO reservations 
            (user_id, room_id, check_in, check_out, status)
            VALUES (?, ?, ?, ?, 'pending_payment')"
        );

        return $stmt->execute([$userId, $roomId, $checkIn, $checkOut]);
    }

    /**
     * 2️⃣ Fake payment (store last 4 digits only)
     */
    public function pay($reservationId, $cardNumber) {
        $last4 = substr($cardNumber, -4);

        $stmt = $this->db->prepare(
            "UPDATE reservations
             SET status = 'paid',
                 card_last4 = ?,
                 paid_at = NOW()
             WHERE reservation_id = ?"
        );

        return $stmt->execute([$last4, $reservationId]);
    }

    /**
     * 3️⃣ Cancel reservation (soft delete, update status)
     */
    public function cancel($reservationId) {
        $stmt = $this->db->prepare(
            "UPDATE reservations
             SET status = 'cancelled'
             WHERE reservation_id = ?"
        );

        return $stmt->execute([$reservationId]);
    }

    /**
     * 4️⃣ Get all reservations for a user
     */
    public function getUserBookings($userId) {
        $stmt = $this->db->prepare(
            "SELECT r.*, ro.name AS room_name, h.name AS hotel_name 
             FROM reservations r
             JOIN rooms ro ON r.room_id = ro.room_id
             JOIN hotels h ON ro.hotel_id = h.hotel_id
             WHERE r.user_id = ?
             ORDER BY r.check_in DESC"
        );

        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}