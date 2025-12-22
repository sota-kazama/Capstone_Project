<?php
require_once 'DAO.php';

class Goals
{
    public int $goal_id;         // 目標ID
    public int $user_id;         // ユーザーID
    public ?string $goal;        // 目標
    public ?string $mile_stone;  // 中間目標(マイルストーン)
    public ?string $goal_date;   // 目標日
    public ?string $result;      // 成果
    public string $created_at;   // 作成日時
    public string $updated_at;   // 更新日時
}

class GoalsDAO
{
    //u_goals(目標管理表)ユーザー内全件取得
    public function getGoalByUserId(int $user_id): ?Goals
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM u_goals WHERE user_id = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Goals');
        $goal_data = $stmt->fetch();

        return $goal_data ? $goal_data : null;
    }
}
?>