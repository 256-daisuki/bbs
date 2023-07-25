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
            header('Location: login.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>新規登録</title>
</head>

<body>
    <h2>新規登録</h2>
    <?php if (isset($errorMessage)) : ?>
        <p style="color: red;"><?php echo $errorMessage; ?></p>
    <?php endif; ?>
    <form action="create_account.php" method="POST">
        <label for="username">ユーザー名:</label><input type="text" id="username" name="username" required><br>
        <label for="email">メールアドレス:</label>
        <input type="email" id="email" name="email" required><br>
        <label for="password">パスワード:</label>
        <input type="password" id="password" name="password" required><br>
        <label for="confirm_password">パスワード確認:</label>
        <input type="password" id="confirm_password" name="confirm_password" required><br>
        <input type="submit" value="Create Account"><a href="/bbs-rule.html">アカウントを作る前に読んでね</a>
    </form>
    <a href="/login.php">ログイン</a>
</body>

</html>
