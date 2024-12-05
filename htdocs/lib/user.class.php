<?php
// Load Composer's autoloader
require 'vendor/autoload.php';

// Include PHPMailer namespaces
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class user {
    private $db; // Store the database connection

    // Constructor to accept the database connection
    function __construct($conn) {
        $this->db = $conn; // Assign the connection to the class property
    }

    // Signup function
    function signup($username, $email, $phone, $password) {
        // Generate a unique hex salt key
        $saltKey = bin2hex(random_bytes(32)); // 64 characters

        // Merge password and salt key
        $saltedPassword = $password . $saltKey;

        // Hash the salted password
        $passwordHash = password_hash($saltedPassword, PASSWORD_BCRYPT);

        // Generate a 6-digit OTP
        $otp = random_int(100000, 999999);

        // Insert user details into the database with OTP
        $stmt = $this->db->prepare("INSERT INTO user (username, email, phone, salt, password, otp, is_verified) VALUES (?, ?, ?, ?, ?, ?, 0)");
        $stmt->bind_param("sssssi", $username, $email, $phone, $saltKey, $passwordHash, $otp);

        if ($stmt->execute()) {
            // Send OTP to user's email
            if ($this->sendOtpEmail($email, $otp)) {
                $_SESSION['email'] = $email;
                header("Location: /verify-otp.php");
                exit();
            } else {
                echo "Signup successful, but failed to send OTP email.";
            }
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }

     // Function to resend OTP
     function resendOtp($email) {
        // Check if the email exists
        $stmt = $this->db->prepare("SELECT id FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($userId);
        $stmt->fetch();
        $stmt->close();

        if ($userId) {
            // Generate a new OTP
            $otp = random_int(100000, 999999);

            // Update the OTP in the database
            $stmt = $this->db->prepare("UPDATE user SET otp = ? WHERE email = ?");
            $stmt->bind_param("is", $otp, $email);

            if ($stmt->execute()) {
                // Send the new OTP to the user's email
                if ($this->sendOtpEmail($email, $otp)) {
                    echo "A new OTP has been sent to your email.";
                } else {
                    echo "Failed to send OTP email.";
                }
            } else {
                echo "Error: Unable to update OTP. " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "No user found with this email.";
        }
    }

    // Function to send OTP email using PHPMailer
    private function sendOtpEmail($email, $otp) {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtpout.secureserver.net'; // Replace with your SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'info@gocosys.com'; // Your SMTP username
            $mail->Password = 'Gocosysmail@098'; // Your SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            // Recipients
            $mail->setFrom('info@gocosys.com', 'GoBook');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP for Signup Verification';
            $mail->Body    = "Hello, <br>Your OTP for signup verification is: <b>$otp</b>. <br>Please enter this OTP to verify your account.";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }

    // Function to verify OTP
    function verifyOtp($email, $enteredOtp) {
        // Retrieve the OTP from the database
        $stmt = $this->db->prepare("SELECT otp FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($storedOtp);
        $stmt->fetch();
        $stmt->close();

        if ($storedOtp == $enteredOtp) {
            // OTP matched, activate the account and clear the OTP
            $stmt = $this->db->prepare("UPDATE user SET otp = NULL, is_verified = 1 WHERE email = ?");
            $stmt->bind_param("s", $email);
            if ($stmt->execute()) {
                header("Location: /dashboard.php");
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Invalid OTP. Please try again.";
        }
    }

    // Sign-in function
    function signin($email, $password) {
        // Start session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Fetch user from database
        $stmt = $this->db->prepare("SELECT id, username, email, phone, salt, password, is_verified FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($userId, $username, $email, $phone, $saltKey, $passwordHash, $isVerified);
        $stmt->fetch();
        $stmt->close();

        if ($saltKey && $passwordHash) {
            // Merge entered password with stored salt key
            $saltedPassword = $password . $saltKey;

            // Verify password
            if (password_verify($saltedPassword, $passwordHash)) {
                if ($isVerified) {
                    // Set session variables
                    $_SESSION['user_id'] = $userId;
                    $_SESSION['username'] = $username;
                    $_SESSION['email'] = $email;
                    $_SESSION['phone'] = $phone;

                    echo "Login successful!";
                } else {
                    echo "Your account is not verified. Please verify your email.";
                }
            } else {
                echo "Invalid email or password.";
            }
        } else {
            echo "User not found.";
        }
    }

     // Forgot Password with Verification Token
     public function forgotPasswordWithToken($email) {
        // Check if email exists
        $stmt = $this->db->prepare("SELECT id FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($userId);
        $stmt->fetch();
        $stmt->close();

        if ($userId) {
            // Generate a secure token and expiry time
            $token = bin2hex(random_bytes(32)); // 64-character token
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token valid for 1 hour

            // Save token and expiry in the database
            $stmt = $this->db->prepare("UPDATE user SET password_reset_token = ?, token_expiry = ? WHERE id = ?");
            $stmt->bind_param("ssi", $token, $expiry, $userId);
            $stmt->execute();
            $stmt->close();

            // Generate the password reset link
            $resetLink = "http://project.zeal.ninja/reset_password.php?token=" . $token;

            // Send the email using PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.your-email-provider.com'; // Your SMTP host
                $mail->SMTPAuth = true;
                $mail->Username = 'your-email@example.com'; // Your SMTP username
                $mail->Password = 'your-email-password';    // Your SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Email content
                $mail->setFrom('your-email@example.com', 'Your App Name');
                $mail->addAddress($email);
                $mail->Subject = 'Password Reset Request';
                $mail->Body = "Click the link below to reset your password:\n\n" . $resetLink;

                $mail->send();
                return "A password reset link has been sent to your email.";
            } catch (Exception $e) {
                return "Failed to send reset email: " . $mail->ErrorInfo;
            }
        } else {
            return "Email not found.";
        }
    }

    // Verify Token and Reset Password
    public function verifyTokenAndResetPassword($token, $newPassword) {
        // Check if the token is valid and not expired
        $stmt = $this->db->prepare("SELECT id, token_expiry FROM user WHERE password_reset_token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->bind_result($userId, $tokenExpiry);
        $stmt->fetch();
        $stmt->close();

        if ($userId && strtotime($tokenExpiry) > time()) {
            // Token is valid and not expired
            $saltKey = bin2hex(random_bytes(32)); // Generate a new salt
            $saltedPassword = $newPassword . $saltKey;
            $passwordHash = password_hash($saltedPassword, PASSWORD_BCRYPT);

            // Update password and clear token
            $stmt = $this->db->prepare("UPDATE user SET password = ?, salt = ?, password_reset_token = NULL, token_expiry = NULL WHERE id = ?");
            $stmt->bind_param("ssi", $passwordHash, $saltKey, $userId);
            $stmt->execute();
            $stmt->close();

            return "Your password has been reset successfully.";
        } else {
            return "Invalid or expired token.";
        }
    }
}

?>
