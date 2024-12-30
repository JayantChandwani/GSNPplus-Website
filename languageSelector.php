<?php
$supportedLanguages = [
    'en' => 'English',
    'hi' => 'हिंदी', // Hindi
    'gu' => 'ગુજરાતી', // Gujarati
    'mr' => 'मराठी', // Marathi
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedLang = $_POST['lang'] ?? 'en';
    error_log("Language selection attempted: $selectedLang");

    if (array_key_exists($selectedLang, $supportedLanguages)) {
        $_SESSION['lang'] = $selectedLang;
        error_log("Language set to: " . $_SESSION['lang']);
    }

    // Redirect to the same page to show translated content
    $redirect = $_SERVER['HTTP_REFERER'] ?? 'index.php';
    header('Location: ' . $redirect);
    exit;
}

$selectedLang = $_SESSION['lang'] ?? 'en';
error_log("Current language from session: $selectedLang");
?>

<!-- Updated language selector dialog with native language names -->
<div id="language-selector-dialog"
    style="position: fixed; top: 10px; right: 10px; background: #f9f9f9; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); font-family: Arial, sans-serif; font-size: 14px; z-index: 1000;">
    <form method="POST" style="margin: 0;">
        <label for="lang" style="display: block; margin-bottom: 5px;">भाषा चुनें / Select Language:</label>
        <select name="lang" id="lang" onchange="this.form.submit()"
            style="width: 100%; padding: 5px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 3px;">
            <?php foreach ($supportedLanguages as $code => $language): ?>
                <option value="<?= $code ?>" <?= $selectedLang === $code ? 'selected' : '' ?>><?= $language ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</div>