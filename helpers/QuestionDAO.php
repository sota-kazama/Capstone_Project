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
    public ?string $answers;
    public ?string $correct_answers;
    public ?string $image_path;   // 画像パス
    public ?string $created_ad;   // 登録日
    public ?string $update_at;    // 更新日
}

class QuestionDAO
{
    /** 全問題を取得 */
    public function getAll(): array
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM question_data ORDER BY q_number";
        $stmt = $dbh->query($sql);

        $data = [];
        while ($row = $stmt->fetchObject('Question')) {
            $data[] = $row;
        }
        return $data;
    }

    /** 新規問題を追加（登録日・更新日を現在日時で設定） */
    public function insert(array $q): bool
    {
        $dbh = DAO::get_db_connect();
        $sql = "INSERT INTO question_data 
                (q_content, answer_content, wrong_answer1, wrong_answer2, wrong_answer3, q_source, answers, correct_answers, image_path, created_ad, update_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, GETDATE(), GETDATE())";
        $stmt = $dbh->prepare($sql);
        return $stmt->execute([
            $q['q_content'], $q['answer_content'], $q['wrong_answer1'], $q['wrong_answer2'],
            $q['wrong_answer3'], $q['q_source'], $q['answers'], $q['correct_answers'],
            $q['image_path'] ?? null
        ]);
    }

    /** 問題を更新（更新日を自動更新） */
    public function update(int $q_number, array $q): bool
    {
        $dbh = DAO::get_db_connect();
        $sql = "UPDATE question_data
                SET q_content=?, answer_content=?, wrong_answer1=?, wrong_answer2=?, wrong_answer3=?,
                    q_source=?, answers=?, correct_answers=?, image_path=?, update_at=GETDATE()
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
     * @param int $offset 取得開始位置
     * @param int $limit 取得件数
     * @param string|null $field 分野番号（ソート・絞り込み用）
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

        $data = [];
        while ($row = $stmt->fetchObject('Question')) {
            $data[] = $row;
        }
        return $data;
    }

    /** 登録されている問題数を返す（ページング用） */
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
            $sql = "SELECT COUNT(*) FROM question_data";
            $stmt = $dbh->query($sql);
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
        $row = $stmt->fetchObject('Question');
        return $row ?: null;
    }
}
?>
