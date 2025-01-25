<?php
// Allow requests from your React app (running at http://localhost:3000
// Enable CORS if required
header('Access-Control-Allow-Origin: http://localhost:3000');  // Replace with your actual frontend URL
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once 'dbconn.php';

header('Content-Type: application/json'); // Send the correct content-type for JSON
$rawInputData = file_get_contents("php://input");

// Decode the JSON data into an associative array
$inputData = json_decode($rawInputData, true);
$username = $inputData['username'];
$password = $inputData['password'];
try {
            // Use a prepared statement for security
            $sql = "SELECT * FROM login WHERE Username = :username AND Password = :password";
            $result = $conn->prepare($sql);
            $result->bindParam(':username', $username, PDO::PARAM_STR);
            $result->bindParam(':password', $password, PDO::PARAM_STR);
            $result->execute();

            // Check for SQL errors
            if ($result->rowCount() > 0) {
                $user = $result->fetch(PDO::FETCH_ASSOC);
                echo json_encode([
                    'success'=>true,
                    'message'=>'Login successful',
                ]);
            } else {
                echo json_encode([
                    'success'=>false,
                    'message'=>'Invalid username or password',
                ]);
            }
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage(),
            ]);
        }

?>
