<?php
class BookingManager {
    private static $instance = null;
    private $conn;

    private function __construct($conn) {
        $this->conn = $conn;
    }

    public static function getInstance($conn) {
        if (self::$instance === null) {
            self::$instance = new BookingManager($conn);
        }
        return self::$instance;
    }
public function calculateRoomNumber($room_id, $check_in, $check_out) {
        $query = "
            SELECT RoomNumber 
            FROM Booking 
            WHERE RoomID = ? 
              AND NOT (CheckOutDate <= ? OR CheckInDate >= ?)
            ORDER BY RoomNumber";
        
        if ($stmt = $this->conn->prepare($query)) {
            $stmt->bind_param('iss', $room_id, $check_in, $check_out);
            $stmt->execute();
            $result = $stmt->get_result();
            $used_numbers = [];

            while ($row = $result->fetch_assoc()) {
                $used_numbers[] = $row['RoomNumber'];
            }
            $stmt->close();

            $room_number = 1;
            while (in_array($room_number, $used_numbers)) {
                $room_number++;
            }

            return $room_number;
        }

        throw new Exception("Unable to calculate room number.");
    }

    public function validateRoomAvailability($room_id, $room_number) {
        $query = "SELECT MaxNumber FROM Room WHERE RoomID = ?";
        
        if ($stmt = $this->conn->prepare($query)) {
            $stmt->bind_param("i", $room_id);
            $stmt->execute();
            $stmt->bind_result($max_number);
            $stmt->fetch();
            $stmt->close();

            if ($room_number > $max_number) {
                throw new Exception("No available rooms for the selected dates.");
            }
        } else {
            throw new Exception("Failed to validate room availability.");
        }
    }

    public function createBooking($user_id, $room_id, $check_in, $check_out, $total_price, $room_number, $status = "Confirmed") {
        $sql = "INSERT INTO Booking (UserID, RoomID, RoomNumber, CheckInDate, CheckOutDate, TotalPrice, BookingStatus) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param("iiissds", $user_id, $room_id, $room_number, $check_in, $check_out, $total_price, $status);
            return $stmt->execute();
        }
        return false;
    }
    public function getUserBookings($user_id) {
        $query = "
            SELECT b.BookingID, b.CheckInDate, b.CheckOutDate, b.TotalPrice, b.BookingStatus, 
            b.RoomNumber, r.RoomType, h.Name AS HotelName
            FROM Booking b
            JOIN Room r ON b.RoomID = r.RoomID
            JOIN Hotel h ON r.HotelID = h.HotelID
            WHERE b.UserID = ?
            ORDER BY b.Timestamp DESC
        ";

        if ($stmt = $this->conn->prepare($query)) {
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $bookings = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $bookings;
        }
        return [];
    }
}
