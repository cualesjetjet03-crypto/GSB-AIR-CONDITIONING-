<?php
/**
 * RSVP Submission Handler
 * Receives, validates, and persists RSVP submissions to JSON file
 */

// Set response headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Only POST requests are accepted.'
    ]);
    exit();
}

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Check if JSON is valid
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid JSON format'
    ]);
    exit();
}

// Validate input structure
if (!$data || !isset($data['name']) || !isset($data['guests'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid input. Name and number of guests are required.'
    ]);
    exit();
}

// Sanitize and validate name
$name = trim($data['name']);
if (empty($name)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Name is required and cannot be empty.'
    ]);
    exit();
}

// Limit name length
if (strlen($name) > 200) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Name is too long (maximum 200 characters).'
    ]);
    exit();
}

// Validate guests as positive integer
$guests = filter_var($data['guests'], FILTER_VALIDATE_INT);
if ($guests === false || $guests < 1) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Number of guests must be a positive integer (minimum 1).'
    ]);
    exit();
}

if ($guests > 99) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Number of guests is too large (maximum 99).'
    ]);
    exit();
}

// Sanitize optional message
$message = isset($data['message']) ? trim($data['message']) : '';
if (strlen($message) > 500) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Message is too long (maximum 500 characters).'
    ]);
    exit();
}

// Prepare RSVP entry
$rsvp = [
    'id' => uniqid('rsvp_', true),
    'name' => $name,
    'guests' => $guests,
    'message' => $message,
    'timestamp' => date('Y-m-d H:i:s'),
    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
];

// File path for storing RSVPs
$dataDir = __DIR__ . '/data';
$file = $dataDir . '/rsvps.json';

// Ensure data directory exists
if (!file_exists($dataDir)) {
    if (!mkdir($dataDir, 0755, true)) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create data directory. Please contact the administrator.'
        ]);
        exit();
    }
}

// Load existing RSVPs
$rsvps = [];
if (file_exists($file)) {
    $content = file_get_contents($file);
    if ($content !== false) {
        $rsvps = json_decode($content, true);
        
        // Handle corrupted JSON file
        if ($rsvps === null) {
            error_log('Corrupted RSVP file detected. Creating backup and resetting.');
            // Create backup of corrupted file
            $backupFile = $dataDir . '/rsvps_backup_' . date('YmdHis') . '.json';
            copy($file, $backupFile);
            $rsvps = [];
        }
    }
}

// Ensure $rsvps is an array
if (!is_array($rsvps)) {
    $rsvps = [];
}

// Append new RSVP
$rsvps[] = $rsvp;

// Save to file with file locking to prevent concurrent write issues
$jsonData = json_encode($rsvps, JSON_PRETTY_PRINT);
$success = file_put_contents($file, $jsonData, LOCK_EX);

if ($success === false) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to save RSVP. Please try again later.'
    ]);
    error_log('Failed to write RSVP to file: ' . $file);
    exit();
}

// Set appropriate file permissions
chmod($file, 0644);

// Return success response
echo json_encode([
    'success' => true,
    'message' => 'RSVP received successfully! Thank you for confirming your attendance.',
    'rsvp_id' => $rsvp['id']
]);
?>
