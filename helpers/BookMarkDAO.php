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
    public function getUserLabel(int $user_id, $label_id) {
        $dbh = DAO::get_db_connect();
        $sql = "select * from u_labels where user_id = :user_id and label = :label";
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
    public function insertBookmark(Bookmark $bookmark)
    {
        $dbh = DAO::get_db_connect();
        if(!$this->getUserLabel($user_id,$label_id)){
            $sql = "INSERT INTO u_labels (user_id, label_id, label, create_ad, update_at)
                    VALUES (:user_id, :label_id, :label, GETDATE(), GETDATE())";
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':user_id', $bookmark->user_id, PDO::PARAM_INT);
            $stmt->bindValue(':label_id', $bookmark->label_id, PDO::PARAM_INT);
            $stmt->bindValue(':label', $bookmark->q_number, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            $sql = "UPDATE u_labels
                    SET area_name = :area_name,
                    s_number = :s_number,
                    update_at = GETDATE()
                    WHERE area_number = :area_number";
                    
        }
    }

}
