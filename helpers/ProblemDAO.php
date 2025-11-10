<?php
require_once 'DAO.php';

class Category
{
    public string $area_number;    // 分野番号（VARCHAR）
    public string $area_name;      // 分野名
    public string $s_number;       // 資格番号（外部キー）
    public ?string $s_name;        // 資格名
    public ?string $created_ad;    // 作成日時
    public ?string $update_at;     // 更新日時
}

class ProblemDAO
{
    private PDO $pdo;

    public function __construct() {
        try {
            // SQL Server に接続する DSN
            $serverName = 'localhost'; // 必要に応じて SSMS のサーバー名に変更
            $dbName = 'your_db';       // データベース名
            $username = 'your_username'; // SQL Server のユーザー名
            $password = 'your_password'; // SQL Server のパスワード

            $dsn = "sqlsrv:Server=$serverName;Database=$dbName";
            $this->pdo = new PDO($dsn, $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('DB接続エラー: ' . $e->getMessage());
        }
    }

    // ===================== 分野管理 =====================

    /**
     * 分野を登録
     */
    public function insertCategory(string $area_name, string $s_number, string $area_number): bool
    {
        $sql = "INSERT INTO q_categories (area_number, area_name, s_number, created_ad, update_at)
                VALUES (:area_number, :area_name, :s_number, GETDATE(), GETDATE())";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':area_number', $area_number, PDO::PARAM_STR);
        $stmt->bindValue(':area_name', $area_name, PDO::PARAM_STR);
        $stmt->bindValue(':s_number', $s_number, PDO::PARAM_STR);
        return $stmt->execute();
    }

    /**
     * 全分野を取得（資格名付き）
     */
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

    /**
     * 分野情報を更新
     */
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

    /**
     * 分野を削除
     */
    public function deleteCategory(string $area_number): bool
    {
        $sql = "DELETE FROM q_categories WHERE area_number = :area_number";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':area_number', $area_number, PDO::PARAM_STR);
        return $stmt->execute();
    }

    /**
     * 全問題取得
     */
    public function getAllQuestions(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM questions ORDER BY question_id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
