<?php
declare(strict_types=1);

function uploadFiles(array $files, string $uploadDir, array $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4']): array {
    $uploadedFiles = [];
    $uploadDir = rtrim($uploadDir, '/') . '/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    foreach ($files['name'] as $index => $name) {
        $tmpName = $files['tmp_name'][$index];
        $type = $files['type'][$index];
        $error = $files['error'][$index];
        $size = $files['size'][$index];

        if ($error !== UPLOAD_ERR_OK) {
            continue;
        }

        if (!in_array($type, $allowedTypes)) {
            continue;
        }

        $extension = pathinfo($name, PATHINFO_EXTENSION);
        $uniqueName = uniqid('', true) . '.' . $extension;

        $destination = $uploadDir . $uniqueName;
        if (move_uploaded_file($tmpName, $destination)) {
            $uploadedFiles[] = $destination;
        }
    }

    return $uploadedFiles;
}