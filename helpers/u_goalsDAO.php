<?php
require_once 'DAO.php';

class Goals
{
    public int $goal_id;
    public int $user_id;
    public ?string $goal;
    public ?string $mile_stone;
    public ?string $mile_stone2;
    public ?string $mile_stone3;
    public ?string $mile_stone4;
    public ?string $mile_stone5;
    public ?string $goal_date;
    public ?string $result;
    public ?string $created_at;
    public ?string $updated_at;
}

class GoalsDAO
{
    // ユーザーの目標一覧を取得（複数件）
    public function getGoalsByUserId(int $user_id): array
    {
        $dbh = DAO::get_db_connect();
        $sql = "
            SELECT
                goal_id,
                user_id,
                goal,
                mile_stone,
                mile_stone2,
                mile_stone3,
                mile_stone4,
                mile_stone5,
                goal_date,
                result,
                created_ad AS created_at,
                update_at AS updated_at
            FROM u_goals
            WHERE user_id = ?
            ORDER BY created_ad desc
        ";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$user_id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Goals');
        return $stmt->fetchAll();
    }

    // 目標IDで単一目標取得
    public function getGoalByGoalId(int $goal_id): ?Goals
    {
        $dbh = DAO::get_db_connect();
        $sql = "
            SELECT
                goal_id,
                user_id,
                goal,
                mile_stone,
                mile_stone2,
                mile_stone3,
                mile_stone4,
                mile_stone5,
                goal_date,
                result,
                created_ad AS created_at,
                update_at AS updated_at
            FROM u_goals
            WHERE goal_id = ?
        ";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$goal_id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Goals');
        $goal = $stmt->fetch();

        return $goal ?: null;
    }

    public function getLatestGoalByUserId(int $user_id): ?Goals
    {
        $dbh = DAO::get_db_connect();
        $sql = "
            SELECT
                goal_id,
                user_id,
                goal,
                mile_stone,
                mile_stone2,
                mile_stone3,
                mile_stone4,
                mile_stone5,
                goal_date,
                result,
                created_ad AS created_at,
                update_at AS updated_at
            FROM u_goals
            WHERE user_id = ?
            ORDER BY created_ad DESC
        ";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$user_id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Goals');
        $goal = $stmt->fetch();

        return $goal ?: null;
    }

    public function insert(
        int $user_id,
        ?string $goal,
        ?string $mile_stone,
        ?string $mile_stone2,
        ?string $mile_stone3,
        ?string $mile_stone4,
        ?string $mile_stone5,
        ?string $goal_date
    ): bool {
        $dbh = DAO::get_db_connect();
        $sql = "
            INSERT INTO u_goals (
                user_id,
                goal,
                mile_stone,
                mile_stone2,
                mile_stone3,
                mile_stone4,
                mile_stone5,
                goal_date,
                created_ad, -- ここがカラム名
                update_at   -- ここがカラム名
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, GETDATE(), GETDATE())
        ";
        $stmt = $dbh->prepare($sql);

        // プレースホルダ (?) は合計 8個 なので、配列の中身も 8個 に合わせます
        return $stmt->execute([
            $user_id,
            $goal,
            $mile_stone,
            $mile_stone2,
            $mile_stone3,
            $mile_stone4,
            $mile_stone5,
            $goal_date
        ]);
    }

    // 更新
    public function update(
        int $goal_id,
        ?string $goal,
        ?string $mile_stone,
        ?string $mile_stone2,
        ?string $mile_stone3,
        ?string $mile_stone4,
        ?string $mile_stone5,
        ?string $goal_date,
        ?string $result
    ): bool {
        $dbh = DAO::get_db_connect();
        $sql = "
            UPDATE u_goals
            SET
                goal = ?,
                mile_stone = ?,
                mile_stone2 = ?,
                mile_stone3 = ?,
                mile_stone4 = ?,
                mile_stone5 = ?,
                goal_date = ?,
                result = ?,
                update_at = GETDATE()
            WHERE goal_id = ?
        ";
        $stmt = $dbh->prepare($sql);
        return $stmt->execute([$goal, $mile_stone, $mile_stone2, $mile_stone3, $mile_stone4, $mile_stone5, $goal_date, $result, $goal_id]);
    }

    // 削除
    public function delete(int $goal_id): bool
    {
        $dbh = DAO::get_db_connect();
        $sql = "DELETE FROM u_goals WHERE goal_id = ?";
        $stmt = $dbh->prepare($sql);
        return $stmt->execute([$goal_id]);
    }
}
?>
