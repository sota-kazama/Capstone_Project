<?php
require_once 'DAO.php';

// -----------------------------
// エンティティクラス
// -----------------------------
class Shitumon
{
    public int $shitu_number;        // 質問番号
    public ?string $shitu_title;     // 質問タイトル（NULLを許可）
    public string $shitu_content;    // 質問内容
    public int $reception_status;    // 質問受付状態
    public string $asked_date;       // 質問日
    public string $update_at;        // 更新日
    public ?string $area_number;     // 出題分野番号
    public ?string $area_name;       // 分野名
    public ?int $s_number = null;
    public int $shitu_count = 0;     // 回答数
}

class ShituAnswer
{
    public int $ans_number;          // 回答番号
    public int $shitu_number;        // 質問番号
    public string $ans_content;      // 回答内容
    public string $answer_date;      // 回答日
    public string $update_at;        // 更新日
}

// -----------------------------
// DAO クラス
// -----------------------------
class ShitumonDAO
{

    // 質問一覧取得
    public function getAll()
    {
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
    public function getByNumber(int $shitu_number)
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM shitumon WHERE shitu_number = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $shitu_number, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchObject('Shitumon');
    }

    // -----------------------------
    // 質問登録（s_number削除済み）
    // -----------------------------
    public function insert(
        string $shitu_title,
        string $shitu_content,
        int $reception_status = 1,
        ?string $area_number = null,
        ?string $area_name = null
    ) {
        $dbh = DAO::get_db_connect();

        $sql = "INSERT INTO shitumon 
                (shitu_title, shitu_content, reception_status, asked_date, update_at, area_number, area_name)
                VALUES (?, ?, ?, GETDATE(), GETDATE(), ?, ?)";

        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $shitu_title, PDO::PARAM_STR);
        $stmt->bindValue(2, $shitu_content, PDO::PARAM_STR);
        $stmt->bindValue(3, $reception_status, PDO::PARAM_INT);
        $stmt->bindValue(4, $area_number, $area_number === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(5, $area_name, $area_name === null ? PDO::PARAM_NULL : PDO::PARAM_STR);

        return $stmt->execute();
    }

    // -----------------------------
    // 質問更新
    // -----------------------------
    public function update(
        int $shitu_number,
        string $shitu_title,
        string $shitu_content,
        int $reception_status = 1,
        ?string $area_number = null,
        ?string $area_name = null
    ) {
        $dbh = DAO::get_db_connect();

        $sql = "UPDATE shitumon SET
                    shitu_title = ?,
                    shitu_content = ?,
                    reception_status = ?,
                    area_number = ?,
                    area_name = ?,
                    update_at = GETDATE()
                WHERE shitu_number = ?";

        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $shitu_title, PDO::PARAM_STR);
        $stmt->bindValue(2, $shitu_content, PDO::PARAM_STR);
        $stmt->bindValue(3, $reception_status, PDO::PARAM_INT);
        $stmt->bindValue(4, $area_number, $area_number === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(5, $area_name, $area_name === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(6, $shitu_number, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // -----------------------------
    // 質問削除
    // -----------------------------
    public function delete(int $shitu_number)
    {
        $dbh = DAO::get_db_connect();
        $sql = "DELETE FROM shitumon WHERE shitu_number = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $shitu_number, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // -----------------------------
    // 回答一覧取得
    // -----------------------------
    public function getAnswers(int $shitu_number)
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM shitu_answer WHERE shitu_number = ? ORDER BY ans_number ASC";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $shitu_number, PDO::PARAM_INT);
        $stmt->execute();

        $data = [];
        while ($row = $stmt->fetchObject('ShituAnswer')) {
            $data[] = $row;
        }
        return $data;
    }

    // -----------------------------
    // 回答追加
    // -----------------------------
    public function addAnswer(int $shitu_number, string $ans_content)
    {
        $dbh = DAO::get_db_connect();

        try {
            $dbh->beginTransaction();

            // 回答追加
            $sql1 = "
            INSERT INTO shitu_answer
            (shitu_number, ans_content, answer_date, update_at)
            VALUES (?, ?, GETDATE(), GETDATE())
        ";
            $stmt1 = $dbh->prepare($sql1);
            $stmt1->bindValue(1, $shitu_number, PDO::PARAM_INT);
            $stmt1->bindValue(2, $ans_content, PDO::PARAM_STR);
            $stmt1->execute();

            // 回答数 +1
            $sql2 = "
            UPDATE shitumon
            SET shitu_count = shitu_count + 1,
                update_at = GETDATE()
            WHERE shitu_number = ?
        ";
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


    // -----------------------------
    // 質問と回答の両方削除（トランザクション）
    // -----------------------------
    public function deleteWithAnswers(int $shitu_number)
    {
        $dbh = DAO::get_db_connect();
        try {
            $dbh->beginTransaction();

            $sql1 = "DELETE FROM shitu_answer WHERE shitu_number = ?";
            $stmt1 = $dbh->prepare($sql1);
            $stmt1->bindValue(1, $shitu_number, PDO::PARAM_INT);
            $stmt1->execute();

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

    // 分野ごとの質問一覧取得
    public function getByArea(?string $area_number)
    {
        $dbh = DAO::get_db_connect();
        if ($area_number) {
            $sql = "SELECT * FROM shitumon WHERE area_number = ? ORDER BY shitu_number DESC";
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(1, $area_number, PDO::PARAM_STR);
            $stmt->execute();
        } else {
            $sql = "SELECT * FROM shitumon ORDER BY shitu_number DESC";
            $stmt = $dbh->query($sql);
        }

        $data = [];
        while ($row = $stmt->fetchObject('Shitumon')) {
            $data[] = $row;
        }
        return $data;
    }



    public function getAllByAreaOrderPage(string $area_number = '', string $order = 'DESC', int $page = 1, int $perPage = 10)
    {
        $dbh = DAO::get_db_connect();
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';
        $offset = ($page - 1) * $perPage;

        if ($area_number !== '') {
            $sql = "SELECT * FROM shitumon WHERE area_number = ? ORDER BY shitu_number $order OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(1, $area_number, PDO::PARAM_STR);
            $stmt->bindValue(2, $offset, PDO::PARAM_INT);
            $stmt->bindValue(3, $perPage, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            $sql = "SELECT * FROM shitumon ORDER BY shitu_number $order OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(1, $offset, PDO::PARAM_INT);
            $stmt->bindValue(2, $perPage, PDO::PARAM_INT);
            $stmt->execute();
        }

        $data = [];
        while ($row = $stmt->fetchObject('Shitumon')) {
            $data[] = $row;
        }
        return $data;
    }

    // 総件数取得（ページネーション用）
    public function getCountByArea(string $area_number = '')
    {
        $dbh = DAO::get_db_connect();
        if ($area_number !== '') {
            $sql = "SELECT COUNT(*) FROM shitumon WHERE area_number = ?";
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(1, $area_number, PDO::PARAM_STR);
            $stmt->execute();
        } else {
            $sql = "SELECT COUNT(*) FROM shitumon";
            $stmt = $dbh->query($sql);
        }
        return (int)$stmt->fetchColumn();
    }
}
