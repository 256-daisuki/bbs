<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $uploadDir = '/var/www/html/img/';
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

    // アップロードされたファイルの拡張子が許可された画像ファイルであるかチェック
    if (!in_array($extension, $allowedExtensions)) {
        echo "画像ファイルのみアップロード可能です。jpg、jpeg、png、gifが投稿できます";
        exit;
    }

    $fileName = uniqid() . '.' . $extension;
    $filePath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
        $imageURL = 'http://bbs.256server.com' . '/img/' . $fileName;
        header('Location: ' . $imageURL);
        exit;
    } else {
        echo "ファイルのアップロードに失敗しました。";
    }
}
?>