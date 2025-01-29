<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "dbconn.php";
session_start();

function sanitize_input($input) {
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

$sql = "SELECT Username, Email, status, user_type FROM login WHERE cid = :cid";
$res = $conn->prepare($sql);
$res->bindParam(':cid', $user_cid, PDO::PARAM_STR);
$res->execute();
$user_info = $res->fetch(PDO::FETCH_ASSOC);

$username = sanitize_input($user_info['Username']);
$email = sanitize_input($user_info['Email']);
$status = $user_info['status'] == "A" ? "Active" : "Passive";
$user_type = sanitize_input($user_info['user_type']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - GSNP+ Matrimony</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: #333;
            min-height: 100vh;
            padding: 2rem;
        }

        .profile-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .profile-header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: #fff;
            padding: 1.5rem;
            text-align: center;
        }

        .profile-header h1 {
            font-size: 24px;
            margin-bottom: 0.5rem;
        }

        .tabs {
            display: flex;
            background-color: #f8f9fa;
            border-bottom: 1px solid #ddd;
        }

        .tab-button {
            flex-grow: 1;
            background: none;
            border: none;
            outline: none;
            cursor: pointer;
            padding: 1rem;
            font-size: 16px;
            color: #666;
            transition: all 0.3s;
        }

        .tab-button:hover, .tab-button.active {
            background-color: #fff;
            color: #6a11cb;
        }

        .tab-button.active {
            border-bottom: 3px solid #6a11cb;
        }

        .tab-content {
            display: none;
            padding: 2rem;
        }

        .tab-content.active {
            display: block;
        }

        .profile-info {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .profile-info p {
            margin: 0.5rem 0;
            font-size: 16px;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1rem;
            font-size: 16px;
            font-weight: bold;
            color: #fff;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
            text-decoration: none;
            text-align: center;
        }

        .btn:hover {
            background: linear-gradient(135deg, #2575fc 0%, #6a11cb 100%);
        }

        .footer {
            text-align: center;
            padding: 1rem;
            background-color: #f8f9fa;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <h1>HIV Matrimony</h1>
            <p>Welcome, <?php echo $username; ?>!</p>
        </div>
        <div class="tabs">
            <button class="tab-button active" onclick="openTab(event, 'profileTab')">Profile</button>
            <button class="tab-button" onclick="openTab(event, 'preferencesTab')">Preferences</button>
            <button class="tab-button" onclick="openTab(event, 'selectedProfilesTab')">Selected Profiles</button>
            <button class="tab-button" onclick="openTab(event, 'listOfProfilesTab')">List of Profiles</button>
        </div>

        <div id="profileTab" class="tab-content active">
            <h2>User Profile</h2>
            <div class="profile-info">
                <p><strong>Username:</strong> <?php echo $username; ?></p>
                <p><strong>Email:</strong> <?php echo $email; ?></p>
                <p><strong>Status:</strong> <?php echo $status; ?></p>
                <p><strong>User Type:</strong> <?php echo $user_type; ?></p>
            </div>
            <a href="edit.php" class="btn">Edit Profile</a>
        </div>

        <div id="preferencesTab" class="tab-content">
            <h2>Preferences</h2>
            <?php include 'preferences.php'; ?>
        </div>

        <div id="selectedProfilesTab" class="tab-content">
            <h2>Selected Profiles</h2>
            <?php include 'selected_profiles.php'; ?>
        </div>

        <div id="listOfProfilesTab" class="tab-content">
            <h2>List of Profiles</h2>
            <?php include 'profile_listing.php'; ?>
        </div>

        <div class="footer">
            <a href="logout.php" class="btn">Logout</a>
        </div>
    </div>

    <script>
        function openTab(evt, tabName) {
            var i, tabContent, tabButtons;
            tabContent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabContent.length; i++) {
                tabContent[i].style.display = "none";
            }
            tabButtons = document.getElementsByClassName("tab-button");
            for (i = 0; i < tabButtons.length; i++) {
                tabButtons[i].className = tabButtons[i].className.replace(" active", "");
            }
            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.className += " active";
        }
    </script>
</body>
</html>

