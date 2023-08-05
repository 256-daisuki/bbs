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
$username = $_SESSION['username'];

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

    // スレッド内の書き込みを取得（idの新しいものが上にくるようにする）
    $sql = "SELECT * FROM thread_{$threadName} ORDER BY id DESC";
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
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h2>ようこそ<?php echo $username; ?>さん!</h2>
    <a href="index.php">インデックスに戻る</a>
    <a href="logout.php">ログアウト</a>

    <h2><?php echo htmlspecialchars($threadName); ?></h2>
    <form action="thread.php" method="POST">
        <input type="hidden" name="thread" value="<?php echo $threadName; ?>">
        <label for="comment">コメント:</label>
        <textarea id="comment" name="comment" rows="1" cols="32" required onkeyup="ShowLength(value);" ></textarea>
        <input type="submit" value="投稿">
    </form>
    <label>画像アップローダー</label>
    <form action="img-upload.php" method="post" enctype="multipart/form-data">
        <label for="image">画像を選択してください:</label>
        <input type="file" id="image" name="image" accept="image/*" required>
        <input type="submit" value="アップロード">
    </form>

    <h3>書き込み</h3>
    <?php
    // カスタムのエスケープ関数
    function custom_escape($str) {
        return nl2br(htmlspecialchars($str, ENT_QUOTES));
    }

    foreach ($comments as $comment) {
        echo '<p>' . $comment['id'] . ' <strong>'. custom_escape($comment['username']) . '</strong> ' . $comment['created_at'] . '<br>';

        // コメント内のURLを解析して画像ファイルを<img>タグで表示
        $content = custom_escape($comment['comment']);
        $content = preg_replace_callback('/(https?:\/\/[^\s<>"\'()]+)/', function($matches) {
            $url = $matches[1];
            $headers = get_headers($url, 1);
            if (isset($headers['Content-Type']) && strpos($headers['Content-Type'], 'image/') === 0) {
                return '<a href="' . $url . '"><img src="' . $url . '" class="img" alt="' . $url . '"></a>';
            } else {
                return '<a href="' . $url . '">' . $url . '</a>';
            }
        }, $content);

        echo $content;
        echo '</p>';
    }
    ?>
    <script>
        // textareaのエンターキーの挙動をカスタマイズ
        document.getElementById("comment").addEventListener("keydown", function(event) {
            if (event.keyCode === 13 && !event.shiftKey) {
                event.preventDefault(); // エンターキーのデフォルト挙動をキャンセル
                this.form.submit(); // 送信
            }
        })
    </script>
    <script>
        const textarea = document.getElementById('comment');

        function adjustTextareaRows() {
            const lines = textarea.value.split('\n').length;
            textarea.rows = lines;
        }

        textarea.addEventListener('input', adjustTextareaRows);
    </script>
</body>

</html>