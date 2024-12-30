<?php
session_start();
require_once 'translation_wrapper.php';
wrapWithTranslation(function () {
    require_once 'dbconn.php';
    require_once 'translate.php';
    include 'languageSelector.php';
    $lang = $_SESSION['lang'] ?? 'en';

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

    if ($pref_info) {
        $minage = $pref_info['MinAge'];
        $maxage = $pref_info['MaxAge'];
        $marital_status = $pref_info['MaritalStatus'];
        $education = $pref_info['Education'];
        $age_range = "$minage - $maxage";
        echo "<p>Age Range: $minage - $maxage</p>";
        echo "<p>Marital Status: $marital_status</p>";
        echo "<p>Education: $education</p>";
    } else {
        echo "<p>No preferences found for this user. Please add your preferences.</p>";
        $age_range = '';
        $marital_status = '';
        $education = '';
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $age_range = $_POST['age_range'];
        list($minage, $maxage) = explode('-', $age_range);
        $marital_status = $_POST['marital_status'];
        $education = $_POST['education'];

        $query = $conn->prepare("INSERT INTO preferences (cid, MinAge, MaxAge, MaritalStatus, Education)
                              VALUES (:cid, :minage, :maxage, :marital_status, :education)
                              ON DUPLICATE KEY UPDATE
                              MinAge = :minage, MaxAge = :maxage, MaritalStatus = :marital_status, Education = :education");

        $query->bindParam(':cid', $cid);
        $query->bindParam(':minage', $minage);
        $query->bindParam(':maxage', $maxage);
        $query->bindParam(':marital_status', $marital_status);
        $query->bindParam(':education', $education);

        try {
            $query->execute();
            echo "<p>Preferences updated successfully!</p>";
        } catch (PDOException $e) {
            echo "<p>Error updating preferences: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
    ob_start();
    ?>

    <form action="" method="post">
        <label>Age Range: <input type="text" name="age_range" value="<?php echo htmlspecialchars($age_range); ?>"
                placeholder="e.g., 25-35"></label><br>
        <label>Marital Status: <input type="text" name="marital_status"
                value="<?php echo htmlspecialchars($marital_status); ?>" placeholder="e.g., Single"></label><br>
        <label>Education: <input type="text" name="education" value="<?php echo htmlspecialchars($education); ?>"
                placeholder="e.g., Bachelor’s"></label><br>
        <input type="hidden" name="cid" value="<?php echo htmlspecialchars($cid); ?>">
        <button type="submit" class="btn btn-primary">Save Preferences</button>
    </form>
    <?php
    $pageContent = ob_get_clean();

    // Translate the content
    echo translatePage($pageContent, $lang);

});
?>