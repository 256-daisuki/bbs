<?php 

require_once '../dbconnect.php';

class UserLogic {
    /**
     * ユーザーを登録する
     * @param array $userData
     * @return bool $result
     */
    public static function createUser($userData) {
        $result = false;
        $sql = 'INSERT INFO users (name, email, 
        password) VALUES (?, ?, ?)';

        // ユーザーデータを配列に入れる
        $arr = [];
        $arr[] = $userData['username'];
        $arr[] = $userData['email'];
        $arr[] = password_hash($userData['password'],
        PASSWORD_DEFAULT);

        try {
            $stmt = connect()->prepare($sql);
            $result = $stmt->execute($arr);
            return $result;
        } catch(\Exception $e) {
            return $result;
        }
    }
}
?>