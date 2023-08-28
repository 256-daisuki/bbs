<?php
ini_set('display_errors', 0);
session_start();
require_once 'dbconnect.php';
ini_set("display_errors", 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

// セッションにログイン情報がない場合はログインページにリダイレクト
if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}

$threadName = $_POST["threadName"];
$commentCount = $_POST["commentCount"];
$RcommentCount = 0;

//スレッド内部の書き込み総数を取得

$sql = "SELECT COUNT(*) as row_count FROM thread_{$threadName}";
$result = $dbh->query($sql);
if ($result) {
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $RcommentCount = $row["row_count"];
}

if ($RcommentCount > $commentCount) {
    //新規コメントの内容をjsonで古い順に送信
    $commentCount += 1;
    $sql = "SELECT id, username, comment, created_at FROM thread_{$threadName} WHERE id >= ".$commentCount." AND id <= ".$RcommentCount." ORDER BY id";
    $result = $dbh->query($sql);

    $response = array();
    if ($result) {
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $response[] = $row; // レスポンスに行データを追加
        }
}
echo json_encode($response);
} else {
    //0を返す
    $response = array("no");
    echo json_encode($response);
}

?>