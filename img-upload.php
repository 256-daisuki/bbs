<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $uploadDir = '/var/www/html/img/';
    $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif']; // 許可する拡張子のリスト

    // アップロードされたファイルの拡張子が許可リストに含まれているかチェック
    if (in_array($extension, $allowedExtensions)) {
        $fileName = uniqid() . '.' . $extension;
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
            $imageURL = 'https://bbs.256server.com/img/' . $fileName;
            header('Location: ' . $imageURL);
            exit;
        } else {
            echo "ファイルのアップロードに失敗したよ。";
        }
    } else {
        echo "画像ファイル以外はアップロードしないでね。";
    }
}
?>