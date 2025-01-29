<?php
session_start();
require_once 'dbconn.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$message = '';

// Get user CID
$sql = "SELECT cid FROM login WHERE Username = :username";
$res = $conn->prepare($sql);
$res->bindParam(':username', $username, PDO::PARAM_STR);
$res->execute();
$user_info = $res->fetch(PDO::FETCH_ASSOC);
$cid = $user_info['cid'];

function sanitize_input($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = sanitize_input($_POST['username']);
    $new_email = sanitize_input($_POST['email']);
   
    try {
        $conn->beginTransaction();
       
        $update_stmt = $conn->prepare("UPDATE login SET Username = :username, Email = :email WHERE cid = :cid");
        $update_stmt->bindParam(':username', $new_username);
        $update_stmt->bindParam(':email', $new_email);
        $update_stmt->bindParam(':cid', $cid);
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

// Get current user data
$stmt = $conn->prepare("SELECT Username, Email FROM login WHERE cid = :cid");
$stmt->bindParam(':cid', $cid);
$stmt->execute();
$user_data = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - GSNP+ Matrimony</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        /* Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            line-height: 1.6;
        }

        h1, h2, h3 {
            font-family: 'Playfair Display', serif;
        }

        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        /* Header Styles */
        header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: #fff;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo img {
            height: 70px;
            width: auto;
        }

        .logo-text span:first-child {
            font-size: 1.8rem;
            font-weight: 600;
        }

        .logo-text span:last-child {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        nav ul {
            display: flex;
            gap: 30px;
            list-style: none;
            align-items: center;
        }

        nav a {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        nav a:hover {
            background: rgba(255,255,255,0.1);
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2c3e50;
            font-weight: 500;
        }

        input {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        input:focus {
            outline: none;
            border-color: #6a11cb;
            box-shadow: 0 0 0 3px rgba(106,17,203,0.1);
        }

        .form-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        button {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 0.8rem 2rem;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 500;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(106,17,203,0.3);
        }

        a.cancel-btn {
            color: #6a11cb;
            text-decoration: none;
            padding: 0.8rem 2rem;
            border: 2px solid #6a11cb;
            border-radius: 25px;
            transition: all 0.3s;
        }

        a.cancel-btn:hover {
            background: #6a11cb;
            color: white;
        }

        #usernameFeedback {
            display: block;
            margin-top: 0.5rem;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .message {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 8px;
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                margin: 1rem;
                padding: 1.5rem;
            }

            .header-content {
                flex-direction: column;
                gap: 1.5rem;
            }

            nav ul {
                flex-wrap: wrap;
                justify-content: center;
                gap: 15px;
            }

            .form-buttons {
                flex-direction: column;
            }

            button, a.cancel-btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo">
                <img src="logoGSNP.png" alt="GSNP+ Matrimony Logo">
                <div class="logo-text">
                    <span>GSNP+ Matrimony</span>
                    <span>Finding Love with Understanding</span>
                </div>
            </div>
            <nav>
                <ul>
                    <li><a href="profile.php">Back to Profile</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <h1>Edit Your Profile</h1>
        
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="POST" action="edit.php" id="editForm">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" 
                    value="<?php echo sanitize_input($user_data['Username']) ?>" 
                    onkeyup="checkUsername()">
                <span id="usernameFeedback"></span>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" 
                    value="<?php echo sanitize_input($user_data['Email']) ?>">
            </div>

            <div class="form-buttons">
                <button type="submit">Save Changes</button>
                <a href="profile.php" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </div>

    <script>
    function checkUsername() {
        const username = document.getElementById("username").value;
        const feedback = document.getElementById("usernameFeedback");

        if (username.length === 0) {
            feedback.textContent = "";
            return;
        }

        fetch(`check_username.php?username=${encodeURIComponent(username)}`)
            .then(response => response.text())
            .then(data => {
                if (data === "exists") {
                    feedback.textContent = "✗ Username not available";
                    feedback.style.color = "#dc3545";
                } else {
                    feedback.textContent = "✓ Username available";
                    feedback.style.color = "#28a745";
                }
            })
            .catch(error => console.error("Error:", error));
    }
    </script>
</body>
</html>