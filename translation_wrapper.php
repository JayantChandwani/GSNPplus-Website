<?php

require_once 'translate.php';

// Function to wrap content with translation
function wrapWithTranslation($callback)
{
    // Start output buffering
    ob_start();

    // Include language selector at the top of every page
    include 'languageSelector.php';

    // Execute the page's content
    $callback();

    // Get the buffered content
    $pageContent = ob_get_clean();

    // Get current language
    $lang = $_SESSION['lang'] ?? 'en';

    // Debug logging
    error_log("Current language: $lang");
    error_log("Content length before translation: " . strlen($pageContent));

    // Translate and output the content
    $translatedContent = translatePage($pageContent, $lang);
    error_log("Content length after translation: " . strlen($translatedContent));

    echo $translatedContent;
}
?>