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

// スレッド内での書き込み処理
if (isset($_POST['thread']) && isset($_POST['comment'])) {
    $threadName = $_POST['thread'];
    $comment = $_POST['comment'];

    // 書き込みを追加
    $now = date('Y-m-d H:i:s');
    $sql = "INSERT INTO thread_{$threadName} (username, comment, created_at) VALUES (?, ?, ?)";
    $stmt = $dbh->prepare($sql);
    $stmt->execute([$username, $comment, $now]);
}

// スレッド名を取得
if (isset($_GET['name']) && !empty($_GET['name'])) {
    $threadName = $_GET['name'];

    // スレッド内の書き込みを取得
    $sql = "SELECT * FROM thread_{$threadName} ORDER BY created_at DESC";
    $stmt = $dbh->query($sql);
    $comments = $stmt->fetchAll();
} else {
    // スレッド名が指定されていない場合は何も表示しない
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $threadName; ?></title>
</head>

<body>
    <h2>ようこそ<?php echo $username; ?>さん!</h2>
    <a href="index.php">インデックスに戻る</a>
    <a href="logout.php">ログアウト</a>

    <h2><?php echo htmlspecialchars($threadName); ?></h2>
    <form action="thread.php" method="POST">
        <input type="hidden" name="thread" value="<?php echo $threadName; ?>">
        <label for="comment">コメント:</label>
        <textarea id="comment" name="comment" rows="1" required></textarea>
        <input type="submit" value="投稿">
    </form>

    <h3>書き込み</h3>
    <?php
    foreach ($comments as $comment) {
        echo '<p>' . $comment['id'] . '&nbsp;<strong>' . htmlspecialchars($comment['username']) . '</strong> ' . $comment['created_at'] . '<br>' . nl2br(htmlspecialchars($comment['comment'])) . '</p>';
    }
    ?>
    <script>
        // textareaのエンターキーの挙動をカスタマイズ
        document.getElementById("comment").addEventListener("keydown", function(event) {
            if (event.keyCode === 13 && !event.shiftKey) {
                event.preventDefault(); // エンターキーのデフォルト挙動をキャンセル
                this.form.submit(); // 送信
            }
        });
    </script>
</body>

</html>