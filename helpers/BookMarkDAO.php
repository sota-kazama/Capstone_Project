<?php
require_once 'DAO.php';

class BookMark
{
    public int $user_id; //ユーザーID
    public int $label_id;  
    public int $q_number;  //問題番号  
    public int $label;  //しおり    
    public bool $bookmark;  //ラベル    
    public string $created_ad; //登録日
    public string $update_at;  //更新日
}

class LabelDAO
{
    // ブックマーク保存（あれば更新）
    public function saveBookmark(int $user_id, int $q_number): void
    {
        $dbh = DAO::get_db_connect();

        $sql = "
            INSERT INTO u_labels (user_id, q_number, bookmark)
            VALUES (:user_id, :q_number, 1)
            ON DUPLICATE KEY UPDATE
                q_number = VALUES(q_number),
                bookmark = 1,
                update_at = CURRENT_TIMESTAMP
        ";

        $stmt = $dbh->prepare($sql);
        $stmt->execute([
            ':user_id'  => $user_id,
            ':q_number' => $q_number
        ]);
    }

    // ブックマーク取得
    public function getBookmark(int $user_id): ?Label
    {
        $dbh = DAO::get_db_connect();

        $sql = "
            SELECT * FROM u_labels
            WHERE user_id = :user_id AND bookmark = 1
        ";

        $stmt = $dbh->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Label');

        $label = $stmt->fetch();
        return $label instanceof Label ? $label : null;
    }
}
