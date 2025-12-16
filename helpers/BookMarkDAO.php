<?php
require_once 'DAO.php';

class BookMark
{
    public int $user_id; //ユーザーID
    public int $label_id;  
    public int $q_number;  //問題番号  
    public int $label;  //    
    public int $bookmark;  //ラベル    
    public string $created_ad; //登録日
    public string $update_at;  //更新日
}

class BookMarkDAO
{
    // ブックマーク存在確認
    public function bookMark_exists(int $bookmark) {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM u_labels WHERE bookmark = :bookmark";
    } 
}
