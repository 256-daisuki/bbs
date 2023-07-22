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
    <title>ログイン</title>
</head>

<body>
    <h2>ログイン</h2>
    <?php if (isset($errorMessage)) : ?>
        <p style="color: red;"><?php echo $errorMessage; ?></p>
    <?php endif; ?>
    <form action="login.php" method="POST">
        <label for="email">メールアドレス:</label>
        <input type="email" id="email" name="email" required><br>
        <label for="password">パスワード:</label>
        <input type="password" id="password" name="password" required><br>
        <input type="submit" value="Login">
    </form>
    <a href="/create_account.php">新規登録</a>
</body>
</html>
