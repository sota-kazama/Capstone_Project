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

    // 型の前に ? を付けて、NULLを許可するように変更します
    public ?int $ms1_status = 0;
    public ?int $ms2_status = 0;
    public ?int $ms3_status = 0;
    public ?int $ms4_status = 0;
    public ?int $ms5_status = 0;
    
    public ?int $is_achieved = 0;
    public ?string $good_points = null;
    public ?string $bad_points = null;
    public ?string $memo = null;
    
    public ?string $goal_date;
    public ?string $result;
    public ?string $created_at;
    public ?string $updated_at;
}

class GoalsDAO
{
    // SELECT文の共通カラム定義
    private const SELECT_COLUMNS = "
        goal_id, user_id, goal, 
        mile_stone, mile_stone2, mile_stone3, mile_stone4, mile_stone5,
        ms1_status, ms2_status, ms3_status, ms4_status, ms5_status,
        is_achieved, good_points, bad_points, memo,
        goal_date, result, 
        created_ad AS created_at, update_at AS updated_at
    ";

    // ユーザーの目標一覧を取得（複数件）
    public function getGoalsByUserId(int $user_id): array
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT " . self::SELECT_COLUMNS . "
                FROM u_goals
                WHERE user_id = ?
                ORDER BY created_ad DESC";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$user_id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Goals');
        return $stmt->fetchAll();
    }

    // 目標IDで単一目標取得
    public function getGoalByGoalId(int $goal_id): ?Goals
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT " . self::SELECT_COLUMNS . "
                FROM u_goals
                WHERE goal_id = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$goal_id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Goals');
        $goal = $stmt->fetch();

        return $goal ?: null;
    }

    // 最新の目標1件を取得
    public function getLatestGoalByUserId(int $user_id): ?Goals
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT " . self::SELECT_COLUMNS . "
                FROM u_goals
                WHERE user_id = ?
                ORDER BY created_ad DESC";
        
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$user_id]);
        if (!$stmt) return null;

        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Goals');
        $goal = $stmt->fetch();

        return $goal ?: null;
    }

    // 新規挿入
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
        $sql = "INSERT INTO u_goals (
                    user_id, goal, 
                    mile_stone, mile_stone2, mile_stone3, mile_stone4, mile_stone5,
                    ms1_status, ms2_status, ms3_status, ms4_status, ms5_status,
                    is_achieved, goal_date, created_ad, update_at
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, 0, 0, 0, 0, 0, 0, ?, GETDATE(), GETDATE())";
        
        $stmt = $dbh->prepare($sql);
        return $stmt->execute([
            $user_id, $goal, 
            $mile_stone, $mile_stone2, $mile_stone3, $mile_stone4, $mile_stone5, 
            $goal_date
        ]);
    }

    // 成果入力画面（results_post.php）からの更新
    public function updateGoalResult(
        int $goal_id, 
        array $ms_status, 
        int $is_achieved, 
        ?string $good, 
        ?string $bad, 
        ?string $memo
    ): bool {
        $dbh = DAO::get_db_connect();
        $sql = "UPDATE u_goals SET 
                    ms1_status = ?, 
                    ms2_status = ?, 
                    ms3_status = ?, 
                    ms4_status = ?, 
                    ms5_status = ?,
                    is_achieved = ?,
                    good_points = ?,
                    bad_points = ?,
                    memo = ?,
                    update_at = GETDATE()
                WHERE goal_id = ?";
        
        $stmt = $dbh->prepare($sql);
        return $stmt->execute([
            $ms_status[1] ?? 0, 
            $ms_status[2] ?? 0, 
            $ms_status[3] ?? 0, 
            $ms_status[4] ?? 0, 
            $ms_status[5] ?? 0,
            $is_achieved,
            $good,
            $bad,
            $memo,
            $goal_id
        ]);
    }

    // 基本情報の更新（goal.php 等での修正用）
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
        $sql = "UPDATE u_goals SET
                    goal = ?,
                    mile_stone = ?,
                    mile_stone2 = ?,
                    mile_stone3 = ?,
                    mile_stone4 = ?,
                    mile_stone5 = ?,
                    goal_date = ?,
                    result = ?,
                    update_at = GETDATE()
                WHERE goal_id = ?";
        $stmt = $dbh->prepare($sql);
        return $stmt->execute([
            $goal, $mile_stone, $mile_stone2, $mile_stone3, 
            $mile_stone4, $mile_stone5, $goal_date, $result, $goal_id
        ]);
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