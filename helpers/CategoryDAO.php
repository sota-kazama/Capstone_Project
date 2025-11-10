<?php
require_once 'DAO.php';

class Category
{
    public string $area_number;   // 分野番号
    public string $area_name;     // 分野名
    public string $s_number;      // 資格番号（外部キー）
    public ?string $s_name;       // 資格名
    public ?string $created_at;   // 登録日
    public ?string $update_at;    // 更新日時
}

class CategoryDAO
{
    /**
     * 全分野を取得（資格名付き）
     */
    public function getAll(): array
    {
        $dbh = DAO::get_db_connect();
        $sql = "
            SELECT c.area_number, c.area_name, c.s_number, s.s_name, 
                   c.created_ad AS created_at, c.update_at
            FROM q_categories c
            LEFT JOIN shikaku s ON c.s_number = s.s_number
            ORDER BY c.area_number
        ";
        $stmt = $dbh->query($sql);

        $data = [];
        while ($row = $stmt->fetchObject('Category')) {
            $data[] = $row;
        }
        return $data;
    }

    /**
     * 分野を1件取得
     */
    public function getByNumber(string $area_number): ?Category
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM q_categories WHERE area_number = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$area_number]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Category');
        return $stmt->fetch() ?: null;
    }

    /**
     * 分野を追加
     */
    public function insert(string $area_number, string $area_name, string $s_number): bool
    {
        $dbh = DAO::get_db_connect();
        $sql = "
            INSERT INTO q_categories (area_number, area_name, s_number, created_ad, update_at)
            VALUES (?, ?, ?, GETDATE(), GETDATE())
        ";
        $stmt = $dbh->prepare($sql);
        return $stmt->execute([$area_number, $area_name, $s_number]);
    }

    /**
     * 分野を更新
     */
    public function update(string $area_number, string $area_name, string $s_number): bool
    {
        $dbh = DAO::get_db_connect();
        $sql = "
            UPDATE q_categories
            SET area_name = ?, s_number = ?, update_at = GETDATE()
            WHERE area_number = ?
        ";
        $stmt = $dbh->prepare($sql);
        return $stmt->execute([$area_name, $s_number, $area_number]);
    }

    /**
     * 分野を削除
     */
    public function delete(string $area_number): bool
    {
        $dbh = DAO::get_db_connect();
        $sql = "DELETE FROM q_categories WHERE area_number = ?";
        $stmt = $dbh->prepare($sql);
        return $stmt->execute([$area_number]);
    }
}
?>
