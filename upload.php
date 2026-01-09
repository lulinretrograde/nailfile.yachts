<?php
header('Content-Type: application/json');

$uploadDir = 'uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$maxSize = 100 * 1024 * 1024; // 100MB
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'video/mp4', 'video/webm', 'video/quicktime', 'text/plain'];
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'webm', 'mov', 'txt', 'md'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    
    // Check file size
    if ($file['size'] > $maxSize) {
        echo json_encode(['error' => 'File too large (max 100MB)']);
        exit;
    }
    
    // Check extension
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExtensions)) {
        echo json_encode(['error' => 'Invalid file extension']);
        exit;
    }
    
    // Check MIME type (allow text/plain for .txt and .md files)
    if (!in_array($file['type'], $allowedTypes) && !in_array($ext, ['txt', 'md'])) {
        echo json_encode(['error' => 'Only images, videos, and text files allowed']);
        exit;
    }
    
    // Generate unique filename
    $filename = uniqid() . '.' . $ext;
    $destination = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        $url = 'https://nailfile.yachts/' . $filename;
        echo json_encode(['url' => $url]);
    } else {
        echo json_encode(['error' => 'Upload failed']);
    }
} else {
    echo json_encode(['error' => 'No file uploaded']);
}
?>
