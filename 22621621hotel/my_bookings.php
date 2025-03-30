<?php 
session_start();
include('config.php');
include('classes/BookingManager.php');

if (isset($_SESSION['user_id'])) {
    include('navbars/navbar_logged_in.php');
} else {
    include('navbars/navbar.php');
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$bookingManager = BookingManager::getInstance($conn);
$bookings = $bookingManager->getUserBookings($user_id);

$conn->close(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Hotel Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>My Bookings</h1>
        <?php if (count($bookings) > 0): ?>
            <table class="table table-bordered mt-3">
                <thead class="table-light">
                    <tr>
                        <th>Hotel Name</th>
                        <th>Room Type</th>
                        <th>Room Number</th>
                        <th>Check-In</th>
                        <th>Check-Out</th>
                        <th>Total Price</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['HotelName']); ?></td>
                            <td><?php echo htmlspecialchars($booking['RoomType']); ?></td>
                            <td><?php echo htmlspecialchars($booking['RoomNumber']); ?></td>
                            <td><?php echo htmlspecialchars($booking['CheckInDate']); ?></td>
                            <td><?php echo htmlspecialchars($booking['CheckOutDate']); ?></td>
                            <td>$<?php echo number_format($booking['TotalPrice'], 2); ?></td>
                            <td><?php echo htmlspecialchars($booking['BookingStatus']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="mt-3">You have no bookings at the moment.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


