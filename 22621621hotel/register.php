<?php 
session_start();
include('config.php');
if (isset($_SESSION['user_id'])) {
    include('navbars/navbar_logged_in.php');
} else {
    include('navbars/navbar.php');
}
include('classes/UserManager.php');

$userManager = UserManager::getInstance($conn);

$username = $email = $password = $confirm_password = "";
$username_err = $email_err = $password_err = $confirm_password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $errors = $userManager->validateUserRegistration($_POST['username'], $_POST['email'], $_POST['password'], $_POST['confirm_password']);
    
    $username_err = isset($errors['username_err']) ? $errors['username_err'] : '';
    $email_err = isset($errors['email_err']) ? $errors['email_err'] : '';
    $password_err = isset($errors['password_err']) ? $errors['password_err'] : '';
    $confirm_password_err = isset($errors['confirm_password_err']) ? $errors['confirm_password_err'] : '';

    if (empty($username_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
        if ($userManager->createUser($_POST['username'], $_POST['email'], $_POST['password'])) {
            header("Location: login.php");
            exit();
        } else {
            echo "Something went wrong. Please try again later.";
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <h2 class="text-center">Register</h2>
            <form action="register.php" method="post">
                <div class="form-group mb-3">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($username); ?>">
                    <span class="invalid-feedback"><?php echo $username_err; ?></span>
                </div>
                <div class="form-group mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($email); ?>">
                    <span class="invalid-feedback"><?php echo $email_err; ?></span>
                </div>
                <div class="form-group mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                    <span class="invalid-feedback"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group mb-3">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
                    <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary w-100" value="Register">
                </div>
                <p class="text-center mt-3">Already have an account? <a href="login.php">Login here</a>.</p>
            </form>
        </div>
    </div>
</div>
</body>
</html>


