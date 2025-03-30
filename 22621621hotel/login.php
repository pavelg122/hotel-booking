<?php
session_start();
include('config.php');
include('navbars/navbar.php');
include 'classes/UserManager.php';
$username = $password = "";
$username_err = $password_err = "";

$userManager = UserManager::getInstance($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter your username.";
    } else {
        $username = trim($_POST["username"]);
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty($username_err) && empty($password_err)) {
        $user = $userManager->authenticateUser($username, $password);
        if ($user) {
            $_SESSION["user_id"] = $user['user_id'];
            $_SESSION["username"] = $user['username'];
            header("Location: index.php");
            exit();
        } else {
            $password_err = "Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <h2 class="text-center">Login</h2>
            <form action="login.php" method="post">
                <div class="form-group mb-3">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($username); ?>">
                    <span class="invalid-feedback"><?php echo $username_err; ?></span>
                </div>
                <div class="form-group mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                    <span class="invalid-feedback"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary w-100" value="Login">
                </div>
                <p class="text-center mt-3">Don't have an account? <a href="register.php">Sign up now</a>.</p>
            </form>
        </div>
    </div>
</div>
</body>
</html>

