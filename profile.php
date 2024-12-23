<?php

require_once "dbconn.php";
session_start();
function sanitize_input($input) {
    // Ensure $input is a string before applying trim
    $input = is_string($input) ? trim($input) : '';
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}


$success = "";
$failure = "";


if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$username = $_SESSION['username'];

$sql = "SELECT cid FROM login WHERE Username = :username";
$res = $conn->prepare($sql);
$res->bindParam(':username', $username, PDO::PARAM_STR);
$res->execute();
$user_info = $res->fetch(PDO::FETCH_ASSOC);
$user_cid = $user_info['cid'];

if (!$user_cid) {
    echo "User not found.";
    exit();
}
$sql = "SELECT Username,Email,status,user_type FROM login WHERE cid = :cid";
$res = $conn->prepare($sql);
$res->bindParam(':cid', $user_cid, PDO::PARAM_STR);
$res->execute();
$user_info = $res->fetch(PDO::FETCH_ASSOC);
$username=$user_info['Username'];
$email=$user_info['Email'];
if($user_info['status']=="A"){
    $status="Active";
}else{
    $status="Passive";
}
$user_type=$user_info['user_type'];



?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
</head>
<body>
    <div class="container">
        <h1>User Profile</h1>
        <div class="profile-info">
            <p><strong>Username:</strong> <?php echo sanitize_input($username) ?></p>
            <p><strong>Email:</strong> <?php echo sanitize_input($email) ?></p>
            <p><strong>Status:</strong> <?php echo sanitize_input($status) ?></p>
            <p><strong>User Type:</strong> <?php echo sanitize_input($user_type) ?></p>
        </div>
        <a href="edit.php" class="edit-btn">Edit Profile</a>
        <a href="logout.php" class="edit-btn">Logout</a>
    </div>
</body>
</html>

