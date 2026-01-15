<?php
require_once 'DAO.php';

class Bug {
    public int $bug_id;        // バグID
    public string $bug_info;   // バグ内容
    public string $created_at; // 登録日
}

class BugDAO {

    // 全バグ情報を取得
    public function getAll(): array
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM bugs ORDER BY bug_id";
        $stmt = $dbh->query($sql);

        $data = [];
        while ($row = $stmt->fetchObject('Bug')) {
            $data[] = $row;
        }
        return $data;
    }

    // 最新3件のバグ情報を取得
    public function getRecent(): array
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT TOP 3 * FROM bugs ORDER BY bug_id DESC";
        $stmt = $dbh->query($sql);

        $data = [];
        while ($row = $stmt->fetchObject('Bug')) {
            $data[] = $row;
        }
        return $data;
    }

    // バグを追加
    public function insert(string $bug_info)
    {
        $dbh = DAO::get_db_connect();
        $sql = "INSERT INTO bugs (bug_info, created_at) VALUES (:bug_info, GETDATE())";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':bug_info', $bug_info, PDO::PARAM_STR);
        $stmt->execute();
    }

    // バグ情報を更新
    public function update(int $bug_id, string $bug_info)
    {
        $dbh = DAO::get_db_connect();
        $sql = "UPDATE bugs SET bug_info = :bug_info WHERE bug_id = :bug_id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':bug_info', $bug_info, PDO::PARAM_STR);
        $stmt->bindValue(':bug_id', $bug_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    // バグを削除
    public function delete(int $bug_id)
    {
        $dbh = DAO::get_db_connect();
        $sql = "DELETE FROM bugs WHERE bug_id = :bug_id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':bug_id', $bug_id, PDO::PARAM_INT);
        $stmt->execute();
    }
}