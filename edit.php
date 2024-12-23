<?php
session_start();
require_once 'dbconn.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$message = '';


$sql = "SELECT cid FROM login WHERE Username = :username";
$res = $conn->prepare($sql);
$res->bindParam(':username', $username, PDO::PARAM_STR);
$res->execute();
$user_info = $res->fetch(PDO::FETCH_ASSOC);
$cid = $user_info['cid'];


function sanitize_input($input) {
    $input = is_string($input) ? trim($input) : '';
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = sanitize_input($_POST['username']);
    $new_email = sanitize_input($_POST['email']);
   
    try {
            $conn->beginTransaction();
           
            $update_stmt = $conn->prepare("UPDATE login SET Username = :username, Email = :email WHERE cid = :cid");
            $update_stmt->bindParam(':username', $new_username, PDO::PARAM_STR);
            $update_stmt->bindParam(':email', $new_email, PDO::PARAM_STR);
            $update_stmt->bindParam(':cid', $cid, PDO::PARAM_STR);
            $update_stmt->execute();
            
            $conn->commit();
            $_SESSION['username'] = $new_username;
            header("Location: profile.php");
            exit();
            
        } catch (PDOException $e) {
            $conn->rollBack();
            $message = "Error updating profile: " . $e->getMessage();
        }
    
}


$stmt = $conn->prepare("SELECT Username, Email FROM login WHERE cid = :cid");
$stmt->bindParam(':cid', $cid, PDO::PARAM_STR);
$stmt->execute();
$user_data = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <style>
        .error { color: red; font-size: 0.8em; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Profile</h1>
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="edit.php" id="editForm">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" 
                       value="<?php echo sanitize_input($user_data['Username']) ?>">
                <span class="error" id="username-error"></span>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" 
                       value="<?php echo sanitize_input($user_data['Email']) ?>">
                <span class="error" id="email-error"></span>
            </div>
            
            <div class="form-buttons">
                <button type="submit">Save Changes</button>
                <a href="profile.php">Cancel</a>
            </div>
        </form>
    </div>
    <script src="validations.js"></script>
</body>
</html>
