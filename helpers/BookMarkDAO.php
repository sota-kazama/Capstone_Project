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
class BookMarkDAO
{
    public function saveBookmark(int $user_id, int $q_number): void
    {
        $dbh = DAO::get_db_connect();

        $sql=

        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':q_number', $q_number, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function insertBookmark(int $q_number): void
    {
        $dbh = DAO::get_db_connect();
        // INSERT時に access_date も設定する場合は、SQLに追加が必要です
        $sql = "INSERT INTO u_labels (q_number, bookmark, created_ad, update_at)
                VALUES ()";

        $stmt = $dbh->prepare($sql);


        $stmt->bindValue(':mail_address', $member->mail_address, PDO::PARAM_STR);
        $stmt->bindValue(':user_name', $member->user_name, PDO::PARAM_STR);
        $stmt->bindValue(':pass_word', $password, PDO::PARAM_STR);

        $stmt->execute();
    }
}
