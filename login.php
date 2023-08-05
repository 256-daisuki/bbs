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
        // ログイン成功
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
    <div class="login-main">
        <div class="login-main-margin">
            <h2>bbsにログイン</h2>
            <?php if (isset($errorMessage)) : ?>
                <p style="color: red;"><?php echo $errorMessage; ?></p>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <input type="email" id="email" class="post_word" name="email" required placeholder="メールアドレス"><br>
                <input type="password" id="password" class="post_word" name="password" required placeholder="パスワード"><br>
                <input type="submit" value="ログイン" class="login_submit">
            </form>
            <a href="/create_account.php">新規登録</a><br>
            <a href="https://256server.com/bbs/index.php">誰でも書き込めるbbs</a>
        </div>
    </div>
</body>
</html>