<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $uploadDir = '/var/www/html/img/';
    $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $fileName = uniqid() . '.' . $extension;
    $filePath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
        $imageURL = 'https://bbs.256server.com' . '/img/' . $fileName;
        echo "画像のURL: $imageURL";
    } else {
        echo "ファイルのアップロードに失敗しました。";
    }
}
?>