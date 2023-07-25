<?php
session_start();
require_once 'dbconnect.php';
ini_set("display_errors", 1);
error_reporting(E_ALL);

// セッションにログイン情報がない場合はログインページにリダイレクト
if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}

$email = $_SESSION['email'];
$username = $_SESSION['username']; // ユーザー名を取得

//====================================//
//==============bbs関係===============//
//====================================//

// スレッドを作成する
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['thread_name']) && !empty($_POST['thread_name']) && isset($_POST['comment'])) {
        $threadName = $_POST['thread_name'];
        $comment = $_POST['comment'];

        // 新しいテーブルを作成
        $sql = "CREATE TABLE IF NOT EXISTS thread_{$threadName} (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT, username VARCHAR(255), comment TEXT, created_at DATETIME)";
        $stmt = $dbh->query($sql);
        if ($stmt) {
            // スレッド作成成功したら1番目の書き込みを追加
            $now = date('Y-m-d H:i:s');
            $sql = "INSERT INTO thread_{$threadName} (user_id, username, comment, created_at) VALUES (?, ?, ?, ?)";
            $stmt = $dbh->prepare($sql);
            $stmt->execute([1, $username, $comment, $now]);

            echo 'スレッドが作成されました。';
            // リダイレクトによりGETリクエストを行う
            header('Location: thread.php?name=' . urlencode($threadName));
            exit;
        } else {
            echo 'スレッド名とコメントを入力してください。';
        }
    }
}

// スレッド内での書き込み処理
if (isset($_POST['thread']) && isset($_POST['comment'])) {
    $threadName = $_POST['thread'];
    $comment = $_POST['comment'];

    // 書き込みを追加
    $now = date('Y-m-d H:i:s');
    $sql = "INSERT INTO thread_{$threadName} (user_id, username, comment, created_at) VALUES (?, ?, ?, ?)";
    $stmt = $dbh->prepare($sql);
    $stmt->execute([1, $username, $comment, $now]);
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>Home</title>
</head>

<body>
    <h2>ようこそ<?php echo $username; ?>さん!</h2>
    <p>あなたは今ログインしています。</p>
    <a href="logout.php">ログアウト</a>

    <h2>新しいスレッドを立てる</h2>
    <form action="index.php" method="POST">
        <label for="thread_name">スレッド名:</label>
        <input type="text" id="thread_name" name="thread_name" required><br>
        <label for="comment">コメント:</label>
        <input type="text" id="comment" name="comment" required><br>
        <input type="submit" value="作成">
    </form>

    <h2>スレッド一覧</h2>
    <?php
    // スレッド一覧を取得
    $sql = "SHOW TABLES";
    $stmt = $dbh->query($sql);
    while ($row = $stmt->fetch()) {
        $threadName = $row[0];
        if (strpos($threadName, 'thread_') === 0) {
            echo '<a href="thread.php?name=' . urlencode(substr($threadName, 7)) . '">' . substr($threadName, 7) . '</a><br>';
        }
    }
    ?>
</body>

</html>
