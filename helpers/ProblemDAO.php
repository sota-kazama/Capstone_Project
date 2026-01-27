<?php
require_once 'DAO.php';

class Category
{
    public string $area_number;   // 分野番号
    public string $area_name;     // 分野名
    public string $s_number;      // 資格番号
    public ?string $s_name;       // 資格名
    public ?string $created_ad;   // 作成日時
    public ?string $update_at;    // 更新日時
}

class ProblemDAO
{
    // ===================== DB接続 =====================
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = DAO::get_db_connect();
    }

    // ===================== 分野管理 =====================

    // 分野を登録
    public function insertCategory(string $area_name, string $s_number, string $area_number): bool
    {
        $sql = "INSERT INTO q_categories 
                (area_number, area_name, s_number, created_ad, update_at)
                VALUES (:area_number, :area_name, :s_number, GETDATE(), GETDATE())";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':area_number', $area_number, PDO::PARAM_STR);
        $stmt->bindValue(':area_name', $area_name, PDO::PARAM_STR);
        $stmt->bindValue(':s_number', $s_number, PDO::PARAM_STR);

        return $stmt->execute();
    }

    // 全分野取得（資格名付き）
    public function getAllCategories(): array
    {
        $sql = "SELECT c.area_number, c.area_name, c.s_number, s.s_name, c.created_ad, c.update_at
                FROM q_categories c
                LEFT JOIN Shikaku s ON c.s_number = s.s_number
                ORDER BY c.area_number";

        $stmt = $this->pdo->query($sql);
        $categories = [];
        while ($row = $stmt->fetchObject('Category')) {
            $categories[] = $row;
        }
        return $categories;
    }

    // 分野情報更新
    public function updateCategory(string $area_number, string $area_name, string $s_number): bool
    {
        $sql = "UPDATE q_categories
                SET area_name = :area_name,
                    s_number = :s_number,
                    update_at = GETDATE()
                WHERE area_number = :area_number";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':area_name', $area_name, PDO::PARAM_STR);
        $stmt->bindValue(':s_number', $s_number, PDO::PARAM_STR);
        $stmt->bindValue(':area_number', $area_number, PDO::PARAM_STR);

        return $stmt->execute();
    }

    // 分野削除
    public function deleteCategory(string $area_number): bool
    {
        $sql = "DELETE FROM q_categories WHERE area_number = :area_number";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':area_number', $area_number, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // 分野番号・分野名取得（セレクトボックス用）
    public function getProblemName(): array
    {
        $sql = "SELECT area_number, area_name FROM q_categories ORDER BY area_number";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ===================== 問題取得 =====================

    // 全問題取得
    public function getAllQuestions(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM questions ORDER BY question_id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 指定分野の問題IDを _ 区切り文字列で取得
    public function getProblemIdString(string $area_number): string
    {
        $sql = "SELECT q_number FROM q_middle WHERE area_number = :area_number";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':area_number', $area_number, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return implode('_', $result);
    }

    // 指定分野の問題ID一覧取得
    public function getProblemIdsByArea(string $area_number): array
    {
        $sql = "SELECT q_number FROM q_middle WHERE area_number = :area_number ORDER BY q_number";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':area_number', $area_number, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // 指定分野の問題情報をすべて取得
    public function getQuestionsByArea(string $area_number): array
    {
<<<<<<< HEAD
        $dbh = DAO::get_db_connect();
        $sql = "
            SELECT q.*
            FROM question_data q
            INNER JOIN q_middle m
                ON q.q_number = m.q_number
            WHERE m.area_number = :area_number
            ORDER BY q.q_number
        ";

        $stmt = $dbh->prepare($sql);
=======
        $sql = "SELECT q.* 
                FROM question_data q
                INNER JOIN q_middle m ON q.q_number = m.q_number
                WHERE m.area_number = :area_number
                ORDER BY q.q_number";

        $stmt = $this->pdo->prepare($sql);
>>>>>>> 9bc8a4614ed9bf0d2c2328486b194e0ed79b8a27
        $stmt->bindValue(':area_number', $area_number, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // ===================== 文字列操作 =====================

    // 文字列αから先頭を削除
    public function removeHeadFromAlpha(string $alpha): string
    {
        if ($alpha === '') return '';
        $arr = explode('_', $alpha);
        array_shift($arr);
        return implode('_', $arr);
    }

    // 文字列βに値を追加
    public function addToBeta(string $beta, string $num): string
    {
        if ($beta === '') return $num;
        return $beta . '_' . $num;
    }
}
?>
