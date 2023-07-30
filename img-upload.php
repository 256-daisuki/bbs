<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $uploadDir = '/var/www/html/img/';
    $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $fileName = uniqid() . '.' . $extension;
    $filePath = $uploadDir . $fileName; // ここを修正

    if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
        $imageURL = 'https://bbs.256server.com' . '/img/' . $fileName;
        header('Location: ' . $imageURL); // 変数名を修正
        exit;
    } else {
        echo "ファイルのアップロードに失敗しました。";
    }
}
?>