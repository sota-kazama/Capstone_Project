<?php
require_once 'DAO.php';

class Shitumon {
    public int $shitu_number;        // 質問番号
    public string $shitu_content;    // 質問内容
    public ?int $s_number;           // 資格番号（NULLあり）
    public int $reception_status;    // 質問受付状態
    public string $asked_date;       // 質問日
    public string $update_at;        // 更新日
}

class ShituAnswer {
    public int $ans_number;          // 回答番号
    public int $shitu_number;        // 質問番号
    public string $ans_content;      // 回答内容
    public string $answer_date;      // 回答日
    public string $update_at;        // 更新日
}

class ShitumonDAO {

    // 質問一覧取得
    public function getAll() {
        $dbh = DAO::get_db_connect();

        $sql = "SELECT * FROM shitumon ORDER BY shitu_number DESC";
        $stmt = $dbh->query($sql);

        $data = [];
        while ($row = $stmt->fetchObject('Shitumon')) {
            $data[] = $row;
        }
        return $data;
    }

    // 質問番号で取得
    public function getByNumber(int $shitu_number) {
        $dbh = DAO::get_db_connect();

        $sql = "SELECT * FROM shitumon WHERE shitu_number = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $shitu_number, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchObject('Shitumon');
    }

    // 質問登録
    public function insert(string $shitu_content, ?int $s_number = null, int $reception_status = 1) {
        $dbh = DAO::get_db_connect();

        $sql = "INSERT INTO shitumon (shitu_content, s_number, reception_status)
                VALUES (?, ?, ?)";
        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(1, $shitu_content, PDO::PARAM_STR);
        if ($s_number === null) {
            $stmt->bindValue(2, null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(2, $s_number, PDO::PARAM_INT);
        }
        $stmt->bindValue(3, $reception_status, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // 質問更新
    public function update(int $shitu_number, string $shitu_content, ?int $s_number = null, int $reception_status = 1) {
        $dbh = DAO::get_db_connect();

        $sql = "UPDATE shitumon SET
                    shitu_content = ?,
                    s_number = ?,
                    reception_status = ?,
                    update_at = GETDATE()
                WHERE shitu_number = ?";
        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(1, $shitu_content, PDO::PARAM_STR);
        if ($s_number === null) {
            $stmt->bindValue(2, null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(2, $s_number, PDO::PARAM_INT);
        }
        $stmt->bindValue(3, $reception_status, PDO::PARAM_INT);
        $stmt->bindValue(4, $shitu_number, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // 質問削除
    public function delete(int $shitu_number) {
        $dbh = DAO::get_db_connect();

        $sql = "DELETE FROM shitumon WHERE shitu_number = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $shitu_number, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // 回答一覧取得
    public function getAnswers(int $shitu_number) {
        $dbh = DAO::get_db_connect();

        $sql = "SELECT * FROM shitu_answer
                WHERE shitu_number = ?
                ORDER BY ans_number ASC";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $shitu_number, PDO::PARAM_INT);
        $stmt->execute();

        $data = [];
        while ($row = $stmt->fetchObject('ShituAnswer')) {
            $data[] = $row;
        }
        return $data;
    }

    // 回答追加
    public function addAnswer(int $shitu_number, string $ans_content) {
        $dbh = DAO::get_db_connect();

        $sql = "INSERT INTO shitu_answer (shitu_number, ans_content)
                VALUES (?, ?)";
        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(1, $shitu_number, PDO::PARAM_INT);
        $stmt->bindValue(2, $ans_content, PDO::PARAM_STR);

        return $stmt->execute();
    }

    // 質問と回答の両方削除（トランザクション）
    public function deleteWithAnswers(int $shitu_number) {
        $dbh = DAO::get_db_connect();

        try {
            $dbh->beginTransaction();

            // 回答削除
            $sql1 = "DELETE FROM shitu_answer WHERE shitu_number = ?";
            $stmt1 = $dbh->prepare($sql1);
            $stmt1->bindValue(1, $shitu_number, PDO::PARAM_INT);
            $stmt1->execute();

            // 質問削除
            $sql2 = "DELETE FROM shitumon WHERE shitu_number = ?";
            $stmt2 = $dbh->prepare($sql2);
            $stmt2->bindValue(1, $shitu_number, PDO::PARAM_INT);
            $stmt2->execute();

            $dbh->commit();
            return true;

        } catch (PDOException $e) {
            $dbh->rollBack();
            throw $e;
        }
    }
}
?>
