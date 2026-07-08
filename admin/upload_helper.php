<?php
/**
 * Upload Helper — reusable file upload functions for admin pages.
 * Include this file wherever file uploads are needed.
 */

function handleAdminUpload($fileInputName, $subfolder = '', $options = []) {
    $defaults = [
        'maxSize'      => 5 * 1024 * 1024,
        'allowedTypes' => ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'],
    ];
    $options = array_merge($defaults, $options);

    if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'url' => '', 'error' => 'No file uploaded or upload error occurred.'];
    }

    $file = $_FILES[$fileInputName];

    $finfo    = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $options['allowedTypes'])) {
        return ['success' => false, 'url' => '', 'error' => 'Invalid file type. Allowed: JPG, PNG, GIF, WebP'];
    }

    if ($file['size'] > $options['maxSize']) {
        return ['success' => false, 'url' => '', 'error' => 'File too large. Maximum: ' . ($options['maxSize'] / 1024 / 1024) . 'MB'];
    }

    $uploadBase = __DIR__ . '/../uploads/';
    $targetDir  = $uploadBase . ($subfolder ? trim($subfolder, '/') . '/' : '');

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;

    if (move_uploaded_file($file['tmp_name'], $targetDir . $filename)) {
        $url = BASE_PATH . '/uploads/' . ($subfolder ? trim($subfolder, '/') . '/' : '') . $filename;
        return ['success' => true, 'url' => $url, 'error' => ''];
    }

    return ['success' => false, 'url' => '', 'error' => 'Failed to save uploaded file.'];
}

function deleteUploadedFile($fileUrl) {
    if (empty($fileUrl)) return true;
    $filePath = str_replace(BASE_PATH, __DIR__ . '/..', $fileUrl);
    $filePath = str_replace('/', DIRECTORY_SEPARATOR, $filePath);
    if (file_exists($filePath) && is_file($filePath)) {
        return unlink($filePath);
    }
    return true;
}
