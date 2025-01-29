<?php
// session_start();
require_once 'dbconn.php';

$username = $_SESSION['username'];
$cidqry = "SELECT cid FROM login WHERE username = :username";
$res = $conn->prepare($cidqry);
$res->bindParam(':username', $username, PDO::PARAM_STR);
$res->execute();
$info = $res->fetch(PDO::FETCH_ASSOC);
$cid = $info['cid'] ?? null;

$query = $conn->prepare("SELECT MinAge, MaxAge, MaritalStatus, Education FROM preferences WHERE cid = :cid");
$query->bindParam(':cid', $cid);
$query->execute();
$pref_info = $query->fetch(PDO::FETCH_ASSOC);

$minage = $pref_info['MinAge'] ?? '';
$maxage = $pref_info['MaxAge'] ?? '';
$marital_status = $pref_info['MaritalStatus'] ?? '';
$education = $pref_info['Education'] ?? '';
$update_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $age_range = $_POST['age_range'];
    list($minage, $maxage) = explode('-', $age_range);
    $marital_status = $_POST['marital_status'];
    $education = $_POST['education'];

    $query = $conn->prepare("INSERT INTO preferences (cid, MinAge, MaxAge, MaritalStatus, Education)
                              VALUES (:cid, :minage, :maxage, :marital_status, :education)
                              ON DUPLICATE KEY UPDATE
                              MinAge = :minage1, MaxAge = :maxage1, MaritalStatus = :marital_status1, Education = :education1");

    $query->bindParam(':cid', $cid);
    $query->bindParam(':minage', $minage);
    $query->bindParam(':minage1', $minage);
    $query->bindParam(':maxage', $maxage);
    $query->bindParam(':maxage1', $maxage);
    $query->bindParam(':marital_status', $marital_status);
    $query->bindParam(':marital_status1', $marital_status);
    $query->bindParam(':education', $education);
    $query->bindParam(':education1', $education);
    
    try {
        $query->execute();
        $update_success = true; // Set success flag
    } catch (PDOException $e) {
        echo "<p class='error'>Error updating preferences: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

<style>
    .preferences-container {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.1);
        padding: 2rem;
        max-width: 400px;
        margin: 0 auto;
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

    .form-group input.locked {
        background-color: #f8f9fa;
        color: #666;
        cursor: not-allowed;
    }

    .form-group input.unlocked {
        background-color: #fff;
        color: #333;
        cursor: text;
    }

    .btn {
        display: inline-block;
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
        margin-top: 1rem;
    }

    .btn:hover {
        background: linear-gradient(135deg, #2575fc 0%, #6a11cb 100%);
    }

    .btn-secondary {
        background: #f8f9fa;
        color: #333;
        border: 1px solid #ddd;
    }

    .btn-secondary:hover {
        background: #e9ecef;
    }

    .success {
        color: #28a745;
        text-align: center;
        margin-bottom: 1rem;
    }

    .error {
        color: #dc3545;
        text-align: center;
        margin-bottom: 1rem;
    }
</style>

<div class="preferences-container">
    <h2>Your Preferences</h2>
    
    <?php if ($update_success): ?>
        <p class="success">Preferences updated successfully!</p>
    <?php endif; ?>

    <form action="" method="post">
        <div class="form-group">
            <label for="age_range">Age Range:</label>
            <input type="text" id="age_range" name="age_range" value="<?php echo htmlspecialchars("$minage - $maxage"); ?>" placeholder="e.g., 25-35" required readonly class="locked">
        </div>
        <div class="form-group">
            <label for="marital_status">Marital Status:</label>
            <input type="text" id="marital_status" name="marital_status" value="<?php echo htmlspecialchars($marital_status); ?>" placeholder="e.g., Single" required readonly class="locked">
        </div>
        <div class="form-group">
            <label for="education">Education:</label>
            <input type="text" id="education" name="education" value="<?php echo htmlspecialchars($education); ?>" placeholder="e.g., Bachelor's" required readonly class="locked">
        </div>
        <button type="submit" class="btn" style="display:none;" id="saveButton">Save Preferences</button>
    </form>
    <button type="button" class="btn btn-secondary" onclick="unlockPreferences()">Update Preferences</button>
</div>

<script>
    function unlockPreferences() {
        const inputs = document.querySelectorAll('.form-group input');
        const saveButton = document.getElementById('saveButton');
        const updateButton = document.querySelector('.btn-secondary');
        
        inputs.forEach(input => {
            input.removeAttribute('readonly');
            input.classList.remove('locked');
            input.classList.add('unlocked');
        });
        
        saveButton.style.display = 'inline-block';
        updateButton.style.display = 'none';
    }
</script>

