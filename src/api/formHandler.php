<?php

/**
 * Universal request data parser for APIs
 *
 * @return array Parsed request input
 */
function getRequestData(): array
{
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

    // --- GET: always use query parameters ---
    if ($method === 'GET') {
        return $_GET ?? [];
    }

    // --- JSON: application/json ---
    if (stripos($contentType, 'application/json') !== false) {
        $raw = file_get_contents('php://input');
        $json = json_decode($raw, true);
        return is_array($json) ? $json : [];
    }

    // --- Form POST: application/x-www-form-urlencoded ---
    if (stripos($contentType, 'application/x-www-form-urlencoded') !== false) {
        return $_POST ?? [];
    }

    // --- Multipart form POST: multipart/form-data (file uploads) ---
    if (stripos($contentType, 'multipart/form-data') !== false) {
        $data = $_POST ?? [];
        if (!empty($_FILES)) {
            $data['_files'] = $_FILES;
        }
        return $data;
    }

    // --- Raw body fallback ---
    $raw = file_get_contents('php://input');
    if (!empty($raw)) {
        $parsed = [];
        parse_str($raw, $parsed);
        if (is_array($parsed) && !empty($parsed)) {
            return $parsed;
        }
    }

    // If nothing matched, return empty array
    return [];
}

?>