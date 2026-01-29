<?php
require_once 'DAO.php';

/**
 * 分野エンティティ
 */
class Category
{
    public string $area_number;   // 分野番号
    public string $area_name;     // 分野名
    public string $s_number;      // 資格番号
    public ?string $s_name;       // 資格名
    public ?string $created_ad;   // 作成日時
    public ?string $update_at;    // 更新日時
}

/**
 * 分野・問題管理DAO
 */
class ProblemDAO
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = DAO::get_db_connect();
    }

    // ===================== 分野管理 =====================

    /** 分野登録 */
    public function insertCategory(
        string $area_name,
        string $s_number,
        string $area_number
    ): bool {
        $sql = "
            INSERT INTO q_categories
            (area_number, area_name, s_number, created_ad, update_at)
            VALUES (:area_number, :area_name, :s_number, GETDATE(), GETDATE())
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':area_number', $area_number, PDO::PARAM_STR);
        $stmt->bindValue(':area_name', $area_name, PDO::PARAM_STR);
        $stmt->bindValue(':s_number', $s_number, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /** 全分野取得（資格名付き） */
    public function getAllCategories(): array
    {
        $sql = "
            SELECT
                c.area_number,
                c.area_name,
                c.s_number,
                s.s_name,
                c.created_ad,
                c.update_at
            FROM q_categories c
            LEFT JOIN Shikaku s ON c.s_number = s.s_number
            ORDER BY c.area_number
        ";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Category');
    }

    /** 分野更新 */
    public function updateCategory(
        string $area_number,
        string $area_name,
        string $s_number
    ): bool {
        $sql = "
            UPDATE q_categories
            SET
                area_name = :area_name,
                s_number = :s_number,
                update_at = GETDATE()
            WHERE area_number = :area_number
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':area_name', $area_name, PDO::PARAM_STR);
        $stmt->bindValue(':s_number', $s_number, PDO::PARAM_STR);
        $stmt->bindValue(':area_number', $area_number, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /** 分野削除 */
    public function deleteCategory(string $area_number): bool
    {
        $sql = "DELETE FROM q_categories WHERE area_number = :area_number";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':area_number', $area_number, PDO::PARAM_STR);
        return $stmt->execute();
    }

    /** セレクトボックス用 */
    public function getProblemName(): array
    {
        $sql = "SELECT area_number, area_name FROM q_categories ORDER BY area_number";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // ===================== 問題取得 =====================

    /** 全問題取得（DB新構成対応） */
    public function getAllQuestions(): array
    {
        $sql = "
            SELECT
                q_number,
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
            FROM question_data
            ORDER BY q_number
        ";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /** 指定分野の問題IDを _ 区切り文字列で取得 */
    public function getProblemIdString(string $area_number): string
    {
        $sql = "SELECT q_number FROM q_middle WHERE area_number = :area_number";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':area_number', $area_number, PDO::PARAM_STR);
        $stmt->execute();

        return implode('_', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    /** 指定分野の問題ID配列 */
    public function getProblemIdsByArea(string $area_number): array
    {
        $sql = "
            SELECT q_number
            FROM q_middle
            WHERE area_number = :area_number
            ORDER BY q_number
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':area_number', $area_number, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /** 指定分野の問題情報取得（新question_data構造対応） */
    public function getQuestionsByArea(string $area_number): array
    {
        $sql = "
            SELECT
                q.q_number,
                q.q_content,
                q.q_source,
                q.answers,
                q.correct_answers,
                q.image_path,
                q.choices1,
                q.choices2,
                q.choices3,
                q.choices4,
                q.created_ad,
                q.update_ad
            FROM question_data q
            INNER JOIN q_middle m
                ON q.q_number = m.q_number
            WHERE m.area_number = :area_number
            ORDER BY q.q_number
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':area_number', $area_number, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // ===================== 文字列操作 =====================

    /** α文字列の先頭削除 */
    public function removeHeadFromAlpha(string $alpha): string
    {
        if ($alpha === '') return '';
        $arr = explode('_', $alpha);
        array_shift($arr);
        return implode('_', $arr);
    }

    /** β文字列に値追加 */
    public function addToBeta(string $beta, string $num): string
    {
        return $beta === '' ? $num : $beta . '_' . $num;
    }
}
?>
