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
    public function saveBookmark(int $user_id, int $q_number): void
    {
        $dbh = DAO::get_db_connect();

        $sql = "
        MERGE u_labels AS target
        USING (SELECT :user_id AS user_id, :q_number AS q_number) AS source
        ON target.user_id = source.user_id
           AND target.q_number = source.q_number
        WHEN MATCHED THEN
            UPDATE SET
                bookmark = 1,
                update_at = GETDATE()
        WHEN NOT MATCHED THEN
            INSERT (user_id, q_number, bookmark, created_ad, update_at)
            VALUES (:user_id, :q_number, 1, GETDATE(), GETDATE());
        ";

        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':q_number', $q_number, PDO::PARAM_INT);
        $stmt->execute();
    }
}
