<?php
require_once 'DAO.php';

class Field
{
    public string $area_number;
    public string $area_name;
    public int $s_number;
    public ?string $created_ad;
    public ?string $update_at;
    public ?string $s_name; // JOINで資格名も取得するため
}

class FieldDAO
{
    private PDO $dbh;

    public function __construct()
    {
        $this->dbh = DAO::get_db_connect();
    }

    /**
     * 分野一覧を取得
     */
    public function getAll(): array
    {
        $sql = "SELECT f.area_number, f.area_name, f.s_number,
                       s.s_name, f.created_ad, f.update_at
                FROM q_categories f
                JOIN shikaku s ON f.s_number = s.s_number
                ORDER BY f.area_number";
        $stmt = $this->dbh->query($sql);
        $list = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $field = new Field();
            $field->area_number = $row['area_number'];
            $field->area_name = $row['area_name'];
            $field->s_number = (int)$row['s_number'];
            $field->s_name = $row['s_name'] ?? '';
            $field->created_ad = $row['created_ad'] ?? '';
            $field->update_at = $row['update_at'] ?? '';
            $list[] = $field;
        }

        return $list;
    }

    /**
     * 分野を追加
     */
    public function insert(string $area_number, string $area_name, int $s_number): bool
    {
        $sql = "INSERT INTO q_categories (area_number, area_name, s_number, created_ad, update_at)
                VALUES (?, ?, ?, GETDATE(), GETDATE())";
        $stmt = $this->dbh->prepare($sql);
        return $stmt->execute([$area_number, $area_name, $s_number]);
    }

    /**
     * 分野を更新
     */
    public function update(string $area_number, string $area_name, int $s_number): bool
    {
        $sql = "UPDATE q_categories
                SET area_name = ?, s_number = ?, update_at = GETDATE()
                WHERE area_number = ?";
        $stmt = $this->dbh->prepare($sql);
        return $stmt->execute([$area_name, $s_number, $area_number]);
    }

    /**
     * 分野を削除
     */
    public function delete(string $area_number): bool
    {
        $sql = "DELETE FROM q_categories WHERE area_number = ?";
        $stmt = $this->dbh->prepare($sql);
        return $stmt->execute([$area_number]);
    }
}
?>
