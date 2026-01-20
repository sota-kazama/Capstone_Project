<?php
require_once 'DAO.php';

class Category
{
    public string $area_number; // 分野番号
    public string $area_name;   // 分野名
    public string $s_number;    // 資格番号（外部キー）
    public ?string $s_name;     // 資格名
    public ?string $created_at; // 登録日
    public ?string $update_at;  // 更新日
}

class CategoryDAO
{
    // 全分野を取得（資格名付き）
    public function getAll(): array
    {
        $dbh = DAO::get_db_connect();
        $sql = "
            SELECT 
                c.area_number,
                c.area_name,
                c.s_number,
                s.s_name,
                c.created_ad AS created_at,
                c.update_at AS update_at
            FROM q_categories c
            LEFT JOIN shikaku s ON c.s_number = s.s_number
            ORDER BY c.area_number
        ";
        $stmt = $dbh->query($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Category');
        return $stmt->fetchAll();
    }

    // 分野を1件取得
    public function getByNumber(string $area_number): ?Category
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM q_categories WHERE area_number = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$area_number]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Category');
        return $stmt->fetch() ?: null;
    }

    // 分野を追加
    public function insert(string $area_number, string $area_name, string $s_number): bool
    {
        $dbh = DAO::get_db_connect();
        $sql = "
            INSERT INTO q_categories (area_number, area_name, s_number, created_ad, update_ad)
            VALUES (?, ?, ?, GETDATE(), GETDATE())
        ";
        $stmt = $dbh->prepare($sql);
        return $stmt->execute([$area_number, $area_name, $s_number]);
    }

    // 分野を更新
    public function update(string $area_number, string $area_name, string $s_number): bool
    {
        $dbh = DAO::get_db_connect();
        $sql = "
            UPDATE q_categories
            SET area_name = ?, s_number = ?, update_ad = GETDATE()
            WHERE area_number = ?
        ";
        $stmt = $dbh->prepare($sql);
        return $stmt->execute([$area_name, $s_number, $area_number]);
    }

    // 分野を削除
    public function delete(string $area_number): bool
    {
        $dbh = DAO::get_db_connect();
        $sql = "DELETE FROM q_categories WHERE area_number = ?";
        $stmt = $dbh->prepare($sql);
        return $stmt->execute([$area_number]);
    }

    // 問題に関連するカテゴリーを削除
    public function deleteCategoriesByQuestion(int $q_number): bool
    {
        $dbh = DAO::get_db_connect();
        $sql = "DELETE FROM q_middle WHERE q_number = ?";
        $stmt = $dbh->prepare($sql);
        return $stmt->execute([$q_number]);
    }

    // 問題とカテゴリーを関連付ける
    public function insertCategoryAssociation(int $q_number, string $area_number): bool
    {
        $dbh = DAO::get_db_connect();
        $sql = "
            INSERT INTO q_middle (q_number, area_number, created_ad, update_ad)
            VALUES (?, ?, GETDATE(), GETDATE())
        ";
        $stmt = $dbh->prepare($sql);
        return $stmt->execute([$q_number, $area_number]);
    }

    // 指定問題に紐付いている分野番号を取得
    public function getCategoriesByQuestion(int $q_number): array
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT area_number FROM q_middle WHERE q_number = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$q_number]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    // 分野を検索（分野名・資格名）
public function search(string $keyword): array
{
    $dbh = DAO::get_db_connect();

    $sql = "
        SELECT 
            c.area_number,
            c.area_name,
            c.s_number,
            s.s_name,
            c.created_ad AS created_at,
            c.update_at AS update_at
        FROM q_categories c
        LEFT JOIN shikaku s ON c.s_number = s.s_number
        WHERE c.area_name LIKE ?
           OR s.s_name LIKE ?
        ORDER BY c.area_number
    ";

    $stmt = $dbh->prepare($sql);
    $kw = '%' . $keyword . '%';
    $stmt->bindValue(1, $kw, PDO::PARAM_STR);
    $stmt->bindValue(2, $kw, PDO::PARAM_STR);
    $stmt->execute();

    $stmt->setFetchMode(PDO::FETCH_CLASS, 'Category');
    return $stmt->fetchAll();
}

}