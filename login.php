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
    <style>
        /* General Reset */
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
            color: #333;
        }

        .login-container {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            max-width: 400px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            padding: 2rem;
            animation: fadeIn 0.8s ease-out;
        }

        .login-card h2 {
            text-align: center;
            font-size: 24px;
            color: #444;
            margin-bottom: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            margin-bottom: 0.5rem;
            color: #666;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            outline: none;
            transition: all 0.3s;
        }

        .form-group input:focus {
            border-color: #6a11cb;
            box-shadow: 0 0 5px rgba(106, 17, 203, 0.3);
        }

        .btn {
            display: block;
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 16px;
            font-weight: bold;
            color: #fff;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
            text-align: center;
        }

        .btn:hover {
            background: linear-gradient(135deg, #2575fc 0%, #6a11cb 100%);
        }

        .footer {
            text-align: center;
            margin-top: 1rem;
            font-size: 14px;
            color: #666;
        }

        .footer a {
            color: #6a11cb;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }

        .footer a:hover {
            color: #2575fc;
        }

        .error, .success {
            text-align: center;
            font-size: 14px;
            padding: 0.5rem 0;
            margin-bottom: 1rem;
        }

        .error {
            color: #ff4d4d;
        }

        .success {
            color: #28a745;
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
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <h2>Login</h2>
            <!-- Error and success messages -->
            <?php if ($error): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <?php if ($success): ?>
                <p class="success"><?php echo $success; ?></p>
            <?php endif; ?>
            <!-- Login Form -->
            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter your username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn">Login</button>
                <div class="footer">
                    <p>Don't have an account? <a href="register_new.php">Sign up</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>