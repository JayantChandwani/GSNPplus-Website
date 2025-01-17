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
        echo "<p>Error updating preferences: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

<div>
    <?php if ($update_success): ?>
        <p class="alert alert-success">Preferences updated successfully!</p>
    <?php endif; ?>

    <form action="" method="post">
        <label>Age Range: 
            <input type="text" name="age_range" value="<?php echo htmlspecialchars("$minage - $maxage"); ?>" placeholder="e.g., 25-35" required readonly class="locked">
        </label><br>
        <label>Marital Status: 
            <input type="text" name="marital_status" value="<?php echo htmlspecialchars($marital_status); ?>" placeholder="e.g., Single" required readonly class="locked">
        </label><br>
        <label>Education: 
            <input type="text" name="education" value="<?php echo htmlspecialchars($education); ?>" placeholder="e.g., Bachelor’s" required readonly class="locked">
        </label><br>
        <button type="submit" class="btn btn-primary" style="display:none;" id="saveButton">Save Preferences</button>
    </form>
    <button type="button" class="btn btn-secondary" onclick="unlockPreferences()">Update Preferences</button>
</div>

<script>
    function unlockPreferences() {
        const inputs = document.querySelectorAll('input[type="text"]');
        const saveButton = document.getElementById('saveButton');
        
        inputs.forEach(input => {
            input.removeAttribute('readonly');
            input.classList.remove('locked');
            input.classList.add('unlocked'); 
        });
        
        saveButton.style.display = 'inline';
    }
</script>

<style>
    input.locked {
        background-color: #f0f0f0; 
        color: #999; 
        cursor: not-allowed;
    }
    input.unlocked {
        background-color: #ffffff;
        color: #000;
        cursor: text;
    }
</style>