<?php
session_start();
include('config.php');
include 'classes/ReviewManager.php';
include 'classes/HotelManager.php';
if (isset($_SESSION['user_id'])) {
    include('navbars/navbar_logged_in.php');
} else {
    include('navbars/navbar.php');
}

if (!isset($_GET['hotel_id']) || empty($_GET['hotel_id'])) {
    header("Location: index.php");
    exit();
}

$hotel_id = $_GET['hotel_id'];

$hotelManager = HotelManager::getInstance($conn);
$reviewManager = ReviewManager::getInstance($conn);

$hotel = $hotelManager->getHotelById($hotel_id);
if (!$hotel) {
    echo "Hotel not found.";
    exit();
}

$reviews = $reviewManager->getReviewsByHotelId($hotel_id);
$no_reviews_message = "No reviews yet. Be the first to leave a review!";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $rating = $_POST['rating'];
        $comment = $_POST['comment'];

        if ($reviewManager->addReview($user_id, $hotel_id, $rating, $comment)) {
            header("Location: hotel_reviews.php?hotel_id=$hotel_id");
            exit();
        } else {
            $error_message = "Failed to submit your review. Please try again.";
        }
    } else {
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($hotel['Name']); ?> - Hotel Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1><?php echo htmlspecialchars($hotel['Name']); ?></h1>
        <p><strong>Location:</strong> <?php echo htmlspecialchars($hotel['Location']); ?></p>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($hotel['Description']); ?></p>

        <h3>Reviews</h3>
        <button class="btn btn-info" data-bs-toggle="collapse" href="#reviewForm" role="button" aria-expanded="false" aria-controls="reviewForm">Add Review</button>

        <div class="collapse mt-3" id="reviewForm">
            <form method="POST" action="hotel_reviews.php?hotel_id=<?php echo $hotel['HotelID']; ?>">
                <div class="form-group">
                    <label for="rating">Rating:</label>
                    <select name="rating" id="rating" class="form-control" required>
                        <option value="1">1 - Poor</option>
                        <option value="2">2 - Fair</option>
                        <option value="3">3 - Good</option>
                        <option value="4">4 - Very Good</option>
                        <option value="5">5 - Excellent</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="comment">Comment:</label>
                    <textarea name="comment" id="review_text" class="form-control" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary mt-3" name="submit_review">Submit Review</button>
            </form>
        </div>

        <h4>Existing Reviews</h4>
        <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $review): ?>
                <div class="border p-3 mb-3">
                    <p><strong><?php echo htmlspecialchars($review['Username']); ?></strong> - 
                       <strong>Rating:</strong> <?php echo $review['Rating']; ?>/5</p>
                    <p><?php echo nl2br(htmlspecialchars($review['Comment'])); ?></p>
                    <p><small>Posted on: <?php echo date("F j, Y, g:i a", strtotime($review['Timestamp'])); ?></small></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p><?php echo $no_reviews_message; ?></p>
        <?php endif; ?>

        <a href="index.php" class="btn btn-secondary mt-4">Back to Home</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

