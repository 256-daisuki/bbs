<?php
session_start();
require_once 'dbconnect.php';
ini_set("display_errors", 1);
error_reporting(E_ALL);

// セッションにログイン情報がない場合はログインページにリダイレクト
if (!isset($_SESSION['email'])) {
    header('Location: ./login.php');
    exit;
}

$email = $_SESSION['email'];
$username = $_SESSION['username'];

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
            // スレッド作成成功したら1番目の書き込みを追加する
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BBS｜home</title>
    <link rel="stylesheet" href="style.css">
    <!-- Google tag (gtag.js) -->
    <!--<script async src="https://www.googletagmanager.com/gtag/js?id=G-BN5KGMB0GN"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());

        gtag('config', 'G-BN5KGMB0GN');
    </script>-->
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
                        <li><a href="#">BBS</a></li>
                    </ul>
                </nav>
            </li>
        </ul>
    </header>
    <main>
        <h1>bbs.256server <div style="color:red">試運転</div></h1>
        <div class="test">
            <div class="popup-container">
                <div class="popup-content">
                    <p>ようこそ！</p>
                    <p><?php echo $username; ?>さん！</p>
                </div>
            </div>
        </div>
        <a href="/bbs-rule.html">BBSのルール</a>
        <a href="logout.php">ログアウト</a>
        <a href="https://256server.com/bbs/index.php">誰でも書き込めるbbs</a>
        <button id="darkModeButton">ダークモード</button>
        <input type="button" value="リロード" onclick="window.location.reload();" />

        <h2>画像アップローダー</h2>
        <form action="img-upload.php" method="post" enctype="multipart/form-data">
            <label for="image">画像を選択してください:</label>
            <input type="file" id="image" name="image" accept="image/*" required>
            <input type="submit" value="アップロード">
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

                echo '<li><a href="thread.php?name=' . htmlspecialchars(substr($thread, 7)) . '">' . htmlspecialchars(substr($thread, 7)) . '（' . $lastId . '）</a></li>';
            }
            ?>
        </ul>
        <h2>新しいスレッドを立てる</h2>
        <form action="index.php" method="POST">
            <label for="thread_name">スレッド名:</label>
            <input type="text" id="thread_name" name="thread_name" required><br>
            <label for="comment">コメント:</label>
            <textarea id="comment" name="comment" rows="1.5" required></textarea><br>
            <input type="submit" value="作成"><a href="/thread-rule.html">スレッドを立てる前に</a>
        </form>
    </main>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const popupContainer = document.querySelector(".popup-container");
        const cookieName = "popupShown";

        // Cookieがすでに保存されている場合はポップアップを表示しない
        if (!getCookie(cookieName)) {
            setTimeout(() => {
                popupContainer.style.top = "8px"; // 上から表示するために位置を0に設定
                setTimeout(() => {
                    closePopup();
                }, 2000); // 2秒後にclosePopup関数を実行

                // Cookieに情報を保存して再訪時にポップアップを表示しないようにする
                setCookie(cookieName, "true", 1); // 1日間有効なCookieを設定
            }, 900); // 0.9秒後にポップアップ表示の処理を実行
        }
    });

    const popupContainer = document.querySelector(".popup-container");
    function closePopup() {
        popupContainer.classList.add("closing"); // クラスを追加
        setTimeout(() => {
            popupContainer.classList.remove("closing"); // クラスを削除
            popupContainer.style.top = "-100%"; // 画面外に戻すために位置を-100%に設定
        }, 2000); // 2秒後に実行
    }

    // Cookieを設定する関数
    function setCookie(name, value, days) {
        const expires = new Date();
        expires.setTime(expires.getTime() + days * 24 * 60 * 60 * 1000);
        document.cookie = name + "=" + value + ";expires=" + expires.toUTCString();
    }

    // Cookieを取得する関数
    function getCookie(name) {
        const cookieArr = document.cookie.split(";");
        for (let i = 0; i < cookieArr.length; i++) {
            const cookiePair = cookieArr[i].split("=");
            if (name === cookiePair[0].trim()) {
                return decodeURIComponent(cookiePair[1]);
            }
        }
        return null;
    }
    </script>
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
    // 表示順を切り替えるJavaScript
    function changeSort() {
        const sort = document.querySelector('select[name="sort"]').value;
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('sort', sort);
        window.location.href = currentUrl.href;
    }
    </script>
</body>
</html>