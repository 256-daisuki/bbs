<?php
session_start();
require_once 'dbconnect.php';

// ログイン処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // パスワードをSHA256でハッシュ化
    $hashedPassword = hash('sha256', $password);

    // ユーザーの検索クエリ
    $sql = "SELECT * FROM users WHERE email = :email AND password = :password";
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':password', $hashedPassword, PDO::PARAM_STR);
    $stmt->execute();

    // ユーザーが存在するかチェック
    if ($stmt->rowCount() > 0) {
        session_unset();
        session_destroy();
        session_set_cookie_params(10368000);//セッションの期限の延長
        session_start();
        $user = $stmt->fetch();
        $_SESSION['email'] = $email;
        $_SESSION['username'] = $user['username']; // ユーザー名をセッションに保存
        header('Location: index.php');
        exit;
    } else {
        // ログイン失敗
        $errorMessage = 'ログインに失敗しました。';
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>bbs.256server｜ログイン</title>
    <link rel="stylesheet" href="sub.css">
</head>

<body>
    <main>
        <div class="login-main" id="login-main" style="height: 300px;">
            <div class="login-main-margin">
                <h2>bbsにログイン</h2>
                <?php if (isset($errorMessage)) : ?>
                    <p style="color: red;"><?php echo $errorMessage; ?></p>
                <?php endif; ?>
                <form action="login.php" method="POST">
                    <input type="email" id="email" class="post_word" name="email" required placeholder="メールアドレス"><br>
                    <input type="password" id="password" class="post_word" name="password" required placeholder="パスワード"><br>
                    <p id="caps-lock"></p>
                    <input type="submit" value="ログイン" class="login_submit" id="low"><br>
                    <div id="low-a" style="transition: 0.4s ease;"><a href="/create_account.php">新規登録</a></div>
                </form>
            </div>
        </div>
        <div class="freebbs-main">
            <div class="freebbs-main-margin">
                <a href="https://256server.com/bbs/index.php">誰でも書き込めるbbs</a>
            </div>
        </div>
    </main>
    <script>
        let isExtended = false; // div要素が伸ばされているかを管理するフラグ

        document.addEventListener("keydown", function(event) {
            const capsLockOn = event.getModifierState && event.getModifierState("CapsLock");
            const messageElement = document.getElementById("caps-lock");
            const divElement = document.getElementById("login-main");
            const submitElement = document.getElementById("low");
            const aElement = document.getElementById('low-a');
            

            if (capsLockOn && !isExtended) {
                messageElement.textContent = "Caps-Lockがオンになっています";
                messageElement.style.opacity = "1"; // メッセージを表示

                // div要素のheightを伸ばす
                divElement.style.height = (divElement.offsetHeight + 30) + "px";

                submitElement.style.transform = "translateY(0px)";
                aElement.style.transform = 'translateY(0px)';
                isExtended = true; // フラグをtrueに設定
            } else if (!capsLockOn && isExtended) {
                messageElement.style.opacity = "0"; // アニメーションで非表示にする

                // メッセージを非表示にする際に、div要素も元の高さに戻す
                divElement.style.height = "300px";
                submitElement.style.transform = "translateY(-40px)";
                aElement.style.transform = 'translateY(-40px)';
                isExtended = false; // フラグをfalseに設定
            }
        });

        // アニメーション終了時に非表示にする
        const messageElement = document.getElementById("caps-lock");
        const submitElement = document.getElementById("low");

        submitElement.style.transform = "translateY(0px)";

        messageElement.addEventListener("transitionend", function() {
            if (messageElement.style.opacity === "0") {

                massageElement.style.display = "none";
            }
        });
    </script>
</body>
</html>
