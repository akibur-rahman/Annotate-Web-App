<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $imageName = $_POST['image'];
    $annotationData = $_POST['data'];
    $rawDir = 'dataset/raw';
    $annotatedDir = 'dataset/annotated/train/labels';

    // Ensure the directories exist
    if (!is_dir($annotatedDir)) {
        mkdir($annotatedDir, 0777, true);
    }

    // Write annotation data to file
    $baseName = pathinfo($imageName, PATHINFO_FILENAME);
    $annotationFile = "$annotatedDir/$baseName.txt";
    file_put_contents($annotationFile, $annotationData, FILE_APPEND);

    echo "Annotation saved.";
} else {
    echo "Invalid request.";
}
