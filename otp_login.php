<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'dbconn.php';
require_once 'send_otp.php';

session_start();

print_r($_SESSION);

function generateOTP($input, $length = 6) {
    $hash = md5($input);
    $numericHash = preg_replace('/[^0-9]/', '', $hash);

    while (strlen($numericHash) < $length) {
        $numericHash .= rand(0, 9);
    }

    return substr($numericHash, 0, $length);
}

function sanitize_input($input) {
    $input = is_string($input) ? trim($input) : '';
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

$success = "";
$failure = "";


// Generate OTP and send it only on the first page load
if (!isset($_SESSION['otp_generated'])) {
    $username = $_SESSION['username'];
    $user_email = "";
    
    // echo "here";
    // echo $_SESSION;

    if(!isset($_SESSION['Email'])){
        $sql = "SELECT Email FROM login WHERE Username = :username";
        $res = $conn->prepare($sql);
        $res->bindParam(':username', $username, PDO::PARAM_STR);
        $res->execute();
        $user_info = $res->fetch(PDO::FETCH_ASSOC);
        $user_email = $user_info['Email'];
    }
    else{
        $user_email = $_SESSION['Email'];
    }

    echo "user_email found" . $user_email;

    $secret = uniqid();
    $timestamp = time();
    $input = $secret . $timestamp;
    $otp = generateOTP($input);

    $_SESSION['otp'] = $otp; // Store OTP in session
    $_SESSION['otp_generated'] = true; // Flag to avoid regenerating OTP

    sendOTP($user_email, $otp);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $entered_otp = sanitize_input($_POST['otp']);
    $stored_otp = $_SESSION['otp'];

    if ($entered_otp == $stored_otp) {
        $success = "Logged in Successfully";

        // Clear OTP session variables
        unset($_SESSION['otp']);
        unset($_SESSION['otp_generated']);

        // Redirect to index.php
        if(!isset($_SESSION['Email'])){
            header('Location: profile.php');
        }
        else{
            header('Location: confirm.html');
        }
        exit();
    } else {
        $failure = "Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
</head>
<body>
    <div>
        <h2>OTP</h2>

        <!-- Display Success Message -->
        <?php if (!empty($success)) : ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>

        <!-- Display Error Message -->
        <?php if (!empty($failure)) : ?>
            <div class="error"><?= $failure ?></div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="text" name="otp" placeholder="Enter OTP" required>
            <button type="submit">Login</button>
        </form>
        
    </div>
</body>
</html>