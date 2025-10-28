<?php
require_once 'DAO.php';

class Shikaku
{
    public string $s_number;   // 資格番号
    public string $s_name;     // 資格名
    public string $created_ad; // 登録日
    public string $update_at;  // 更新日
}

class ShikakuDAO
{
    /**
     * 資格一覧を取得
     */
    public function getAll()
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT s_number, s_name, created_ad, update_at FROM shikaku ORDER BY s_number";
        $stmt = $dbh->query($sql);

        $data = [];
        while ($row = $stmt->fetchObject('Shikaku')) {
            $data[] = $row;
        }
        return $data;
    }

    /**
     * 資格を追加
     */
    public function insert(string $s_name): bool
    {
        $dbh = DAO::get_db_connect();
        $sql = "INSERT INTO shikaku (s_name, created_ad, update_at)
                VALUES (?, GETDATE(), GETDATE())";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $s_name, PDO::PARAM_STR);
        return $stmt->execute();
    }

    /**
     * 資格を更新
     */
    public function update(int $s_number, string $s_name): bool
    {
        $dbh = DAO::get_db_connect();
        $sql = "UPDATE shikaku SET s_name = ?, update_at = GETDATE() WHERE s_number = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $s_name, PDO::PARAM_STR);
        $stmt->bindValue(2, $s_number, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * 資格を削除
     */
    public function delete(int $s_number): bool
    {
        $dbh = DAO::get_db_connect();
        $sql = "DELETE FROM shikaku WHERE s_number = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $s_number, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
