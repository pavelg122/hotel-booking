<?php
class UserManager {
    private static $instance = null;
    private $conn;

    private function __construct($conn) {
        $this->conn = $conn;
    }

    public static function getInstance($conn) {
        if (self::$instance === null) {
            self::$instance = new UserManager($conn);
        }
        return self::$instance;
    }

    public function changePassword($new_password_input, $confirm_password_input, $user_id, &$new_password_err, &$confirm_password_err) {
    $new_password = $confirm_password = "";
    if (empty(trim($new_password_input))) {
        $new_password_err = "Please enter the new password.";
    } elseif (strlen(trim($new_password_input)) < 6) {
        $new_password_err = "Password must have at least 6 characters.";
    } else {
        $new_password = trim($new_password_input);
    }
    if (empty(trim($confirm_password_input))) {
        $confirm_password_err = "Please confirm the password.";
    } else {
        $confirm_password = trim($confirm_password_input);
        if (empty($new_password_err) && ($new_password !== $confirm_password)) {
            $confirm_password_err = "Passwords do not match.";
        }
    }
    if (empty($new_password_err) && empty($confirm_password_err)) {
        $sql = "UPDATE User SET PasswordHash = ? WHERE UserID = ?";
        try {
            if ($stmt = $this->conn->prepare($sql)) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt->bind_param("si", $hashed_password, $user_id);

                if ($stmt->execute()) {
                    session_destroy();
                    header("Location: login.php");
                    exit;
                } else {
                    throw new Exception("Failed to update the password. Please try again later.");
                }
            } else {
                throw new Exception("Database query failed.");
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}

    
    public function authenticateUser($username, $password) {
        $sql = "SELECT UserID, Username, PasswordHash FROM User WHERE Username = ?";
        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param("s", $username);
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows === 1) {
                    $stmt->bind_result($user_id, $db_username, $hashed_password);
                    if ($stmt->fetch() && password_verify($password, $hashed_password)) {
                        return ['user_id' => $user_id, 'username' => $db_username];
                    }
                }
            }
            $stmt->close();
        }
        return false;
    }
    public function validateUserRegistration($username, $email, $password, $confirm_password) {
        $errors = [];
        if (empty(trim($username))) {
            $errors['username_err'] = "Please enter a username.";
        } else {
            $sql = "SELECT UserID FROM User WHERE Username = ?";
            if ($stmt = $this->conn->prepare($sql)) {
                $stmt->bind_param("s", $param_username);
                $param_username = trim($username);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    $errors['username_err'] = "This username is already taken.";
                }
                $stmt->close();
            }
        }
        
        if (empty(trim($email))) {
            $errors['email_err'] = "Please enter an email address.";
        } else {
            $errors['email'] = $email;
        }
        
        if (empty(trim($password))) {
            $errors['password_err'] = "Please enter a password.";
        } elseif (strlen(trim($password)) < 6) {
            $errors['password_err'] = "Password must have at least 6 characters.";
        }
        
        if (empty(trim($confirm_password))) {
            $errors['confirm_password_err'] = "Please confirm password.";
        } elseif ($password !== $confirm_password) {
            $errors['confirm_password_err'] = "Password did not match.";
        }

        return $errors;
    }

    public function createUser($username, $email, $password) {
        $sql = "INSERT INTO User (Username, Email, PasswordHash, CreatedAt) VALUES (?, ?, ?, NOW())";
        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param("sss", $param_username, $param_email, $param_password);
            $param_username = $username;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            return $stmt->execute();
        }
        return false;
    }
}


