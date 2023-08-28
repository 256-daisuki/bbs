<?php
session_start();
require_once 'dbconnect.php';

// アカウント作成処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $username = $_POST['username'];

    // パスワードの一致チェック
    if ($password !== $confirmPassword) {
        $errorMessage = 'パスワードが一致しません。';
    } else {
        // パスワードをSHA256でハッシュ化
        $hashedPassword = hash('sha256', $password);

        // アカウント作成日時を取得
        $accountCreatedTime = date("Y-m-d H:i:s");

        // ユーザーの重複チェッククエリ
        $checkUserSql = "SELECT * FROM users WHERE email = :email";
        $stmt = $dbh->prepare($checkUserSql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        // ユーザーの重複チェック
        if ($stmt->rowCount() > 0) {
            $errorMessage = '既に存在するメールアドレスです。';
        } else {
            // 新しいユーザーをデータベースに挿入
            $insertUserSql = "INSERT INTO users (email, password, username, account_created_time) VALUES (:email, :password, :username, :accountCreatedTime)";
            $stmt = $dbh->prepare($insertUserSql);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':password', $hashedPassword, PDO::PARAM_STR);
            $stmt->bindValue(':username', $username, PDO::PARAM_STR);
            $stmt->bindValue(':accountCreatedTime', $accountCreatedTime, PDO::PARAM_STR);
            $stmt->execute();

            // ログインページにリダイレクト
            header('Location: ./login.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>bbs.256server｜アカウント作成</title>
    <link rel="stylesheet" href="sub.css">
</head>

<body>
    <main>
        <div class="create-main">
            <div class="create-main-margin">
                <h2>アカウント作成</h2>
                <?php if (isset($errorMessage)) : ?>
                    <p style="color: red;"><?php echo $errorMessage; ?></p>
                <?php endif; ?>
                <form action="create_account.php" method="POST">
                    <input type="text" id="username" class="post_word" name="username" required placeholder="ユーザー名"><br>
                    <input type="email" id="email" class="post_word" name="email" required placeholder="メールアドレス"><br>
                    <input type="password" id="password" class="post_word"  name="password" required placeholder="パスワード"><br>
                    <input type="password" id="confirm_password" class="post_word" name="confirm_password" required placeholder="パスワード確認"><br>
                    <a href="/bbs-rule.html">アカウントを作る前に読んでね</a><br>
                    <input type="submit" class="create_submit" value="アカウント作成する"><a href="/login.php">ログイン</a>
                </form>
                
            </div>
        </div>
    </main>
</body>

</html>
