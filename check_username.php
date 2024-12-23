<?php
require_once 'dbconn.php';

if(isset($_GET['username'])) {
    $username = trim($_GET['username']);
    
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM login WHERE Username = :username");
    $stmt->execute([':username' => $username]);
    $result = $stmt->fetch();
    
    if($result['count'] > 0){
        echo "exists";
    }else{
        echo "available";
    }
}
