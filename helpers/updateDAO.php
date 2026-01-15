<?php
require_once 'DAO.php';

class UpdateInfo {  // 更新情報エンティティ
    public int $update_id;   // ID
    public string $up_info;  // 更新内容
    public string $created_ad; // 登録日
}

class UpdateDAO {
    /** 全更新情報取得 */
    public function getAll(): array
    {
        $dbh = DAO::get_db_connect();
        $stmt = $dbh->query("SELECT * FROM update_info");
        $data = [];
        while ($row = $stmt->fetchObject(UpdateInfo::class)) $data[] = $row;
        return $data;
    }

    /** 最新3件取得 */
    public function getInfo(): array
    {
        $dbh = DAO::get_db_connect();
        $stmt = $dbh->query("SELECT TOP 3 * FROM update_info ORDER BY update_id DESC");
        $data = [];
        while ($row = $stmt->fetchObject(UpdateInfo::class)) $data[] = $row;
        return $data;
    }

    /** 更新情報追加 */
    public function insert(string $up_info): bool
    {
        $dbh = DAO::get_db_connect();
        $stmt = $dbh->prepare("INSERT INTO update_info (up_info, created_ad) VALUES (:up_info, GETDATE())");
        $stmt->bindValue(':up_info', $up_info, PDO::PARAM_STR);
        return $stmt->execute();
    }

    /** 更新情報更新 */
    public function update(int $update_id, string $up_info): bool
    {
        $dbh = DAO::get_db_connect();
        $stmt = $dbh->prepare("UPDATE update_info SET up_info = :up_info WHERE update_id = :update_id");
        $stmt->bindValue(':up_info', $up_info, PDO::PARAM_STR);
        $stmt->bindValue(':update_id', $update_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /** 更新情報削除 */
    public function delete(int $update_id): bool
    {
        $dbh = DAO::get_db_connect();
        $stmt = $dbh->prepare("DELETE FROM update_info WHERE update_id = :update_id");
        $stmt->bindValue(':update_id', $update_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}