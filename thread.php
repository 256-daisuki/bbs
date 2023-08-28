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
    $offset = isset($_GET['page']) ? ($_GET['page'] - 1) * 100 : 0;
    $sql = "SELECT * FROM thread_{$threadName} ORDER BY id DESC LIMIT 100 OFFSET {$offset}";
    $stmt = $dbh->query($sql);
    $comments = $stmt->fetchAll();
} else {
    // スレッド名が指定されていない場合は何も表示しない
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

$commentCount = 0;
//スレッド内部の書き込み総数を取得
$sql = "SELECT COUNT(*) as row_count FROM thread_{$threadName}";
$result = $dbh->query($sql);
if ($result) {
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $commentCount = $row["row_count"];
}

echo "<script>let commentCount = ".$commentCount."; let threadName = '".$threadName."';</script>";
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $threadName; ?></title>
    <link rel="stylesheet" href="style.css">
</head>

<body id="dark-mode">
    <header>
        <ul class="header-ul">
            <li class="title-256"><a href="#">256</a></li>
            <li class="nav-link">
                <nav>
                    <ul>
                        <li><a href="https://256server.com">home</a></li>
                        <li><a href="https://256server.com/tools">tools</a></li>
                        <li><a href="https://256server.com/history/index.html">history</a></li>
                        <li><a href="./index.php">BBS</a></li>
                    </ul>
                </nav>
            </li>
        </ul>
    </header>
    <main>
        <a href="index.php">インデックスに戻る</a>
        <a href="logout.php">ログアウト</a>
        <button id="darkModeButton">ダークモード</button>
        <input type="button" value="リロード" onclick="window.location.reload();" />

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
            <input type="file" id="image" name="image" required><!--大変身勝手ながrあ僕のガラケーで動かすためには形式指定を外すのです！-->
            <input type="submit" value="アップロード">
        </form>

        <div class="pagination">
            <h3>
                ページ
                <?php
                for ($i = 1; $i <= ceil($commentCount / 100); $i++) {
                    echo ' <a href="?name=' . $threadName . '&page=' . $i . '">' . $i . '</a>';
                }
                ?>
            </h3>
        </div>

        <h3 id="kakikomi">書き込み</h3>
    
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
                    $contentType = isset($headers['Content-Type']) ? $headers['Content-Type'] : '';
                    
                    if (is_array($contentType)) {
                        $contentType = implode('', $contentType);
                    }
                    
                    if (strpos($contentType, 'image/') === 0) {
                        return '<a href="' . $url . '" target="_blank"><img src="' . $url . '" class="img" alt="' . $url . '"></a>';
                    } else {
                        return '<a href="' . $url . '" target="_blank">' . $url . '</a>';
                    }
                }, $content);
            
                echo $content;
                echo '</p>';
            }
        ?>

        <div class="pagination">
            <h3>
                ページ
                <?php
                for ($i = 1; $i <= ceil($commentCount / 100); $i++) {
                    echo ' <a href="?name=' . $threadName . '&page=' . $i . '">' . $i . '</a>';
                }
                ?>
            </h3>
        </div>

    </main>
    
    <!--<script>
        // textareaのエンターキーの挙動をカスタマイズ
        document.getElementById("comment").addEventListener("keydown", function(event) {
            if (event.keyCode === 13 && !event.shiftKey) {
                event.preventDefault(); // エンターキーのデフォルト挙動をキャンセル
                this.form.submit(); // 送信
            }
        })
    </script>-->
    <script>
        const darkModeButton = document.getElementById('darkModeButton');
        const content = document.getElementById('dark-mode');
        const DARK_MODE_COOKIE_NAME = 'darkMode';

        darkModeButton.addEventListener('click', toggleDarkMode);

        // ページ読み込み時に保存されたダークモードの状態を復元
        if (getDarkModeCookie()) {
            content.classList.add('dark-mode');
        }
        header-dark
        
        function toggleDarkMode() {
            content.classList.toggle('dark-mode');
            const darkMode = content.classList.contains('dark-mode');
            setDarkModeCookie(darkMode);
        }

        function setDarkModeCookie(value) {
            document.cookie = `${DARK_MODE_COOKIE_NAME}=${value}; path=/`;
        }

        function getDarkModeCookie() {
            const cookies = document.cookie.split(';');
            for (const cookie of cookies) {
                const [name, value] = cookie.split('=');
                if (name.trim() === DARK_MODE_COOKIE_NAME) {
                return value.trim() === 'true';
                }
            }
        return false;
        }
    </script>
    <script>
        const textarea = document.getElementById('comment');

        function adjustTextareaRows() {
            const lines = textarea.value.split('\n').length;
            textarea.rows = lines;
        }

        textarea.addEventListener('input', adjustTextareaRows);
    </script>
    <script>//0.5秒毎のXMLによる新規コメント確認
        setInterval(function() {
            checknew();
        }, 500);

        function checknew() {
            var xhr = new XMLHttpRequest();
            var url = "checknewcomment.php";
            var data = "threadName=" + encodeURIComponent(threadName) + "&commentCount=" + encodeURIComponent(commentCount);

            xhr.open("POST", url, true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    var newjsonArray = response.dataArray; // dataArrayはPHPスクリプトで定義されたJSONキーに合わせてください
                    if (response[0] === "no") {
                        //スルー
                    } else {
                        for (let i = 0; i < response.length;) {
                            commentCount += 1;
                            var newElement = document.createElement("p");
                            newElement.innerHTML = response[i].id + " <strong>" + response[i].username + "</strong> " + response[i].created_at + "<br>" + response[i].comment;
                            kakikomi.parentNode.insertBefore(newElement, kakikomi.nextSibling);
                            i++;
                        }
                    }
            }
        };

        xhr.send(data);
    }   
    </script>
</body>

</html>
