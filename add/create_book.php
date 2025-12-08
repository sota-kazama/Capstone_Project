<?php
require_once '../helpers/BookDAO.php';
require_once '../helpers/MemberDAO.php';

session_start();

// ログイン確認
if (!isset($_SESSION['member'])) {
    header('Location: login.php');
    exit;
}

$member = $_SESSION['member'];

// 管理者アクセス制御
if ($member->u_admin !== 1 && $member->u_admin !== '1') {
    header('Location: index.php');
    exit;
}

$dao = new BookDAO();
$message = '';
$error = '';

// ★ 全件取得（一覧表示用）
$books = $dao->getAllBooks();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $book_code  = $_POST['book_code'] ?? '';
        $book_name  = $_POST['book_name'] ?? '';
        $sakusya    = $_POST['sakusya'] ?? '';
        $syuppan    = $_POST['syuppan'] ?? '';

        if ($book_code === '' || $book_name === '' || $sakusya === '' || $syuppan === '') {
            throw new Exception("全ての項目を入力してください。");
        }

        $success = $dao->insertBook($book_code, $book_name, $sakusya, $syuppan);

        if ($success) {
            $message = "書籍を登録しました。";

            // 登録後に最新一覧を再取得
            $books = $dao->getAllBooks();

        } else {
            $error = "登録に失敗しました。";
        }

    } catch (Exception $e) {
        $error = "エラー: " . $e->getMessage();
    }
}
?>



<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
        <link href="../css/BaseDesignData.css" rel="stylesheet" />
        <link href="../css/side.css" rel="stylesheet" />
        <?php include '../template/header2.php'; ?>
        <title>書籍登録</title>
    </head>

    <body>
        <div class="d-flex w-100 min-vh-100">
            <?php include 'side.php'; ?>

            <main class="main-content flex-grow-1 p-4">
                <div class="d-flex align-items-center mb-3">
                    <h1 class="m-0">書籍登録</h1>
                    <a href="book_search.php" class="btn btn-secondary ms-auto">書籍検索</a>
                </div>

                <?php if ($message): ?>
                <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

                <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <!-- ▼ 書籍登録フォーム -->
                <div class="card p-4 mt-3">
                    <h4>書籍新規登録フォーム</h4>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">ISBN-13コード</label>
                            <input type="text" class="form-control" name="book_code" maxlength="14" required />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">書籍名</label>
                            <input type="text" class="form-control" name="book_name" maxlength="50" required />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">作者名</label>
                            <input type="text" class="form-control" name="sakusya" maxlength="50" required />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">出版社名</label>
                            <input type="text" class="form-control" name="syuppan" maxlength="50" required />
                        </div>

                        <button type="submit" class="btn btn-primary">登録</button>
                    </form>
                </div>

                <!-- ▼ ここから書籍一覧 -->
                <div class="card p-4 mt-5">
                    <h4>書籍一覧</h4>

                    <table class="table table-striped mt-3">
                        <thead>
                            <tr>
                                <th>ISBNコード</th>
                                <th>書籍名</th>
                                <th>作者</th>
                                <th>出版社</th>
                                <th>登録日</th>
                                <th>更新日</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($books as $b): ?>
                            <tr>
                                <td><?= htmlspecialchars($b->book_code) ?></td>
                                <td><?= htmlspecialchars($b->book_name) ?></td>
                                <td><?= htmlspecialchars($b->sakusya) ?></td>
                                <td><?= htmlspecialchars($b->syuppan) ?></td>
                                <td><?= htmlspecialchars($b->created_ad) ?></td>
                                <td><?= htmlspecialchars($b->update_at) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </body>
</html>
