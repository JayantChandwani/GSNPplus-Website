<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once 'dbconn.php';

$error = "";
$success = "";

// print_r($_SESSION);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $user_session="";
    $status="";

    if (!empty($username) && !empty($password)) {
        $sql = "SELECT * FROM login WHERE Username = :username AND Password = :password";
        $result = $conn->prepare($sql);
        $result->bindParam(':username', $username, PDO::PARAM_STR);
        $result->bindParam(':password', $password, PDO::PARAM_STR);
        $result->execute();

        if(!$result) {
            die("SQL Error: " . $conn->error);
        }

        if ($result->rowCount() > 0) {
            $_SESSION['username'] = $username;
            $user = $result->fetch(PDO::FETCH_ASSOC);
            $user_session = $user['user_type'];
            $_SESSION['user_session']=$user_session;
            $status=$user['status'];
            $_SESSION['status']=$status;
            $_SESSION['type']="login";
            if($status!="A"){
                $error="Account not Activated";
            }else{
                header('Location: otp_login.php');
            }
            //redirects to be added later according to user_session
        } else {
            $error = "Invalid username or password";
        }
    } else {
        $error = "Please fill in all fields";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>

        <!-- Display Success Message -->
        <?php if (!empty($success)) : ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>

        <!-- Display Error Message -->
        <?php if (!empty($error)) : ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        
    </div>
</body>
</html>