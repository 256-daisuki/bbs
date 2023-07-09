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
        $_SESSION['email'] = $email;
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
    <title>Login</title>
</head>

<body>
    <h2>Login</h2>
    <?php if (isset($errorMessage)) : ?>
        <p style="color: red;"><?php echo $errorMessage; ?></p>
    <?php endif; ?>
    <form action="login.php" method="POST">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <input type="submit" value="Login">
    </form>
</body>

</html>
