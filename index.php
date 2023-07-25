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

// スレッド一覧を取得
$sql = "SHOW TABLES LIKE 'thread_%'";
$stmt = $dbh->query($sql);
$threads = $stmt->fetchAll(PDO::FETCH_COLUMN);

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
    <a href="logout.php">ログアウト</a>

    <h2>新しいスレッドを立てる</h2>
    <form action="index.php" method="POST">
        <label for="thread_name">スレッド名:</label>
        <input type="text" id="thread_name" name="thread_name" required><br>
        <label for="comment">コメント:</label>
        <textarea id="comment" name="comment" rows="1.5" required></textarea><br>
        <input type="submit" value="作成">
    </form>

    <h2>スレッド一覧</h2>
    <p>表示順：
        <a href="#" onclick="changeSort('new')">新しい順</a>
        <a href="#" onclick="changeSort('old')">古い順</a>
        <a href="#" onclick="changeSort('popular')">人気順</a>
    </p>
    <ul id="threadList">
        <?php
        // スレッド一覧の取得
        $sql = "SHOW TABLES LIKE 'thread_%'";
        $stmt = $dbh->query($sql);
        $threads = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // ソート方法に応じてスレッド一覧を並び替える
        if (isset($_GET['sort'])) {
            $sort = $_GET['sort'];
            if ($sort === 'old') {
                $threads = array_reverse($threads);
            } elseif ($sort === 'popular') {
                // 人気順の場合はスレッドの最後のidの値でソート
                usort($threads, function ($a, $b) use ($dbh) {
                    $sql = "SELECT MAX(id) AS last_id FROM {$a}";
                    $stmt = $dbh->query($sql);
                    $lastIdA = $stmt->fetchColumn();

                    $sql = "SELECT MAX(id) AS last_id FROM {$b}";
                    $stmt = $dbh->query($sql);
                    $lastIdB = $stmt->fetchColumn();

                    return $lastIdB - $lastIdA;
                });
            }
        }

        // スレッド一覧を表示
        foreach ($threads as $thread) {
            $sql = "SELECT MAX(id) AS last_id FROM {$thread}";
            $stmt = $dbh->query($sql);
            $lastId = $stmt->fetchColumn();

            echo '<li><a href="thread.php?name=' . htmlspecialchars(substr($thread, 7)) . '">' . htmlspecialchars(substr($thread, 7)) . '（' . $lastId . '件）</a></li>';
        }
        ?>
    </ul>

    <script>
        // 表示順を切り替えるJavaScript関数
        function changeSort(sort) {
            let currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('sort', sort);
            window.location.href = currentUrl.href;
        }
    </script>
</body>

</html>
