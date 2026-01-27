<?php
require_once 'DAO.php';
class BookMark
{
    public int $user_id; //ユーザーID
<<<<<<< HEAD
    public int $label_id;  
    public int $q_number;  //問題番号  
    public int $label;  //しおり    
    public bool $bookmark;  //ラベル    
    public string $create_ad; //登録日
=======
    public int $label_id;
    public int $q_number;  //問題番号
    public int $label;  //しおり
    public bool $bookmark;  //ラベル
    public string $created_ad; //登録日
>>>>>>> b2e04b3a091f5e9a4501505059c6b732d4e5828a
    public string $update_at;  //更新日
}
class BookMarkDAO
{
    public function insertBookmark(Bookmark $bookmark): void
    {
        $dbh = DAO::get_db_connect();
        // INSERT時に access_date も設定する場合は、SQLに追加が必要です
        $sql = "INSERT INTO u_labels (user_id, label_id, label, create_ad, update_at)
                VALUES (:user_id, '', :label, GETDATE(), GETDATE())";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':user_id', $bookmark->user_id, PDO::PARAM_INT);
        $stmt->bindValue(':label', $bookmark->q_number, PDO::PARAM_INT);

        $stmt->execute();
    }

    //しおり存在確認
    public function getUserLabel(int $user_id , int $label) {
        $dbh = DAO::get_db_connect();
        $sql = "select * from u_labels where user_id = :user_id and label = :label";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':label', $label, PDO::PARAM_INT);
        $stmt->execute();
        if($stmt -> fetch() !== false){
            return true;
        } else {
            return false;
        }
    }
}
