<?php
// DB接続設定の読み込み
require_once 'config.php';
require_once 'DAO.php';


class Category
{
    public string $area_number;   // 分野番号（VARCHAR）
    public string $area_name;     // 分野名
    public string $s_number;      // 資格番号（外部キー）
    public ?string $s_name;       // 資格名
    public ?string $created_ad;   // 作成日時
    public ?string $update_at;    // 更新日時
}

class ProblemDAO
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = DAO::get_db_connect(); // PDO接続取得
    }

    // ===================== 分野管理 =====================

    // 分野を登録
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

    // 全分野を取得（資格名付き）
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

    // 分野情報を更新
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

    // 分野を削除
    public function deleteCategory(string $area_number): bool
    {
        $sql = "DELETE FROM q_categories WHERE area_number = :area_number";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':area_number', $area_number, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // 全問題取得
    public function getAllQuestions(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM questions ORDER BY question_id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 指定分野の問題IDを _ 区切り文字列で取得
     */
    public function getProblemIdString(string $area_number): string
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT q_number FROM q_middle WHERE area_number = :area_number";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':area_number', $area_number, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return implode('_', $result);
    }

    //分野名取得
    public function getProblemName(): array
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT area_number, area_name FROM q_categories";
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * 指定分野の問題ID一覧を取得
     */
    public function getProblemIdsByArea(string $area_number): array
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT q_number
                FROM q_middle
                WHERE area_number = :area_number
                ORDER BY q_number";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':area_number', $area_number, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * 指定分野の問題情報をすべて取得
     */
    public function getQuestionsByArea(string $area_number): array
    {
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
        $stmt->bindValue(':area_number', $area_number, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    //文字列αから削除
    function removeHeadFromAlpha(string $alpha): string
    {
        if ($alpha === '') {
            return '';
        }

        $arr = explode('_', $alpha);
        $head = array_shift($arr); // 先頭の数字

        $newAlpha = implode('_', $arr);

        return $newAlpha;
    }
    //文字列βに追加
    function addToBeta(string $beta, string $num): string
    {
        if ($beta === '') {
            return $num;
        }

        return $beta . '_' . $num;
    }

    // public function searchString(string $beta, string $find, string $string)
    // {

    //     // 1. _の位置を探す
    //     $pos = strpos($string, $find);

    //     if ($pos !== false) {
    //         // 2. 0文字目から、$pos分だけ（_の直前まで）切り出す
    //         $beta = substr($string, 0, $pos);
    //         echo $beta;
    //     } else {
    //         echo "文字が見つかりませんでした。";
    //     }
    //     // $i = 0;
    //     // $parts = explode($find, $string);
    //     // if($beta = '') {
    //     // // 1回目
    //     // $beta = $parts[$i];
    //     // echo $beta; // 1
    //     // $i++;
    //     // } else {
    //     // // 2回目
    //     // $beta .= $find . $parts[$i];
    //     // echo $beta; // 1_2
    //     // $i++;
    //     // }
    // }


    // public function deleteString(string $find, string $string)
    // {
    //     // "_" を含めて、それより後ろを取得
    //     $result = strstr($string, $find); 
    //     // 最初の文字"_"も削除したい場合は substr で1文字飛ばす
    //     $result = substr(strstr($string, $find), 1);

    //     echo $result; // 結果: 山田太郎.pdf

    // }


}

