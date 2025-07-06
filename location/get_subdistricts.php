<?php
require_once __DIR__.'/../includes/config.php';
require_once __DIR__.'/../includes/csrf.php';
require_once __DIR__.'/../includes/functions.php';

try {
    // Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST requests allowed');
    }

    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
        throw new Exception('Invalid CSRF token');
    }

    // Validate input
    if (empty($_POST['district_code'])) {
        throw new Exception('District code not provided');
    }

    $subdistricts = fetchSubdistricts($_POST['district_code']);
    
    $options = '<option value="">Select Subdistrict</option>';
    foreach ($subdistricts as $subdistrict) {
        $options .= sprintf(
            '<option value="%s">%s</option>',
            htmlspecialchars($subdistrict['subdistrict_code']),
            htmlspecialchars($subdistrict['subdistrict_name'])
        );
    }

    header('Content-Type: text/html');
    echo $options;

} catch (Exception $e) {
    header('Content-Type: text/html');
    echo '<option value="">Error: ' . htmlspecialchars($e->getMessage()) . '</option>';
}