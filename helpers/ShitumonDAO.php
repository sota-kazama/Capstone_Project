<?php
require_once __DIR__ . '/DAO.php';

class ShitumonDAO
{
    private $conn;

    public function __construct()
    {
        // DAOクラスの静的メソッドで接続取得
        $this->conn = DAO::get_db_connect();
    }

    // 質問一覧取得
    public function getAll()
    {
        $sql = "SELECT * FROM shitumon ORDER BY shitu_number DESC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll();
    }

    // 質問番号で取得
    public function getByNumber($shitu_number)
    {
        $sql = "SELECT * FROM shitumon WHERE shitu_number = :num";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':num', $shitu_number, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // 質問登録// DAOのinsert例
    public function insert($shitu_content, $s_number = null, $reception_status = 1)
    {
        $sql = "INSERT INTO shitumon (shitu_content, s_number, reception_status) 
            VALUES (:content, :snum, :status)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':content', $shitu_content, PDO::PARAM_STR);

        if ($s_number === null) {
            $stmt->bindValue(':snum', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':snum', $s_number, PDO::PARAM_INT);
        }

        $stmt->bindValue(':status', $reception_status, PDO::PARAM_INT);
        return $stmt->execute();
    }





    // 質問更新
    public function update($shitu_number, $shitu_content, $s_number = null, $reception_status = 1)
    {
        $sql = "UPDATE shitumon 
                SET shitu_content = :content, s_number = :snum, reception_status = :status, update_at = GETDATE()
                WHERE shitu_number = :num";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':content', $shitu_content, PDO::PARAM_STR);
        $stmt->bindValue(':snum', $s_number, PDO::PARAM_INT);
        $stmt->bindValue(':status', $reception_status, PDO::PARAM_INT);
        $stmt->bindValue(':num', $shitu_number, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // 質問削除
    public function delete($shitu_number)
    {
        $sql = "DELETE FROM shitumon WHERE shitu_number = :num";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':num', $shitu_number, PDO::PARAM_INT);
        return $stmt->execute();
    }



    public function getAnswers($shitu_number)
    {
        $sql = "SELECT * FROM shitu_answer WHERE shitu_number = :num ORDER BY ans_number ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':num', $shitu_number, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // 回答を追加
    public function addAnswer($shitu_number, $ans_content)
    {
        $sql = "INSERT INTO shitu_answer (shitu_number, ans_content) VALUES (:num, :content)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':num', $shitu_number, PDO::PARAM_INT);
        $stmt->bindValue(':content', $ans_content, PDO::PARAM_STR);
        return $stmt->execute();
    }


        public function deleteWithAnswers($shitu_number)
    {
        try {
            // トランザクション開始
            $this->conn->beginTransaction();

            // 1. 関連する回答を削除
            $sql1 = "DELETE FROM shitu_answer WHERE shitu_number = :num";
            $stmt1 = $this->conn->prepare($sql1);
            $stmt1->bindValue(':num', $shitu_number, PDO::PARAM_INT);
            $stmt1->execute();

            // 2. 質問本体を削除
            $sql2 = "DELETE FROM shitumon WHERE shitu_number = :num";
            $stmt2 = $this->conn->prepare($sql2);
            $stmt2->bindValue(':num', $shitu_number, PDO::PARAM_INT);
            $stmt2->execute();

            // コミット
            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }
}
