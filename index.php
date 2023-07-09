<?php
session_start();

// セッションにログイン情報がない場合はログインページにリダイレクト
if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}

$email = $_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>Home</title>
</head>

<body>
    <h2>Welcome, <?php echo $email; ?>!</h2>
    <p>You are now logged in.</p>
    <a href="logout.php">Logout</a>
</body>

</html>
