<?php
require_once '../helpers/BugDAO.php';
require_once '../helpers/MemberDAO.php';

session_start();

if (!isset($_SESSION['member'])) {
    header('Location: login.php');
    exit;
}

$theme = $_COOKIE['theme'] ?? 'light';
$bugDAO = new BugDAO();
$message = "";

/* ===== POST処理 ===== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $bug_info = trim($_POST['bug_info'] ?? '');
        if ($bug_info === '') {
            $message = "バグ内容を入力してください。";
        } else {
            $bugDAO->insert($bug_info);
            $message = "バグを追加しました。";
        }
    }

    if ($action === 'edit') {
        $bug_id = (int)$_POST['bug_id'];
        $bug_info = trim($_POST['bug_info'] ?? '');
        if ($bug_info === '') {
            $message = "バグ内容を入力してください。";
        } else {
            $bugDAO->update($bug_id, $bug_info);
            $message = "バグ情報を更新しました。";
        }
    }

    if ($action === 'delete') {
        $bug_id = (int)$_POST['bug_id'];
        $bugDAO->delete($bug_id);
        $message = "バグを削除しました。";
    }
}

$bugList = $bugDAO->getAll();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <link href="../css/BaseDesignData.css" rel="stylesheet">
    <link href="../css/side.css" rel="stylesheet">
    <link id="theme-css" rel="stylesheet" href="../css_theme/<?= htmlspecialchars($theme) ?>.css">
    <link href="../css_theme/toggle-button.css" rel="stylesheet">

    <title>バグ管理</title>
</head>
<body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">
<?php include '../template/header2.php'; ?>

<div class="d-flex w-100 min-vh-100">
    <?php include 'side.php'; ?>

    <main class="main-content flex-grow-1 p-4">
        <div class="d-flex align-items-center mb-3">
            <h1 class="m-0">バグ管理</h1>
        </div>

        <!-- 追加フォーム -->
        <div class="card p-4 mt-3">
            <h4>バグ追加</h4>

            <?php if ($message): ?>
                <div class="alert alert-info mt-3">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form method="post" class="mt-3">
                <input type="hidden" name="action" value="add">

                <div class="mb-3">
                    <label class="form-label">バグ内容</label>
                    <textarea name="bug_info" class="form-control" rows="4" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> 追加
                </button>
            </form>
        </div>

        <!-- 一覧 -->
        <div class="card p-4 mt-5">
            <h4>バグ一覧</h4>

            <?php if (empty($bugList)): ?>
                <p class="text-muted mt-3">バグ情報はまだありません。</p>
            <?php else: ?>
                <table class="table table-striped align-middle mt-3">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>バグ内容</th>
                            <th>登録日</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($bugList as $bug): ?>
                        <tr>
                            <td><?= htmlspecialchars($bug->bug_id) ?></td>

                            <!-- 編集 -->
                            <td>
                                <form method="post" class="d-flex gap-2">
                                    <input type="hidden" name="action" value="edit">
                                    <input type="hidden" name="bug_id" value="<?= $bug->bug_id ?>">
                                    <textarea name="bug_info" class="form-control" rows="2"><?= htmlspecialchars($bug->bug_info) ?></textarea>
                                    <button class="btn btn-success btn-sm">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                            </td>

                            <td><?= htmlspecialchars($bug->created_at) ?></td>

                            <!-- 削除 -->
                            <td>
                                <form method="post" onsubmit="return confirm('本当に削除しますか？');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="bug_id" value="<?= $bug->bug_id ?>">
                                    <button class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>
</div>

<button id="theme-toggle-btn" class="btn btn-primary theme-toggle-btn">
    <i id="theme-icon" class="bi <?= $theme === 'dark' ? 'bi-sun' : 'bi-moon' ?>"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/theme-toggle.js"></script>

<footer>
<?php include '../template/footer.php'; ?>
</footer>
</body>
</html>
