<?php 
session_start();
include('config.php');
if (isset($_SESSION['user_id'])) {
    include('navbars/navbar_logged_in.php');
} else {
    include('navbars/navbar.php');
}
include('classes/HotelManager.php');
include('classes/RoomManager.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$hotelManager = HotelManager::getInstance($conn);
$roomManager = RoomManager::getInstance($conn);

if (isset($_GET['hotel_id'])) {
    $hotel_id = $_GET['hotel_id'];

    $hotel = $hotelManager->getHotelById($hotel_id);
    $rooms = $roomManager->getRoomsByHotelId($hotel_id);
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
    <title>Select Room - Hotel Reservation System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Available Rooms at <?php echo htmlspecialchars($hotel['Name']); ?></h1>
        <p><strong>Location:</strong> <?php echo htmlspecialchars($hotel['Location']); ?></p>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($hotel['Description']); ?></p>

        <div class="row">
            <?php if (count($rooms) > 0): ?>
                <?php foreach ($rooms as $room): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="images/<?php echo htmlspecialchars($room['Image']); ?>" class="card-img-top" alt="Room image">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($room['RoomType']); ?></h5>
                                <p class="card-text">Price per night: $<?php echo number_format($room['Price'], 2); ?></p>
                                <a href="confirm_booking.php?hotel_id=<?php echo $hotel_id; ?>&room_id=<?php echo $room['RoomID']; ?>" class="btn btn-primary">Book This Room</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No rooms available for this hotel at the moment.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
