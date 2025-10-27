<?php
require_once 'DAO.php';

class Member
{
    public int $user_id;                 // ユーザーID
    public string $user_name;            // ユーザーネーム
    public string $mail_address;         // メールアドレス
    public string $pass_word;            // パスワード
    public int $u_correct_count;         // ユーザー正答数カウント
    public int $u_answers_count;         // 回答数カウント
    public ?string $created_ad;          // 作成日時
    public ?string $update_at;           // 更新日時
    public ?string $access_date;         // 最終アクセス日
    public ?string $u_admin;             // 管理者フラグ
    public ?string $member_type;         // 会員種別
    public ?string $question_hold;       // 保有問題
}

class MemberDAO
{
    /**
     * メンバーを取得（ログイン認証）
     */
    public function get_member(string $mail_address, string $pass_word)
    {
        $dbh = DAO::get_db_connect();

        $sql = "
            SELECT *
            FROM master_user
            WHERE mail_address = :mail_address
        ";

        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':mail_address', $mail_address, PDO::PARAM_STR);
        $stmt->execute();

        $member = $stmt->fetchObject('Member');

        if ($member !== false && password_verify($pass_word, $member->pass_word)) {
            return $member;
        }

        return false;
    }

    /**
     * 新規メンバー登録
     */
    public function insert(Member $member): void
    {
        $dbh = DAO::get_db_connect();

        $sql = "
            INSERT INTO master_user (mail_address, user_name, pass_word)
            VALUES (:mail_address, :user_name, :pass_word)
        ";

        $stmt = $dbh->prepare($sql);

        $password = password_hash($member->pass_word, PASSWORD_DEFAULT);

        $stmt->bindValue(':mail_address', $member->mail_address, PDO::PARAM_STR);
        $stmt->bindValue(':user_name', $member->user_name, PDO::PARAM_STR);
        $stmt->bindValue(':pass_word', $password, PDO::PARAM_STR);

        $stmt->execute();
    }

    /**
     * メールアドレスの重複チェック
     */
    public function email_exists(string $mail_address): bool
    {
        $dbh = DAO::get_db_connect();

        $sql = "
            SELECT *
            FROM master_user
            WHERE mail_address = :mail_address
        ";

        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':mail_address', $mail_address, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch() !== false;
    }
}
