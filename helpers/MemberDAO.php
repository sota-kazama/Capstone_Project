<?php
require_once 'DAO.php';

class Member
{
    public int $user_id;             // ユーザーID
    public string $user_name;        // ユーザーネーム
    public string $mail_address;     // メールアドレス
    public string $pass_word;        // パスワード
    public int $u_correct_count;     // ユーザー正答数カウント
    public int $u_answers_count;     // 回答数カウント
    public ?string $created_ad;      // 作成日時
    public ?string $update_at;       // 更新日時
    public ?string $access_date;     // 最終アクセス日
    public ?string $u_admin;         // 管理者フラグ
    public ?string $member_type;     // 会員種別 0:有効 1:無効
    public ?string $question_hold;   // 保有問題
}

class MemberDAO
{
    /**
     * ログイン認証
     */
    public function get_member(string $mail_address, string $pass_word)
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM master_user WHERE mail_address = :mail_address";
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
        $sql = "INSERT INTO master_user (mail_address, user_name, pass_word)
                VALUES (:mail_address, :user_name, :pass_word)";
        $stmt = $dbh->prepare($sql);

        $password = password_hash($member->pass_word, PASSWORD_DEFAULT);

        $stmt->bindValue(':mail_address', $member->mail_address, PDO::PARAM_STR);
        $stmt->bindValue(':user_name', $member->user_name, PDO::PARAM_STR);
        $stmt->bindValue(':pass_word', $password, PDO::PARAM_STR);

        $stmt->execute();
    }

    /**
     * メールアドレス存在チェック
     */
    public function email_exists(string $mail_address): bool
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM master_user WHERE mail_address = :mail_address";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':mail_address', $mail_address, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch() !== false;
    }

    // ===================== 管理者用 =====================

    /**
     * ページング付きメンバー取得
     */
    public function getAllMembersPaged(int $page = 1, int $perPage = 50): array
    {
        $dbh = DAO::get_db_connect();

        $countSql = "SELECT COUNT(*) AS total_count FROM master_user";
        $countStmt = $dbh->query($countSql);
        $totalCount = (int)$countStmt->fetchColumn();
        $totalPages = (int)ceil($totalCount / $perPage);
        $offset = ($page - 1) * $perPage;

        $sql = "
            SELECT user_id, user_name, mail_address, u_admin, member_type
            FROM master_user
            ORDER BY user_id ASC
            OFFSET :offset ROWS FETCH NEXT :limit ROWS ONLY
        ";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->execute();

        $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'members' => $members,
            'total_pages' => $totalPages,
            'total_count' => $totalCount,
        ];
    }

    /**
     * 管理者：メンバー情報更新（パスワード任意）
     */
    public function updateMemberAccount(int $user_id, string $user_name, string $mail_address, ?string $password_hashed, int $u_admin): bool
    {
        $dbh = DAO::get_db_connect();

        $sql = "
            UPDATE master_user
            SET user_name = :user_name,
                mail_address = :mail_address,
                u_admin = :u_admin,
                update_at = GETDATE()
        ";

        if ($password_hashed !== null) {
            $sql .= ", pass_word = :pass_word";
        }

        $sql .= " WHERE user_id = :user_id";

        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':user_name', $user_name, PDO::PARAM_STR);
        $stmt->bindValue(':mail_address', $mail_address, PDO::PARAM_STR);
        $stmt->bindValue(':u_admin', $u_admin, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

        if ($password_hashed !== null) {
            $stmt->bindValue(':pass_word', $password_hashed, PDO::PARAM_STR);
        }

        return $stmt->execute();
    }

    /**
     * 管理者：会員種別更新
     */
    public function setMemberType(int $user_id, int $member_type): bool
    {
        $dbh = DAO::get_db_connect();
        $sql = "
            UPDATE master_user
            SET member_type = :member_type,
                update_at = GETDATE()
            WHERE user_id = :user_id
        ";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':member_type', $member_type, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // ===================== ユーザー本人用 =====================

    /**
     * user_id からメンバー情報取得
     */
    public function getMemberById(int $user_id)
    {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM master_user WHERE user_id = :user_id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * ログイン中ユーザー自身のプロフィール更新
     */
    public function updateMemberSelf(int $user_id, string $user_name, string $mail_address, ?string $password_hashed = null): bool
    {
        $dbh = DAO::get_db_connect();

        $sql = "
            UPDATE master_user
            SET user_name = :user_name,
                mail_address = :mail_address,
                update_at = GETDATE()
        ";

        if ($password_hashed !== null) {
            $sql .= ", pass_word = :pass_word";
        }

        $sql .= " WHERE user_id = :user_id";

        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':user_name', $user_name, PDO::PARAM_STR);
        $stmt->bindValue(':mail_address', $mail_address, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

        if ($password_hashed !== null) {
            $stmt->bindValue(':pass_word', $password_hashed, PDO::PARAM_STR);
        }

        return $stmt->execute();
    }
}
