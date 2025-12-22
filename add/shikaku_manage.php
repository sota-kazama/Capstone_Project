<?php
require_once '../helpers/MemberDAO.php';
require_once '../helpers/ShikakuDAO.php';

session_start();

if (!isset($_SESSION['member'])) {
    header('Location: login.php');
    exit;
}

$member = $_SESSION['member'];
$dao = new ShikakuDAO();
$message = "";

// --- POST処理 ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $s_name = trim($_POST['s_name']);
        if ($s_name !== '') {
            $dao->insert($s_name);
            $message = "資格を追加しました。";
        }
    } elseif (isset($_POST['update'])) {
        $dao->update((int)$_POST['s_number'], $_POST['s_name']);
        $message = "資格を更新しました。";
    } elseif (isset($_POST['delete'])) {
        $dao->delete((int)$_POST['s_number']);
        $message = "資格を削除しました。";
    }

    // --- ここがポイント ---
    // POSTが完了したらリダイレクトしてGETに切り替える
    header("Location: shikaku_manage.php?msg=" . urlencode($message));
    exit;
}

// --- GETでページ表示 ---
if (isset($_GET['msg'])) {
    $message = $_GET['msg'];
}

$list = $dao->getAll();
?>


<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
            rel="stylesheet"
        />
        <link href="../css/BaseDesignData.css" rel="stylesheet" />
        <link href="../css/side.css" rel="stylesheet" />
        <title>資格管理</title>
    </head>

    <body>
        <div class="d-flex w-100 min-vh-100">
            <?php include 'side.php'; ?>
            <main class="main-content container mt-4">
                <h1>資格管理</h1>

                <?php if ($message): ?>
                <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

                <!-- 新規追加フォーム -->
                <div class="card mb-4">
                    <div class="card-header">新しい資格を追加</div>
                    <div class="card-body">
                        <form method="post">
                            <div class="mb-3">
                                <label for="s_name" class="form-label">資格名</label>
                                <input type="text" name="s_name" id="s_name" class="form-control" required />
                            </div>
                            <button type="submit" name="add" class="btn btn-primary">追加</button>
                        </form>
                    </div>
                </div>

                <!-- 一覧 -->
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>資格名</th>
                            <th>登録日</th>
                            <th>更新日</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($list as $row): ?>
                        <tr>
                            <form method="post">
                                <td><?= htmlspecialchars($row->s_number) ?></td>
                                <td>
                                    <input
                                        type="text"
                                        name="s_name"
                                        value="<?= htmlspecialchars($row->s_name) ?>"
                                        class="form-control"
                                    />
                                </td>
                                <td><?= htmlspecialchars($row->created_ad) ?></td>
                                <td><?= htmlspecialchars($row->update_at) ?></td>
                                <td>
                                    <input
                                        type="hidden"
                                        name="s_number"
                                        value="<?= htmlspecialchars($row->s_number) ?>"
                                    />
                                    <button type="submit" name="update" class="btn btn-sm btn-success">更新</button>
                                    <button
                                        type="submit"
                                        name="delete"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('削除しますか？');"
                                    >
                                        削除
                                    </button>
                                </td>
                            </form>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </main>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>

    <footer>
        <?php include '../template/footer.php'; ?>
    </footer>
</html>
