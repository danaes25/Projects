<?php
// models/Hotel.php
class Hotel {
    private $hotelId;
    private $name;
    private $city;
    private $country;
    private $description;
    private $picture;
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Getters and Setters
    public function setHotelId($id) { $this->hotelId = $id; }
    public function getHotelId() { return $this->hotelId; }
    public function setName($name) { $this->name = $name; }
    public function getName() { return $this->name; }
    public function setCity($city) { $this->city = $city; }
    public function getCity() { return $this->city; }
    public function setCountry($country) { $this->country = $country; }
    public function getCountry() { return $this->country; }
    public function setDescription($desc) { $this->description = $desc; }
    public function getDescription() { return $this->description; }
    public function setPicture($picture) { $this->picture = $picture; }
    public function getPicture() { return $this->picture; }

    // Methods from Class Diagram
    public function getAllRooms() {
        $sql = "SELECT * FROM rooms WHERE hotel_id = " . $this->hotelId;
        return mysqli_query($this->db->getConnection(), $sql);
    }

    // New method: Get hotel by ID
    public function getById($id) {
        $this->hotelId = $id;
        $sql = "SELECT * FROM hotels WHERE hotel_id = $id";
        $result = mysqli_query($this->db->getConnection(), $sql);
        
        if ($row = mysqli_fetch_assoc($result)) {
            $this->name = $row['name'];
            $this->city = $row['city'];
            $this->country = $row['country'];
            $this->description = $row['description'];
            $this->picture = $row['picture'];
            return $this;
        }
        return null;
    }

    // Update hotel
    public function update($data, $file = null) {
        $name = $data['name'];
        $city = $data['city'];
        $country = $data['country'];
        $description = $data['description'];
        $picture = $this->picture;

        if ($file && !empty($file['name'])) {
            $uploadDir = "../uploads/hotels/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $fileName = time() . "_" . basename($file['name']);
            $target = $uploadDir . $fileName;
            move_uploaded_file($file['tmp_name'], $target);
            $picture = "uploads/hotels/" . $fileName;
        }

        $sql = "UPDATE hotels 
                SET name='$name', city='$city', country='$country', 
                    description='$description', picture='$picture'
                WHERE hotel_id=" . $this->hotelId;
        
        return mysqli_query($this->db->getConnection(), $sql);
    }

    // Delete hotel
    public function delete() {
        $sql = "DELETE FROM hotels WHERE hotel_id=" . $this->hotelId;
        return mysqli_query($this->db->getConnection(), $sql);
    }

    // Optional: Add branch method (if implementing multi-hotel)
    public function addBranch($branchData) {
        // Implementation for adding a new branch
        // This would insert into hotels table with parent_hotel_id
    }
}
?>