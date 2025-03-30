<?php
class HotelManager {
    private static $instance = null;
    private $conn;

    private function __construct($conn) {
        $this->conn = $conn;
    }

    public static function getInstance($conn) {
        if (self::$instance === null) {
            self::$instance = new HotelManager($conn);
        }
        return self::$instance;
    }

    public function addHotel($name, $location, $description) {
        $sql = "INSERT INTO Hotel (Name, Location, Description) VALUES (?, ?, ?)";
        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param("sss", $name, $location, $description);
            return $stmt->execute();
        }
        return false;
    }

    
      public function getHotelById($hotel_id) {
        $sql = "SELECT * FROM Hotel WHERE HotelID = ?";
        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param("i", $hotel_id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        }
        return null;
    }
    
      public function getAllHotels() {
        $sql = "SELECT * FROM Hotel";
        $result = $this->conn->query($sql);
        $hotels = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $hotels[] = $row;
            }
        }
        return $hotels;
    }
    
}


