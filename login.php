<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once 'dbconn.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => ''
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        $sql = "SELECT * FROM login WHERE Username = :username AND Password = :password";
        $result = $conn->prepare($sql);
        $result->bindParam(':username', $username, PDO::PARAM_STR);
        $result->bindParam(':password', $password, PDO::PARAM_STR);
        $result->execute();

        if (!$result) {
            $response['message'] = "SQL Error: " . $conn->error;
            echo json_encode($response);
            exit;
        }

        if ($result->rowCount() > 0) {
            $user = $result->fetch(PDO::FETCH_ASSOC);
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['status'] = $user['status'];
            if ($user['status'] != "A") {
                $response['message'] = "Account not Activated";
            } else {
                $response['success'] = true;
                $response['message'] = "Login successful";
            }
        } else {
            $response['message'] = "Invalid username or password";
        }
    } else {
        $response['message'] = "Please fill in all fields";
    }
}

echo json_encode($response);
?>