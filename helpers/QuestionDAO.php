<?php
require_once 'DAO.php';

class Question
{
    public int $q_number;
    public string $q_content;
    public string $answer_content;
    public ?string $wrong_answer1;
    public ?string $wrong_answer2;
    public ?string $wrong_answer3;
    public ?string $q_source;
    public ?string $answers;           // JSON文字列
    public ?string $correct_answers;   // JSON文字列
    public ?string $image_path;
    public ?string $created_ad;        // 登録日
    public ?string $update_ad;         // 更新日
}

class QuestionDAO
{
    /** 全問題を取得 */
    public function getAll(): array
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM question_data ORDER BY q_number";
        $stmt = $dbh->query($sql);

        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Question');
        return $stmt->fetchAll();
    }
    public function changeIndex(): array
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM question_data ORDER BY q_number";
        $stmt = $dbh->query($sql);

        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Question');
        return $stmt->fetchAll();
    }
    
    /** 新規問題を追加（登録日・更新日を現在日時で設定） */
    public function insert(array $q): int
    {
        $dbh = DAO::get_db_connect();

        $sql = "INSERT INTO question_data 
                (q_content, answer_content, wrong_answer1, wrong_answer2, wrong_answer3,
                 q_source, answers, correct_answers, image_path, created_ad, update_ad)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, GETDATE(), GETDATE())";

        $stmt = $dbh->prepare($sql);
        $result = $stmt->execute([
            $q['q_content'], $q['answer_content'], $q['wrong_answer1'], $q['wrong_answer2'],
            $q['wrong_answer3'], $q['q_source'], $q['answers'], $q['correct_answers'],
            $q['image_path'] ?? null
        ]);

        if (!$result) return 0;

        // 新規登録後に自動採番された ID を取得
        return (int)$dbh->query("SELECT SCOPE_IDENTITY()")->fetchColumn();
    }

    /** 問題を更新（更新日を自動更新） */
    public function update(int $q_number, array $q): bool
    {
        $dbh = DAO::get_db_connect();
        $sql = "UPDATE question_data
                SET q_content=?, answer_content=?, wrong_answer1=?, wrong_answer2=?, wrong_answer3=?,
                    q_source=?, answers=?, correct_answers=?, image_path=?, update_ad=GETDATE()
                WHERE q_number=?";
        $stmt = $dbh->prepare($sql);
        return $stmt->execute([
            $q['q_content'], $q['answer_content'], $q['wrong_answer1'], $q['wrong_answer2'],
            $q['wrong_answer3'], $q['q_source'], $q['answers'], $q['correct_answers'],
            $q['image_path'] ?? null,
            $q_number
        ]);
    }

    /** 問題を削除 */
    public function delete(int $q_number): bool
    {
        $dbh = DAO::get_db_connect();
        $sql = "DELETE FROM question_data WHERE q_number = ?";
        $stmt = $dbh->prepare($sql);
        return $stmt->execute([$q_number]);
    }

    /**
     * 指定範囲の問題を取得（無限スクロール用）
     */
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

    /** 登録されている問題数を返す */
    public function countAll(?string $field = null): int
    {
        $dbh = DAO::get_db_connect();

        if ($field !== null) {
            $sql = "SELECT COUNT(*) FROM question_data 
                    WHERE q_number IN (
                        SELECT q_number FROM question_fields WHERE field_number = ?
                    )";
            $stmt = $dbh->prepare($sql);
            $stmt->execute([$field]);
        } else {
            $stmt = $dbh->query("SELECT COUNT(*) FROM question_data");
        }

        return (int)$stmt->fetchColumn();
    }

    /** IDで1件取得 */
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

    //分野名取得
    public function getProblemName(): array
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT area_number FROM q_categories";
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    

}
?>
