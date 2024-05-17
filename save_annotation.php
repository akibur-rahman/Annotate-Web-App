<?php
// Start session
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $imageName = $_POST['image_name'];
    $annotations = $_POST['annotations']; // This should be a JSON string

    $annotationData = json_decode($annotations, true);
    $labelFile = str_replace('.jpg', '.txt', $imageName);
    $labelPath = "dataset/raw/$labelFile";

    $file = fopen($labelPath, 'w');
    foreach ($annotationData as $annotation) {
        $label = $annotation['label'];
        $x = $annotation['x'];
        $y = $annotation['y'];
        $width = $annotation['width'];
        $height = $annotation['height'];
        fwrite($file, "$label $x $y $width $height\n");
    }
    fclose($file);

    echo "Annotation saved.";
} else {
    echo "Invalid request.";
}
