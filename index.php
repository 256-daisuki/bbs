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
        $sql = "CREATE TABLE IF NOT EXISTS thread_{$threadName} (id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(255), comment TEXT, created_at DATETIME)";
        $stmt = $dbh->query($sql);
        if ($stmt) {
            // スレッド作成成功したら1番目の書き込みを追加
            $now = date('Y-m-d H:i:s');
            $sql = "INSERT INTO thread_{$threadName} (username, comment, created_at) VALUES (?, ?, ?)";
            $stmt = $dbh->prepare($sql);
            $stmt->execute([$username, $comment, $now]);

            echo 'スレッドが作成されました。';
            // リダイレクトによりGETリクエストを行う
            header('Location: thread.php?name=' . urlencode($threadName));
            exit;
        } else {
            echo 'スレッドの作成に失敗しました。';
        }
    } else {
        echo 'スレッド名とコメントを入力してください。';
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>BBS｜home</title>
</head>

<body>
    <h1>bbs.256server <div  style="color:red">試運転</div></h1>
    <h2>ようこそ<?php echo $username; ?>さん!</h2>
    <a href="/bbs-rule.html">BBSのルール</a>
    <a href="logout.php">ログアウト</a>

    <h2>新しいスレッドを立てる</h2>
    <form action="index.php" method="POST">
        <label for="thread_name">スレッド名:</label>
        <input type="text" id="thread_name" name="thread_name" required><br>
        <label for="comment">コメント:</label>
        <textarea id="comment" name="comment" rows="1.5" required></textarea><br>
        <input type="submit" value="作成"><a href="/thread-rule.html">スレッドを立てる前に</a>
    </form>

    <h2>スレッド一覧</h2><!--ここはすべてChatGPTが書きました　動かないからって私にモンク言わないで-->
    <form action="" method="get">
        <p>表示順：
            <select name="sort" onchange="this.form.submit()">
            <option value="new" <?php if (isset($_GET['sort']) && $_GET['sort'] === 'new') echo 'selected'; ?>>新しい順</option>
            <option value="old" <?php if (isset($_GET['sort']) && $_GET['sort'] === 'old') echo 'selected'; ?>>古い順</option>
            <option value="popular" <?php if (isset($_GET['sort']) && $_GET['sort'] === 'popular') echo 'selected'; ?>>人気順</option>
            </select>
        </p>
    </form>
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
                // 古い順はスレッドの最初の書き込み時間でソート
                usort($threads, function ($a, $b) use ($dbh) {
                    $sql = "SELECT MIN(created_at) AS first_created_at FROM {$a}";
                    $stmt = $dbh->query($sql);
                    $firstCreatedAtA = $stmt->fetchColumn();

                    $sql = "SELECT MIN(created_at) AS first_created_at FROM {$b}";
                    $stmt = $dbh->query($sql);
                    $firstCreatedAtB = $stmt->fetchColumn();

                    return strtotime($firstCreatedAtA) - strtotime($firstCreatedAtB);
                });
            } elseif ($sort === 'new') {
                // 新しい順はスレッドの最初の書き込み時間でソート
                usort($threads, function ($a, $b) use ($dbh) {
                    $sql = "SELECT MIN(created_at) AS first_created_at FROM {$a}";
                    $stmt = $dbh->query($sql);
                    $firstCreatedAtA = $stmt->fetchColumn();

                    $sql = "SELECT MIN(created_at) AS first_created_at FROM {$b}";
                    $stmt = $dbh->query($sql);
                    $firstCreatedAtB = $stmt->fetchColumn();

                    return strtotime($firstCreatedAtB) - strtotime($firstCreatedAtA);
                });
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
            $sql = "SELECT MAX(id) AS last_id, MIN(created_at) AS first_created_at FROM {$thread}";
            $stmt = $dbh->query($sql);
            $threadInfo = $stmt->fetch(PDO::FETCH_ASSOC);
            $lastId = $threadInfo['last_id'];
            $firstCreatedAt = $threadInfo['first_created_at'];

            echo '<li><a href="thread.php?name=' . htmlspecialchars(substr($thread, 7)) . '">' . htmlspecialchars(substr($thread, 7)) . '（' . $lastId . '件の書き込み' . '）</a></li>';
        }
        ?>
    </ul>

    <script>
    // 表示順を切り替えるJavaScript関数
    function changeSort() {
        const sort = document.querySelector('select[name="sort"]').value;
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('sort', sort);
        window.location.href = currentUrl.href;
    }
</script>
</body>
</html>