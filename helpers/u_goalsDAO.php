<?php
require_once 'DAO.php';

class Goals
{
    public int $goal_id;          // 目標ID
    public int $user_id;          // ユーザーID
    public ?string $goal;         // 目標
    public ?string $mile_stone;   // 中間目標(マイルストーン)
    public ?string $goal_date;    // 目標日
    public ?string $result;       // 成果
    public ?string $created_at;   // 作成日時
    public ?string $updated_at;   // 更新日時
}

class GoalsDAO
{
    /**
     * ユーザーの目標を1件取得
     */
    public function getGoalByUserId(int $user_id): ?Goals
    {
        $dbh = DAO::get_db_connect();
        $sql = "
            SELECT
                goal_id,
                user_id,
                goal,
                mile_stone,
                goal_date,
                result,
                created_ad AS created_at,
                update_at AS updated_at
            FROM u_goals
            WHERE user_id = ?
        ";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$user_id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Goals');
        $goal_data = $stmt->fetch();

        return $goal_data ?: null;
    }

    /**
     * 目標を新規登録
     */
    public function insert(
        int $user_id,
        ?string $goal,
        ?string $mile_stone,
        ?string $goal_date
    ): bool {
        $dbh = DAO::get_db_connect();
        $sql = "
            INSERT INTO u_goals (
                user_id,
                goal,
                mile_stone,
                goal_date,
                created_ad,
                update_at
            )
            VALUES (?, ?, ?, ?, GETDATE(), GETDATE())
        ";
        $stmt = $dbh->prepare($sql);
        return $stmt->execute([$user_id, $goal, $mile_stone, $goal_date]);
    }

    /**
     * 目標を更新
     */
    public function update(
        int $goal_id,
        ?string $goal,
        ?string $mile_stone,
        ?string $goal_date,
        ?string $result
    ): bool {
        $dbh = DAO::get_db_connect();
        $sql = "
            UPDATE u_goals
            SET
                goal = ?,
                mile_stone = ?,
                goal_date = ?,
                result = ?,
                update_at = GETDATE()
            WHERE goal_id = ?
        ";
        $stmt = $dbh->prepare($sql);
        return $stmt->execute([$goal, $mile_stone, $goal_date, $result, $goal_id]);
    }

    /**
     * 目標を削除
     */
    public function delete(int $goal_id): bool
    {
        $dbh = DAO::get_db_connect();
        $sql = "DELETE FROM u_goals WHERE goal_id = ?";
        $stmt = $dbh->prepare($sql);
        return $stmt->execute([$goal_id]);
    }
}
?>
