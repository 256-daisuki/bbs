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

// スレッドを作成する
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['thread_name']) && !empty($_POST['thread_name']) && isset($_POST['comment'])) {
        $threadName = $_POST['thread_name'];
        $comment = $_POST['comment'];

        // 新しいテーブルを作成
        $sql = "CREATE TABLE IF NOT EXISTS thread_{$threadName} (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT, username VARCHAR(255), comment TEXT, image_path VARCHAR(255), created_at DATETIME)";
        $stmt = $dbh->query($sql);
        if ($stmt) {
            // スレッド作成成功したら1番目の書き込みを追加
            $now = date('Y-m-d H:i:s');

            // 画像をアップロードする処理
            $uploadDir = 'uploads/'; // アップロードディレクトリのパス（適宜変更してください）
            $uploadFile = $uploadDir . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile);

            $sql = "INSERT INTO thread_{$threadName} (username, comment, image_path, created_at) VALUES (?, ?, ?, ?)";
            $stmt = $dbh->prepare($sql);
            $stmt->execute([$username, $comment, $uploadFile, $now]);

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
    $sql = "INSERT INTO thread_{$threadName} (username, comment, created_at) VALUES (?, ?, ?)";
    $stmt = $dbh->prepare($sql);
    $stmt->execute([$username, $comment, $now]);
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title><?php echo $threadName; ?></title>
</head>

<body>
    <h2>ようこそ<?php echo $username; ?>さん!</h2>
    <p>あなたは今ログインしています。</p>
    <a href="index.php">インデックスに戻る</a>
    <a href="logout.php">ログアウト</a>

    <h2><?php echo $threadName; ?></h2>
    <form action="thread.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="thread" value="<?php echo $threadName; ?>">
        <label for="comment">コメント:</label>
        <input type="text" id="comment" name="comment" required>
        <label for="image">画像を選択:</label>
        <input type="file" id="image" name="image">
        <input type="submit" value="投稿">
    </form>

    <h3>スレッド内の書き込み</h3>
    <?php
    foreach ($comments as $comment) {
        echo '<p><strong>' . htmlspecialchars($comment['username']) . '</strong> ' . $comment['created_at'] . '<br>';
        if ($comment['image_path']) {
            echo '<img src="' . htmlspecialchars($comment['image_path']) . '" alt="アップロードされた画像" width="200">';
        }
        echo '<br>' . htmlspecialchars($comment['comment']) . '</p>';
    }
    ?>
</body>

</html>
