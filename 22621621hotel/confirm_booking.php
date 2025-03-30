<?php
session_start();
include('config.php');
include 'classes/HotelManager.php';
include 'classes/RoomManager.php';
include 'classes/BookingManager.php';

if (isset($_SESSION['user_id'])) {
    include('navbars/navbar_logged_in.php');
} else {
    include('navbars/navbar.php');
}

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['hotel_id']) && isset($_GET['room_id'])) {
    $hotel_id = $_GET['hotel_id'];
    $room_id = $_GET['room_id'];

    $hotelManager = HotelManager::getInstance($conn);
    $roomManager = RoomManager::getInstance($conn);
    $bookingManager = BookingManager::getInstance($conn);

    $hotel = $hotelManager->getHotelById($hotel_id);
    $room = $roomManager->getRoomById($room_id, $hotel_id);

    if (!$hotel || !$room || $room['Availability'] == 0) {
        echo "Sorry, this room is no longer available.";
        exit;
    }

    $error_message = null;
    $total_price = null;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['check_in']) && isset($_POST['check_out'])) {
            $check_in = $_POST['check_in'];
            $check_out = $_POST['check_out'];

            $check_in_date = strtotime($check_in);
            $check_out_date = strtotime($check_out);
            $current_date = strtotime(date("Y-m-d"));

            if ($check_out_date <= $check_in_date) {
                $error_message = "Check-out date must be after the check-in date.";
            } elseif ($check_in_date < $current_date) {
                $error_message = "Check-in date cannot be in the past.";
            } else {
                try {
                    $room_number = $bookingManager->calculateRoomNumber($room_id, $check_in, $check_out);

                    $bookingManager->validateRoomAvailability($room_id, $room_number);

                    $num_nights = ($check_out_date - $check_in_date) / (60 * 60 * 24);
                    $total_price = $room['Price'] * $num_nights;

                    if (isset($_POST['confirm_booking']) && isset($_SESSION['user_id'])) {
                        $user_id = $_SESSION['user_id'];

                        if ($bookingManager->createBooking($user_id, $room_id, $check_in, $check_out, $total_price, $room_number)) {
                            $_SESSION['booking_details'] = [
                                'hotel_name' => $hotel['Name'],
                                'room_type' => $room['RoomType'],
                                'check_in' => $check_in,
                                'check_out' => $check_out,
                                'total_price' => $total_price,
                                'room_number' => $room_number,
                            ];
                            header("Location: thank_you.php");
                            exit();
                        } else {
                            $error_message = "Failed to create the booking. Please try again.";
                        }
                    }
                } catch (Exception $e) {
                    $error_message = $e->getMessage();
                }
            }
        }
    }
} else {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Booking - Hotel Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Booking Confirmation</h1>
        <p>Hotel: <?php echo htmlspecialchars($hotel['Name']); ?></p>
        <p>Room: <?php echo htmlspecialchars($room['RoomType']); ?></p>
        <p>Price per night: $<?php echo number_format($room['Price'], 2); ?></p>

        <h3>Booking Dates</h3>
        <form method="POST" action="confirm_booking.php?hotel_id=<?php echo $hotel_id; ?>&room_id=<?php echo $room_id; ?>">
            <div class="form-group">
                <label for="check_in">Check-in Date:</label>
                <input type="date" name="check_in" id="check_in" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="check_out">Check-out Date:</label>
                <input type="date" name="check_out" id="check_out" class="form-control" required>
            </div>
            
            <?php
            if ($error_message) {
                echo "<div class='alert alert-danger mt-3'>$error_message</div>";
            }
            ?>

            <button type="submit" class="btn btn-primary mt-3" name="confirm_booking">Confirm Booking</button>
        </form>

        <?php
        if ($total_price) {
            echo "<p>Total Price: $" . number_format($total_price, 2) . "</p>";
        }
        ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


