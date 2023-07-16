<?php
// DB接続情報
$host = 'localhost';
$dbname = 'user';
$user = 'php-login';
$pass = '';

try {
    // データベースに接続
    $dbh = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    // エラーモードを設定
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // エラーメッセージを表示
    die('DB接続エラー: ' . $e->getMessage());
}