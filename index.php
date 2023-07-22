<?php
session_start();
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
// DB接続関数
function connect()
{
    $host = 'localhost';
    $dbname = 'user';
    $user = 'php-login';
    $pass = '';
    
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    
    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        return $pdo;
    } catch(PDOException $e) {
        echo '接続失敗: ' . $e->getMessage();
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>Home</title>
</head>

<body>
    <h2>ようこそ<?php echo $username; ?>さん!</h2>
    <p>あなたは今ログインしています。</p>
    <a href="logout.php">ログアウト</a>
</body>

</html>
