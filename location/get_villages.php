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

    if (empty($_POST['subdistrict_code'])) {
        throw new Exception('Subdistrict code not provided');
    }

    $villages = fetchVillages($_POST['subdistrict_code']);
    
    $options = '<option value="">Select Village</option>';
    foreach ($villages as $village) {
        $options .= sprintf(
            '<option value="%s">%s</option>',
            htmlspecialchars($village['village_code']),
            htmlspecialchars($village['village_name'])
        );
    }

    header('Content-Type: text/html');
    echo $options;

} catch (Exception $e) {
    header('Content-Type: text/html');
    echo '<option value="">Error: ' . htmlspecialchars($e->getMessage()) . '</option>';
}
