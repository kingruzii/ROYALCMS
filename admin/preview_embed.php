<?php
require_once __DIR__ . '/embed_helper.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['url'])) {
    $url = trim($_POST['url']);
    $type = detectEmbedType($url);
    
    if ($type) {
        $html = generateEmbedHtml($url, $type);
        echo json_encode(['success' => true, 'html' => $html, 'type' => $type]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Unsupported URL type']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No URL provided']);
}
?>