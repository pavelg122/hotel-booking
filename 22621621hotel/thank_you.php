<?php
session_start();
include('config.php');

$booking_details = isset($_SESSION['booking_details']) ? $_SESSION['booking_details'] : null;

if (!$booking_details) {
    echo "No booking details available.";
    exit();
}

$hotel_name = $booking_details['hotel_name'];
$room_type = $booking_details['room_type'];
$check_in = $booking_details['check_in'];
$check_out = $booking_details['check_out'];
$total_price = $booking_details['total_price'];
$room_number = $booking_details['room_number'];

unset($_SESSION['booking_details']);

if (isset($_SESSION['user_id'])) {
    include('navbars/navbar_logged_in.php');
} else {
    include('navbars/navbar.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        
        <h1>Thank you for your booking!</h1>
        <p>Your booking has been confirmed. We look forward to welcoming you.</p>

        <h3>Booking Details</h3>
        <ul>
            <li><strong>Hotel Name:</strong> <?php echo htmlspecialchars($hotel_name); ?></li>
            <li><strong>Room Type:</strong> <?php echo htmlspecialchars($room_type); ?></li>
            <li><strong>Room Number:</strong> <?php echo htmlspecialchars($room_number); ?></li>
            <li><strong>Check-in Date:</strong> <?php echo htmlspecialchars($check_in); ?></li>
            <li><strong>Check-out Date:</strong> <?php echo htmlspecialchars($check_out); ?></li>
            <li><strong>Total Price:</strong> $<?php echo htmlspecialchars($total_price); ?></li>
        </ul>

        <a href="index.php" class="btn btn-primary">Back to Home</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

