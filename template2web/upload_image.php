<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(403);
    echo 'Unauthorized';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imageWidth = $_POST['width'];
    $imageHeight = $_POST['height'];
    $imageSrc = $_POST['src'];
    $imageFile = $_FILES['image'];

    if ($imageFile['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo 'File upload error.';
        exit;
    }

    $imageFilePath = $_SERVER['DOCUMENT_ROOT'] . parse_url($imageSrc, PHP_URL_PATH);
    $imageDir = dirname($imageFilePath);
    $imageName = basename($imageFilePath);
    $imageExt = pathinfo($imageName, PATHINFO_EXTENSION);

    // Backup old image
    if (file_exists($imageFilePath)) {
        $backupName = pathinfo($imageName, PATHINFO_FILENAME) . '-' . time() . '.' . $imageExt;
        $backupPath = $imageDir . '/' . $backupName;
        rename($imageFilePath, $backupPath);
    }

    // Move the uploaded file to the target location
    $tempFilePath = $imageFile['tmp_name'];
    move_uploaded_file($tempFilePath, $imageFilePath);

    // Resize the image
    resizeImage($imageFilePath, $imageWidth, $imageHeight);

    echo 'success';
} else {
    http_response_code(405);
    echo 'Method not allowed';
}

function resizeImage($filePath, $newWidth, $newHeight) {
    $info = getimagesize($filePath);
    if (!$info) {
        return;
    }

    list($width, $height, $type) = $info;

    // Create image resource
    switch ($type) {
        case IMAGETYPE_JPEG:
            $srcImg = imagecreatefromjpeg($filePath);
            break;
        case IMAGETYPE_PNG:
            $srcImg = imagecreatefrompng($filePath);
            break;
        case IMAGETYPE_GIF:
            $srcImg = imagecreatefromgif($filePath);
            break;
        default:
            return;
    }

    // Create a new true color image with the desired dimensions
    $dstImg = imagecreatetruecolor($newWidth, $newHeight);

    // Handle transparency for PNG images
    if ($type === IMAGETYPE_PNG) {
        imagealphablending($dstImg, false);
        imagesavealpha($dstImg, true);
        $transparent = imagecolorallocatealpha($dstImg, 255, 255, 255, 127);
        imagefill($dstImg, 0, 0, $transparent);
    }

    // Resize the image
    imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    // Save the resized image
    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($dstImg, $filePath);
            break;
        case IMAGETYPE_PNG:
            imagepng($dstImg, $filePath);
            break;
        case IMAGETYPE_GIF:
            imagegif($dstImg, $filePath);
            break;
    }

    // Free up memory
    imagedestroy($srcImg);
    imagedestroy($dstImg);
}

