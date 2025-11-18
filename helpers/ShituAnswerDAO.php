<?php
require_once 'DAO.php';

class ShituAnswerDAO {
    private $conn;


    // 回答一覧取得（質問番号で）
    public function getByShituNumber($shitu_number) {
        $sql = "SELECT * FROM shitu_answer WHERE shitu_number = :num ORDER BY answer_date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':num', $shitu_number, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 回答登録
    public function insert($shitu_number, $ans_content) {
        $sql = "INSERT INTO shitu_answer (shitu_number, ans_content) 
                VALUES (:num, :content)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':num', $shitu_number, PDO::PARAM_INT);
        $stmt->bindValue(':content', $ans_content, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // 回答更新
    public function update($ans_number, $ans_content) {
        $sql = "UPDATE shitu_answer 
                SET ans_content = :content, update_at = GETDATE()
                WHERE ans_number = :num";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':content', $ans_content, PDO::PARAM_STR);
        $stmt->bindValue(':num', $ans_number, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // 回答削除
    public function delete($ans_number) {
        $sql = "DELETE FROM shitu_answer WHERE ans_number = :num";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':num', $ans_number, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
