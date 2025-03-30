<?php
class ReviewManager {
    private static $instance = null;
    private $conn;

    private function __construct($conn) {
        $this->conn = $conn;
    }

    public static function getInstance($conn) {
        if (self::$instance === null) {
            self::$instance = new ReviewManager($conn);
        }
        return self::$instance;
    }

    public function getReviewsByHotelId($hotel_id) {
        $sql = "SELECT r.Rating, r.Comment, r.Timestamp, u.Username 
                FROM review r 
                JOIN user u ON r.UserID = u.UserID 
                WHERE r.HotelID = ? 
                ORDER BY r.Timestamp DESC";
        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param("i", $hotel_id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    public function addReview($user_id, $hotel_id, $rating, $comment) {
        $sql = "INSERT INTO review (UserID, HotelID, Rating, Comment) VALUES (?, ?, ?, ?)";
        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param("iiis", $user_id, $hotel_id, $rating, $comment);
            return $stmt->execute();
        }
        return false;
    }
}


