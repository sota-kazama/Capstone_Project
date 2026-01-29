<?php
require_once 'DAO.php';
class BookMark
{
    public int $user_id; //ユーザーID
    public int $label_id;  
    public int $q_number;  //問題番号  
    public int $label;  //しおり    
    public bool $bookmark;  //ラベル    
    public string $create_ad; //登録日
    public string $update_at;  //更新日
}
class BookMarkDAO
{
    //ブックマーク存在確認
    public function getUserLabel(int $user_id, int $label_id) {
        $dbh = DAO::get_db_connect();
        $sql = "select * from u_labels where user_id = :user_id and label_id = :label_id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':label_id', $label_id, PDO::PARAM_INT);
        $stmt->execute();
        if($stmt -> fetch() !== false){
            return true;
        } else {
            return false;
        }
    }

    //ブックマーク登録または更新
    public function insertOrUpdateBookmark(int $user_id, int $label_id, int $q_number)
    {
        $dbh = DAO::get_db_connect();
        if(!$this->getUserLabel($user_id,$label_id)){
            $sql = "INSERT INTO u_labels (user_id, label_id, q_number, create_ad, update_at)
                    VALUES (:user_id, :label_id, :q_number, GETDATE(), GETDATE())";
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindValue(':label_id', $label_id, PDO::PARAM_INT);
            $stmt->bindValue(':q_number', $q_number, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            $sql = "UPDATE u_labels
                    SET q_number = :q_number, update_at = GETDATE()
                    WHERE user_id = :user_id and label_id = :label_id";
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindValue(':label_id', $label_id, PDO::PARAM_INT);
            $stmt->bindValue(':q_number', $q_number, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

}
