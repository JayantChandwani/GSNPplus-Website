<?php

function translateText($text, $targetLang)
{
    // Add debug logging
    error_log("Translating text to $targetLang: " . substr($text, 0, 100));

    // Only allow specific languages
    $allowedLanguages = ['hi', 'gu', 'mr'];

    if ($targetLang === 'en' || empty(trim($text)) || !in_array($targetLang, $allowedLanguages)) {
        error_log("Skipping translation: lang=$targetLang, empty=" . empty(trim($text)) . ", allowed=" . in_array($targetLang, $allowedLanguages));
        return $text;
    }

    $sourceLang = 'en'; // Assuming original content is in English
    $url = 'https://libretranslate.de/translate';

    $postData = array(
        'q' => $text,
        'source' => $sourceLang,
        'target' => $targetLang,
        'format' => 'text'
    );

    try {
        // Add debug logging for API call
        error_log("Calling LibreTranslate API for text: " . substr($text, 0, 100));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            error_log('Curl error: ' . curl_error($ch));
            curl_close($ch);
            return $text;
        }

        curl_close($ch);
        $result = json_decode($response, true);

        if (isset($result['translatedText'])) {
            error_log("Translation successful");
            return $result['translatedText'];
        }

        error_log("Translation failed - no translated text in response");
        return $text;
    } catch (Exception $e) {
        error_log("Translation error: " . $e->getMessage());
        return $text;
    }
}

function translatePage($html, $targetLang)
{
    if ($targetLang === 'en') {
        return $html;
    }

    // Create a cache directory if it doesn't exist
    $cacheDir = __DIR__ . '/cache';
    if (!file_exists($cacheDir)) {
        mkdir($cacheDir, 0777, true);
    }

    // Generate cache key based on content and target language
    $cacheKey = md5($html . $targetLang);
    $cacheFile = $cacheDir . '/' . $cacheKey . '.html';

    // Check if cached version exists and is less than 24 hours old
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < 86400)) {
        return file_get_contents($cacheFile);
    }

    // Load HTML content
    $doc = new DOMDocument();
    @$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

    // Get all text nodes
    $xpath = new DOMXPath($doc);
    $textNodes = $xpath->query('//text()[normalize-space()]');

    foreach ($textNodes as $node) {
        $parentNode = $node->parentNode;

        // Skip translation for specific elements
        if (in_array(strtolower($parentNode->nodeName), ['script', 'style', 'code', 'pre'])) {
            continue;
        }

        // Skip translation for input values and placeholders
        if (in_array(strtolower($parentNode->nodeName), ['input', 'textarea'])) {
            if ($parentNode->hasAttribute('value') || $parentNode->hasAttribute('placeholder')) {
                continue;
            }
        }

        $text = trim($node->nodeValue);
        if ($text === '' || is_numeric($text)) {
            continue;
        }

        // Translate the text
        $translatedText = translateText($text, $targetLang);
        $node->nodeValue = $translatedText;

        // Add small delay to avoid overwhelming the API
        usleep(100000); // 100ms delay
    }

    $translatedHtml = $doc->saveHTML();

    // Cache the translated content
    file_put_contents($cacheFile, $translatedHtml);

    return $translatedHtml;
}
