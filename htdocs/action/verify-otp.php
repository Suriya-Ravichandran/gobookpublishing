<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
// Include the classes
require_once '../lib/database.class.php';
require_once '../lib/user.class.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verfiy-otp'])) {
    // Create a database object and get the connection
    $db = new database();
    $conn = $db->getconnection(); // Get the connection

    // Create a user object and pass the database connection
    $user = new user($conn);

    // Collect form data
    $otp = $_POST['otp'];
    $email=htmlspecialchars($_SESSION['email']);
    // Call the signup function
    $user->verifyOtp($email, $otp);
} else {
    // Redirect to signup page if not a POST request
    header("Location: /signin.php");
    exit();
}
?>
