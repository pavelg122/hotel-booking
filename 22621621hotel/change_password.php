<?php
session_start();
include('config.php');
include 'classes/UserManager.php';

if (isset($_SESSION['user_id'])) {
    include('navbars/navbar_logged_in.php');
} else {
    include('navbars/navbar.php');
}

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";

$userManager = UserManager::getInstance($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];
    $user_id = $_SESSION["user_id"];
    try {
        $userManager->changePassword($new_password, $confirm_password, $user_id, $new_password_err, $confirm_password_err);
    } catch (Exception $e) {
        $general_err = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <h2 class="text-center">Reset Password</h2>
            <p class="text-center">Please fill out this form to reset your password.</p>
            <?php if (!empty($general_err)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($general_err); ?></div>
            <?php endif; ?>
            <form action="change_password.php" method="post">
                <div class="form-group mb-3">
                    <label>New Password</label>
                    <input type="password" name="new_password" class="form-control <?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($new_password); ?>">
                    <span class="invalid-feedback"><?php echo htmlspecialchars($new_password_err); ?></span>
                </div>
                <div class="form-group mb-3">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
                    <span class="invalid-feedback"><?php echo htmlspecialchars($confirm_password_err); ?></span>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary w-100" value="Submit">
                </div>
                <p class="text-center mt-3"><a href="index.php">Cancel</a></p>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



