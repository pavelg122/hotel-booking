<?php
session_start();
include('config.php');
include 'classes/HotelManager.php';

if (isset($_SESSION['user_id'])) {
    include('navbars/navbar_logged_in.php');
} else {
    include('navbars/navbar.php');
}

$hotelManager = HotelManager::getInstance($conn);
$hotels = $hotelManager->getAllHotels();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Welcome to Our Hotel Booking System</h1>
        <div class="row">
            <?php foreach ($hotels as $hotel): ?>
                <div class="col-md-4">
                    <div class="card mb-4">
                        <img src="images/<?php echo htmlspecialchars($hotel['Image']); ?>" class="card-img-top" alt="Hotel Image">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($hotel['Name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($hotel['Description']); ?></p>
                            <a href="room_selection.php?hotel_id=<?php echo $hotel['HotelID']; ?>" class="btn btn-primary">Book Now</a>
                            <a href="hotel_reviews.php?hotel_id=<?php echo $hotel['HotelID']; ?>" class="btn btn-info">Reviews</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

