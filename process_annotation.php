<?php
// Start session
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$rawDir = 'dataset/raw';
$trainDir = 'dataset/annotated/train';
$testDir = 'dataset/annotated/test';
$validateDir = 'dataset/annotated/validate';

// Example function to move and split data
function moveAndSplitData($rawDir, $trainDir, $testDir, $validateDir)
{
    $images = glob("$rawDir/*.jpg");
    shuffle($images);

    $totalImages = count($images);
    $trainImages = array_slice($images, 0, round($totalImages * 0.6));
    $validateImages = array_slice($images, round($totalImages * 0.6), round($totalImages * 0.2));
    $testImages = array_slice($images, round($totalImages * 0.8));

    foreach ($trainImages as $image) {
        $imageName = basename($image);
        rename($image, "$trainDir/images/$imageName");
        // Move corresponding label file
        rename(str_replace('.jpg', '.txt', $image), "$trainDir/labels/" . str_replace('.jpg', '.txt', $imageName));
    }

    foreach ($validateImages as $image) {
        $imageName = basename($image);
        rename($image, "$validateDir/images/$imageName");
        rename(str_replace('.jpg', '.txt', $image), "$validateDir/labels/" . str_replace('.jpg', '.txt', $imageName));
    }

    foreach ($testImages as $image) {
        $imageName = basename($image);
        rename($image, "$testDir/images/$imageName");
        rename(str_replace('.jpg', '.txt', $image), "$testDir/labels/" . str_replace('.jpg', '.txt', $imageName));
    }
}

// Call function to move and split data
moveAndSplitData($rawDir, $trainDir, $testDir, $validateDir);

echo "Data processing complete.";
