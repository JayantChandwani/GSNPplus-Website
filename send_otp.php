<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'vendor/autoload.php';

    function sendOTP($email, $otp) {

        $message_body = "One Time Password for authentication is:<br/><br/>".$otp;
        $mail = new PHPMailer();
        
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'adityachaudhary09080706@gmail.com';
            $mail->Password = 'xnshqtexyijatosi';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('adityachaudhary09080706@gmail.com', 'TEST');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'One Time Password for Verification';
            $mail->Body = $message_body;

            $mail->send();
            echo 'OTP sent successfully.';
        } catch (Exception $e) {
            echo "Failed to send email. Error: {$mail->ErrorInfo}";
        }
    }
    // echo "Generated otp: $otp";
    // sendOTP('p13821963@gmail.com', $otp);
?>