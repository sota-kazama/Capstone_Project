<?php
require_once 'DAO.php';

class Question
{
    public int $q_number;              // 問題ID
    public string $q_content;          // 問題文
    public ?string $q_source;          // 出典
    public ?string $answers;           // JSON文字列
    public ?string $correct_answers;   // JSON文字列
    public ?string $image_path;        // 画像パス
    public ?string $choices1;          // 選択肢1
    public ?string $choices2;          // 選択肢2
    public ?string $choices3;          // 選択肢3
    public ?string $choices4;          // 選択肢4
    public ?string $created_ad;        // 登録日
    public ?string $update_ad;         // 更新日
}


class QuestionDAO
{
    /** 全問題取得 */
    public function getAll(): array
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM question_data ORDER BY q_number";
        $stmt = $dbh->query($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Question');
        return $stmt->fetchAll();
    }
    /** インデックス順で取得（getAllと同じ処理） */
    public function changeIndex(): array
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM question_data ORDER BY q_number";
        $stmt = $dbh->query($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Question');
        return $stmt->fetchAll();
    }

     /** 新規問題追加 */
    public function insert(array $q): int
    {
        $dbh = DAO::get_db_connect();
        $sql = "
            INSERT INTO question_data
            (
                q_content,
                q_source,
                answers,
                correct_answers,
                image_path,
                choices1,
                choices2,
                choices3,
                choices4,
                created_ad,
                update_ad
            )
            VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, GETDATE(), GETDATE())
        ";

        $stmt = $dbh->prepare($sql);
        $result = $stmt->execute([
            $q['q_content'],
            $q['q_source'],
            $q['answers'],
            $q['correct_answers'],
            $q['image_path'] ?? null,
            $q['choices1'],
            $q['choices2'],
            $q['choices3'],
            $q['choices4'],
        ]);

        if (!$result) {
            return 0;
        }

        return (int)$dbh->query("SELECT SCOPE_IDENTITY()")->fetchColumn();
    }

   /** 問題更新 */
    public function update(int $q_number, array $q): bool
    {
        $dbh = DAO::get_db_connect();
        $sql = "
            UPDATE question_data
            SET
                q_content = ?,
                q_source = ?,
                answers = ?,
                correct_answers = ?,
                image_path = ?,
                choices1 = ?,
                choices2 = ?,
                choices3 = ?,
                choices4 = ?,
                update_ad = GETDATE()
            WHERE q_number = ?
        ";

        $stmt = $dbh->prepare($sql);
        return $stmt->execute([
            $q['q_content'],
            $q['q_source'],
            $q['answers'],
            $q['correct_answers'],
            $q['image_path'] ?? null,
            $q['choices1'],
            $q['choices2'],
            $q['choices3'],
            $q['choices4'],
            $q_number
        ]);
    }

    /** 問題削除 */
    public function delete(int $q_number): bool
    {
        $dbh = DAO::get_db_connect();
        $sql = "DELETE FROM question_data WHERE q_number = ?";
        $stmt = $dbh->prepare($sql);
        return $stmt->execute([$q_number]);
    }

    /** 指定範囲の問題を取得（無限スクロール用） */
    public function getList(int $offset = 0, int $limit = 20, ?string $field = null): array
    {
        $dbh = DAO::get_db_connect();
        if ($field !== null) {
            $sql = "SELECT * FROM question_data 
                    WHERE q_number IN (
                        SELECT q_number FROM question_fields WHERE field_number = ?
                    )
                    ORDER BY q_number
                    OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";
            $stmt = $dbh->prepare($sql);
            $stmt->execute([$field, $offset, $limit]);
        } else {
            $sql = "SELECT * FROM question_data 
                    ORDER BY q_number 
                    OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";
            $stmt = $dbh->prepare($sql);
            $stmt->execute([$offset, $limit]);
        }
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Question');
        return $stmt->fetchAll();
    }

    /** 登録件数取得 */
    public function countAll(): int
    {
        $dbh = DAO::get_db_connect();
        return (int)$dbh->query("SELECT COUNT(*) FROM question_data")->fetchColumn();
    }

    /** ID指定で1件取得 */
    public function findById(int $q_number): ?Question
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM question_data WHERE q_number = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$q_number]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Question');
        return $stmt->fetch() ?: null;
    }

    /** 最後に登録した ID を取得 */
    public function getLastInsertId(): int
    {
        $dbh = DAO::get_db_connect();
        return (int)$dbh->query("SELECT SCOPE_IDENTITY()")->fetchColumn();
    }

    public function findQuestionById(int $q_number)
{
    $dbh = DAO::get_db_connect();
    $sql = "SELECT * FROM question_data WHERE q_number = :q_number";
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':q_number', $q_number, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_OBJ);
}

    /** 検索（問題文・出典） */
    public function search(string $keyword): array
    {
        $dbh = DAO::get_db_connect();
        $sql = "
            SELECT *
            FROM question_data
            WHERE q_content LIKE ?
               OR q_source LIKE ?
            ORDER BY q_number DESC
        ";

        $stmt = $dbh->prepare($sql);
        $kw = '%' . $keyword . '%';
        $stmt->execute([$kw, $kw]);

        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Question');
        return $stmt->fetchAll();
    }
}
?>
