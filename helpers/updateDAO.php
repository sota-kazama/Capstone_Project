<?php
require_once 'DAO.php';

class update {
    public int $update_id;
    public string $up_info;
    public string $created_ad;
}

class UpdateDAO {
    public function getAll()
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM update_info";
        $stmt = $dbh->query($sql);

        $data = [];
        while ($row = $stmt->fetchObject('update')) {
            $data[] = $row;
        }
        return $data;
    }

        public function getInfo()
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT TOP 3 * FROM update_info ORDER BY update_id DESC";
        $stmt = $dbh->query($sql);

        $data = [];
        while ($row = $stmt->fetchObject('update')) {
            $data[] = $row;
        }
        return $data;
    }

    public function insert(string $up_info)
    {
        $dbh = DAO::get_db_connect();
        $sql = "INSERT INTO update_info (up_info, created_ad)
                VALUES (:up_info, GETDATE())";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':up_info', $up_info, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function update(int $update_id, string $up_info)
    {
        $dbh = DAO::get_db_connect();
        $sql = "UPDATE update_info
                SET up_info = :up_info
                WHERE update_id = :update_id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':up_info', $up_info, PDO::PARAM_STR);
        $stmt->bindValue(':update_id', $update_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function delete(int $update_id)
    {
        $dbh = DAO::get_db_connect();
        $sql = "DELETE FROM update_info WHERE update_id = :update_id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':update_id', $update_id, PDO::PARAM_INT);
        $stmt->execute();
    }
}