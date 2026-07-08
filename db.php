<?php
/**
 * Database connection bootstrap file
 */
require_once __DIR__ . '/config.php';

// Expose a global $pdo connection
$pdo = getDBConnection();
