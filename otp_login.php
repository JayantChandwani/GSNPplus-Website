<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'dbconn.php';
require_once 'send_otp.php';

session_start();

if ($_SESSION['type'] == "register") {
    $show_change_email_option = true;
} else {
    $show_change_email_option = false;
}
//print_r($_SESSION['type']);
//print_r($show_change_email_option);
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

   // echo "user_email found" . $user_email;

    $secret = uniqid();
    $timestamp = time();
    $input = $secret . $timestamp;
    $otp = generateOTP($input);

    $_SESSION['otp'] = $otp; // Store OTP in session
    $_SESSION['otp_generated'] = true; // Flag to avoid regenerating OTP

    sendOTP($user_email, $otp);
}

// Handle Change Email Request
if (isset($_POST['change_email_form'])) {
    $new_email = sanitize_input($_POST['new_email']);
    $username = $_SESSION['username'];

    // Update email in the database
    try {
        $sql = "UPDATE login SET Email = :new_email WHERE Username = :username";
        $res = $conn->prepare($sql);
        $res->bindParam(':new_email', $new_email, PDO::PARAM_STR);
        $res->bindParam(':username', $username, PDO::PARAM_STR);
        $res->execute();

        // Resend OTP to new email
        $secret = uniqid();
        $timestamp = time();
        $input = $secret . $timestamp;
        $otp = generateOTP($input);

        $_SESSION['otp'] = $otp; // Update OTP in session
        $_SESSION['otp_generated'] = true; // Reset OTP flag
        $_SESSION['Email'] = $new_email; // Update session email

        sendOTP($new_email, $otp);
        $success = "Email updated successfully. OTP sent to the new email.";
    } catch (Exception $e) {
        $failure = "Error updating email: " . $e->getMessage();
    }
} elseif (isset($_POST['otp'])) {
    // Handle OTP validation
    $entered_otp = sanitize_input($_POST['otp']);
    $stored_otp = $_SESSION['otp'];

    if ($entered_otp == $stored_otp) {
        $success = "Logged in Successfully";

        // Clear OTP session variables
        unset($_SESSION['otp']);
        unset($_SESSION['otp_generated']);

        // Redirect to profile or confirmation page
        if ($_SESSION['type']=="login") {
            header('Location: profile.php');
        } else {
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
    <title>OTP Verification - HIV/AIDS Matrimony</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-image: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .otp-container {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .otp-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .otp-header h1 {
            color: #333;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .otp-header p {
            color: #666;
            font-size: 1rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        .form-group input:focus {
            outline: none;
            border-color: #4a90e2;
            box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.2);
        }
        .btn {
            width: 100%;
            padding: 0.75rem;
            background-color: #4a90e2;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #3a7bc8;
        }
        .btn-secondary {
            background-color: #6c757d;
            margin-top: 1rem;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .error {
            color: #d32f2f;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            text-align: center;
        }
        .success {
            color: #388e3c;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            text-align: center;
        }
        #change-email-div {
            margin-top: 1rem;
        }
    </style>
    <script>
        function toggleChangeEmail() {
            const changeEmailDiv = document.getElementById('change-email-div');
            changeEmailDiv.style.display = changeEmailDiv.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</head>
<body>
    <div>
        

        <div class="otp-container">
        <div class="otp-header">
            <h1>OTP Verification</h1>
            <p>Please enter the OTP sent to your email</p>
        </div>

        <?php if (!empty($success)) : ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>

        <?php if (!empty($failure)) : ?>
            <div class="error"><?= $failure ?></div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <input type="text" name="otp" placeholder="Enter OTP" required>
            </div>
            <button type="submit" class="btn">Verify OTP</button>
        </form>

        <?php if ($show_change_email_option) : ?>
            <button onclick="toggleChangeEmail()" class="btn btn-secondary">Change Email ID</button>
            <div id="change-email-div" style="display: none;">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <input type="hidden" name="change_email_form" value="1">
                    <div class="form-group">
                        <input type="email" name="new_email" placeholder="Enter new email" required>
                    </div>
                    <button type="submit" class="btn">Change Email</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>