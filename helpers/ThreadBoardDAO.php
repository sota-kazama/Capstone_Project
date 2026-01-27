<?php
require_once 'DAO.php';

/* =========================
   Entity
   ========================= */

class ThreadTitle
{
    public int $thread_number;
    public string $thread_name;
    public string $created_ad;
    public string $update_at;
}

class ThreadData
{
    public int $thread_number;
    public int $user_id;
    public int $toukou_number;
    public string $post_content;
    public string $created_ad;
    public string $update_at;
}

/* =========================
   DAO
   ========================= */

class ThreadBoardDAO
{
    /* =========================
       スレッド関連
       ========================= */

    // スレッド一覧取得
    public function getAllThreads()
    {
        $dbh = DAO::get_db_connect();

        $sql = "SELECT * FROM thread_title
                ORDER BY update_at DESC, thread_number DESC";

        $stmt = $dbh->prepare($sql);
        $stmt->execute();

        $data = [];
        while ($row = $stmt->fetchObject('ThreadTitle')) {
            $data[] = $row;
        }

        return $data;
    }

    // スレッド新規作成
    public function insertThread(string $thread_name)
    {
        $dbh = DAO::get_db_connect();

        $sql = "INSERT INTO thread_title
                (thread_name, created_ad, update_at)
                VALUES (?, CAST(GETDATE() AS DATE), CAST(GETDATE() AS DATE))";

        $stmt = $dbh->prepare($sql);
        return $stmt->execute([$thread_name]);
    }

    // スレッド検索
    public function searchThreads(string $keyword)
    {
        $dbh = DAO::get_db_connect();

        $sql = "SELECT * FROM thread_title
                WHERE thread_name LIKE ?
                ORDER BY created_ad DESC";

        $stmt = $dbh->prepare($sql);
        $stmt->execute(['%' . $keyword . '%']);

        $data = [];
        while ($row = $stmt->fetchObject('ThreadTitle')) {
            $data[] = $row;
        }

        return $data;
    }

    // スレッド名取得
    public function getThreadTitle(int $thread_number)
    {
        $dbh = DAO::get_db_connect();

        $sql = "SELECT * FROM thread_title
                WHERE thread_number = ?";

        $stmt = $dbh->prepare($sql);
        $stmt->execute([$thread_number]);

        return $stmt->fetchObject('ThreadTitle');
    }

    // 投稿があったらスレッド更新日を更新
    public function updateThreadTime(int $thread_number)
    {
        $dbh = DAO::get_db_connect();

        $sql = "UPDATE thread_title
                SET update_at = CAST(GETDATE() AS DATE)
                WHERE thread_number = ?";

        $stmt = $dbh->prepare($sql);
        return $stmt->execute([$thread_number]);
    }

    /* =========================
       投稿関連
       ========================= */

    // 指定スレッドの投稿取得
    public function getPostsByThread(int $thread_number)
    {
        $dbh = DAO::get_db_connect();

        $sql = "SELECT * FROM thread_data
                WHERE thread_number = ?
                ORDER BY toukou_number ASC";

        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $thread_number, PDO::PARAM_INT);
        $stmt->execute();

        $data = [];
        while ($row = $stmt->fetchObject('ThreadData')) {
            $data[] = $row;
        }

        return $data;
    }

    // 新規投稿（掲示板ごとに自動採番）
    public function insertPost(int $thread_number, int $user_id, string $content)
    {
        $dbh = DAO::get_db_connect();

        try {
            // トランザクション開始
            $dbh->beginTransaction();

            // 次の投稿番号を取得（ロック付き）
            $sql = "
                SELECT ISNULL(MAX(toukou_number), 0) + 1
                FROM thread_data WITH (UPDLOCK, HOLDLOCK)
                WHERE thread_number = ?
            ";
            $stmt = $dbh->prepare($sql);
            $stmt->execute([$thread_number]);
            $nextNumber = (int)$stmt->fetchColumn();

            // 投稿INSERT
            $sql = "
                INSERT INTO thread_data (
                    thread_number,
                    user_id,
                    toukou_number,
                    post_content,
                    created_ad,
                    update_at
                )
                VALUES (?, ?, ?, ?, GETDATE(), GETDATE())
            ";
            $stmt = $dbh->prepare($sql);
            $stmt->execute([
                $thread_number,
                $user_id,
                $nextNumber,
                $content
            ]);

            // スレッド更新日更新
            $this->updateThreadTime($thread_number);

            // コミット
            $dbh->commit();
            return true;

        } catch (Exception $e) {
            $dbh->rollBack();
            throw $e;
        }
    }
}
