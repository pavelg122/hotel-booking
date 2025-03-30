<?php
class RoomManager {
    private static $instance = null;
    private $conn;

    private function __construct($conn) {
        $this->conn = $conn;
    }

    public static function getInstance($conn) {
        if (self::$instance === null) {
            self::$instance = new RoomManager($conn);
        }
        return self::$instance;
    }

    
    public function getRoomById($room_id, $hotel_id) {
        $sql = "SELECT * FROM Room WHERE RoomID = ? AND HotelID = ?";
        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param("ii", $room_id, $hotel_id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        }
        return null;
    }
    
     public function getRoomsByHotelId($hotel_id) {
        $sql = "SELECT * FROM Room WHERE HotelID = ?";
        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param('i', $hotel_id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }
}


