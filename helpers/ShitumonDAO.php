<?php
require_once 'DAO.php';

// -----------------------------
// エンティティクラス
// -----------------------------
class Shitumon
{
    public int $shitu_number;        // 質問番号
    public ?string $shitu_title;     // 質問タイトル
    public string $shitu_content;    // 質問内容
    public int $reception_status;    // 質問受付状態
    public string $asked_date;       // 質問日
    public string $update_at;        // 更新日
    public ?string $area_number;     // 出題分野番号
    public ?string $area_name;       // 分野名
    public ?int $s_number = null;
    public ?int $user_id = null;     // ユーザーID
    public ?int $shitu_count = null; // 質問数
}

class ShituAnswer
{
    public int $ans_number;      // 回答番号
    public int $shitu_number;    // 質問番号
    public string $ans_content;  // 回答内容
    public string $answer_date;  // 回答日
    public string $update_at;    // 更新日
}

// -----------------------------
// DAO クラス
// -----------------------------
class ShitumonDAO
{
    // 質問一覧取得
    public function getAll(): array
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM shitumon ORDER BY shitu_number DESC";
        $stmt = $dbh->query($sql);

        $data = [];
        while ($row = $stmt->fetchObject(Shitumon::class)) {
            $data[] = $row;
        }
        return $data;
    }

    // 質問番号で取得
    public function getByNumber(int $shitu_number): ?Shitumon
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM shitumon WHERE shitu_number = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $shitu_number, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchObject(Shitumon::class) ?: null;
    }

    // 質問登録
    public function insert(
        string $shitu_title,
        string $shitu_content,
        int $reception_status = 1,
        ?string $area_number = null,
        ?string $area_name = null,
        ?int $user_id = null
    ): bool {
        $dbh = DAO::get_db_connect();

        $sql = <<<SQL
INSERT INTO shitumon
(shitu_title, shitu_content, reception_status, asked_date, update_at, area_number, area_name, user_id)
VALUES (?, ?, ?, GETDATE(), GETDATE(), ?, ?, ?)
SQL;

        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $shitu_title, PDO::PARAM_STR);
        $stmt->bindValue(2, $shitu_content, PDO::PARAM_STR);
        $stmt->bindValue(3, $reception_status, PDO::PARAM_INT);
        $stmt->bindValue(4, $area_number, $area_number === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(5, $area_name, $area_name === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(6, $user_id, $user_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);

        return $stmt->execute();
    }

    // 質問更新
    public function update(
        int $shitu_number,
        string $shitu_title,
        string $shitu_content,
        int $reception_status = 1,
        ?string $area_number = null,
        ?string $area_name = null
    ): bool {
        $dbh = DAO::get_db_connect();

        $sql = <<<SQL
UPDATE shitumon SET
    shitu_title = ?,
    shitu_content = ?,
    reception_status = ?,
    area_number = ?,
    area_name = ?,
    update_at = GETDATE()
WHERE shitu_number = ?
SQL;

        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $shitu_title, PDO::PARAM_STR);
        $stmt->bindValue(2, $shitu_content, PDO::PARAM_STR);
        $stmt->bindValue(3, $reception_status, PDO::PARAM_INT);
        $stmt->bindValue(4, $area_number, $area_number === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(5, $area_name, $area_name === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(6, $shitu_number, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // 質問削除
    public function delete(int $shitu_number): bool
    {
        $dbh = DAO::get_db_connect();
        $sql = "DELETE FROM shitumon WHERE shitu_number = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $shitu_number, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // 回答一覧取得
    public function getAnswers(int $shitu_number): array
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM shitu_answer WHERE shitu_number = ? ORDER BY ans_number ASC";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $shitu_number, PDO::PARAM_INT);
        $stmt->execute();

        $data = [];
        while ($row = $stmt->fetchObject(ShituAnswer::class)) {
            $data[] = $row;
        }
        return $data;
    }

    // 回答追加
    public function addAnswer(int $shitu_number, string $ans_content): bool
    {
        $dbh = DAO::get_db_connect();
        $sql = "INSERT INTO shitu_answer (shitu_number, ans_content, answer_date, update_at)
                VALUES (?, ?, GETDATE(), GETDATE())";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $shitu_number, PDO::PARAM_INT);
        $stmt->bindValue(2, $ans_content, PDO::PARAM_STR);

        return $stmt->execute();
    }

    // 質問＋回答削除（トランザクション）
    public function deleteWithAnswers(int $shitu_number): bool
    {
        $dbh = DAO::get_db_connect();

        try {
            $dbh->beginTransaction();

            $stmt = $dbh->prepare("DELETE FROM shitu_answer WHERE shitu_number = ?");
            $stmt->bindValue(1, $shitu_number, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $dbh->prepare("DELETE FROM shitumon WHERE shitu_number = ?");
            $stmt->bindValue(1, $shitu_number, PDO::PARAM_INT);
            $stmt->execute();

            $dbh->commit();
            return true;
        } catch (PDOException $e) {
            $dbh->rollBack();
            throw $e;
        }
    }

    // 分野ごとの質問一覧
    public function getByArea(?string $area_number): array
    {
        $dbh = DAO::get_db_connect();

        if ($area_number !== null) {
            $sql = "SELECT * FROM shitumon WHERE area_number = ? ORDER BY shitu_number DESC";
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(1, $area_number, PDO::PARAM_STR);
            $stmt->execute();
        } else {
            $stmt = $dbh->query("SELECT * FROM shitumon ORDER BY shitu_number DESC");
        }

        $data = [];
        while ($row = $stmt->fetchObject(Shitumon::class)) {
            $data[] = $row;
        }
        return $data;
    }

    // ユーザー別質問一覧
    public function getAllByUser(int $user_id): array
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM shitumon WHERE user_id = ? ORDER BY shitu_number DESC";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $data = [];
        while ($row = $stmt->fetchObject(Shitumon::class)) {
            $data[] = $row;
        }
        return $data;
    }

    // 受付状態更新
    public function updateReceptionStatus(int $shitu_number, int $status): bool
    {
        $dbh = DAO::get_db_connect();
        $sql = "UPDATE shitumon SET reception_status = ? WHERE shitu_number = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $status, PDO::PARAM_INT);
        $stmt->bindValue(2, $shitu_number, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // 分野別ページング＋並び順取得
    public function getAllByAreaOrderPage(?string $area_number, string $order, int $page, int $perPage): array
    {
        $dbh = DAO::get_db_connect();
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT s.*, c.area_name
                FROM shitumon s
                LEFT JOIN q_categories c ON s.area_number = c.area_number";

        $params = [];
        if (!empty($area_number)) {
            $sql .= " WHERE s.area_number = :area_number";
            $params[':area_number'] = $area_number;
        }

        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';
        $sql .= " ORDER BY s.asked_date $order
                  OFFSET :offset ROWS
                  FETCH NEXT :limit ROWS ONLY";

        $stmt = $dbh->prepare($sql);

        if (isset($params[':area_number'])) {
            $stmt->bindValue(':area_number', $params[':area_number'], PDO::PARAM_STR);
        }
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);

        $stmt->execute();

        $data = [];
        while ($row = $stmt->fetchObject(Shitumon::class)) {
            $data[] = $row;
        }

        return $data;
    }

    // 分野別件数取得
    public function getCountByArea(?string $area_number): int
    {
        $dbh = DAO::get_db_connect();

        if (!empty($area_number)) {
            $sql = "SELECT COUNT(*) as cnt FROM shitumon WHERE area_number = :area_number";
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':area_number', $area_number, PDO::PARAM_STR);
            $stmt->execute();
        } else {
            $stmt = $dbh->query("SELECT COUNT(*) as cnt FROM shitumon");
        }

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$row['cnt'];
    }
}
