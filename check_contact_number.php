<?php
require_once 'dbconn.php'; 

header('Content-Type: text/plain'); 

if (isset($_GET['num'])) {
    $contactNumber = trim($_GET['num']);
    
    if (!preg_match('/^\d{10,15}$/', $contactNumber)) {
        echo "invalid";
        exit;
    }

    try {
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM family WHERE contact = :contact");
        $stmt->bindParam(':contact', $contactNumber, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['count'] > 0) {
            echo "exists";
        } else {
            echo "available";
        }
    } catch (PDOException $e) {
        // Log the error message (for debugging; do not expose in production)
        error_log("Database Error: " . $e->getMessage());
        echo "error";
    }
} else {
    echo "no_number";
}
