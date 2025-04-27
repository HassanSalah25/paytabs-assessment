<?php
// Start output buffering
ob_start();

// Start PHP session (if needed later)
session_start();

// Load routes
require_once __DIR__ . '/routes/web.php';

// End and flush output
ob_end_flush();
