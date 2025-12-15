<?php
require_once 'DAO.php';

class BookMark
{
    public int $user_id; //ユーザーID
    public int $label_id;  
    public int $q_number;  //問題番号  
    public int $label;  //しおり    
    public bool $bookmark;  //ラベル    
    public date $created_ad; //登録日
    public date $update_at;  //更新日
}

class BookMarkDAO{
    //問題情報取得
    public function getBookMark(int $q_number) {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM question_data WHERE q_number = :q_number";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':q_number', $q_number, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>