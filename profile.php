<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'translation_wrapper.php';
wrapWithTranslation(function () {

    require_once "dbconn.php";
    require 'translate.php';
    include 'languageSelector.php';
    $lang = $_SESSION['lang'] ?? 'en';
    session_start();
    function sanitize_input($input)
    {
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
    $username = $user_info['Username'];
    $email = $user_info['Email'];
    if ($user_info['status'] == "A") {
        $status = "Active";
    } else {
        $status = "Passive";
    }
    $user_type = $user_info['user_type'];


    ob_start();
    ?>


    <!-- <!DOCTYPE html>
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
</html> -->

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>User Dashboard</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <style>
            .tab-content {
                margin-top: 20px;
            }
        </style>
        <script>
            function showTab(tabId) {
                const tabs = document.querySelectorAll('.tab-content');
                tabs.forEach(tab => tab.style.display = 'none');
                document.getElementById(tabId).style.display = 'block';
            }
        </script>
    </head>

    <body>
        <div class="container mt-4">
            <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#" onclick="showTab('profileTab')">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="showTab('preferencesTab')">Preferences</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="showTab('selectedProfilesTab')">Selected Profiles</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="showTab('listOfProfilesTab')">List of Profiles</a>
                </li>
            </ul>
            <div class="tab-content" id="profileTab" style="display: block;">
                <?php include 'profile_listing.php'; ?>
            </div>
            <div class="tab-content" id="preferencesTab" style="display: none;">
                <?php include 'preferences.php'; ?>
            </div>
            <div class="tab-content" id="selectedProfilesTab" style="display: none;">
                <?php include 'selected_profiles.php'; ?>
            </div>
            <a href="edit.php" class="edit-btn">Edit Profile</a>
            <br>
            <a href="logout.php" class="edit-btn">Logout</a>
        </div>
    </body>

    </html>
    <?php
    $pageContent = ob_get_clean();

    // Translate the content
    echo translatePage($pageContent, $lang);

});
?>