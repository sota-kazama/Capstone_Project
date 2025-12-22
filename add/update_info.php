<?php
require_once '../helpers/UpdateDAO.php';
require_once '../helpers/MemberDAO.php';

session_start();

if (!isset($_SESSION['member'])) {
    header('Location: login.php');
    exit;
}

$theme = $_COOKIE['theme'] ?? 'light';
$updateDAO = new UpdateDAO();
$message = "";

/* ===== POST処理 ===== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    // 追加
    if ($action === 'add') {
        $up_info = trim($_POST['up_info'] ?? '');
        if ($up_info === '') {
            $message = "更新内容を入力してください。";
        } else {
            $updateDAO->insert($up_info);
            $message = "更新情報を追加しました。";
        }
    }

    // 編集
    if ($action === 'edit') {
        $update_id = (int)$_POST['update_id'];
        $up_info = trim($_POST['up_info'] ?? '');
        if ($up_info === '') {
            $message = "更新内容を入力してください。";
        } else {
            $updateDAO->update($update_id, $up_info);
            $message = "更新情報を更新しました。";
        }
    }

    // 削除
    if ($action === 'delete') {
        $update_id = (int)$_POST['update_id'];
        $updateDAO->delete($update_id);
        $message = "更新情報を削除しました。";
    }
}

$updateList = $updateDAO->getAll();
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

    <title>更新情報管理</title>
</head>

<body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">
<?php include '../template/header2.php'; ?>

<div class="d-flex w-100 min-vh-100">
<?php include 'side.php'; ?>

<main class="main-content container mt-4">

    <!-- 追加フォーム -->
    <div class="card mt-5 p-4">
        <h4>更新情報追加</h4>

        <?php if ($message): ?>
            <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="post">
    <input type="hidden" name="action" value="add">
    <div class="mb-3">
        <label class="form-label">更新内容</label>
        <textarea name="up_info" class="form-control" rows="4" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> 追加
    </button>
</form>
        </form>
    </div>

    <!-- 一覧 -->
    <div class="card mt-4 p-4">
        <h4>更新情報一覧</h4>

        <?php if (empty($updateList)): ?>
            <p class="text-muted">更新情報はまだありません。</p>
        <?php else: ?>
            <table class="table table-striped mt-3">
    <thead>
        <tr>
            <th>ID</th>
            <th>更新内容</th>
            <th>作成日</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($updateList as $update): ?>
        <tr>
            <td><?= htmlspecialchars($update->update_id) ?></td>

            <!-- 編集フォーム（インライン） -->
            <td>
                <form method="post" class="d-flex gap-2">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="update_id" value="<?= $update->update_id ?>">
                    <textarea name="up_info" class="form-control" rows="2"><?= htmlspecialchars($update->up_info) ?></textarea>
                    <button class="btn btn-success btn-sm">
                        <i class="bi bi-check-lg"></i>
                    </button>
                </form>
            </td>

            <td><?= htmlspecialchars($update->created_ad) ?></td>

            <!-- 削除 -->
            <td>
                <form method="post"
                    onsubmit="return confirm('本当に削除しますか？');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="update_id" value="<?= $update->update_id ?>">
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

<button id="theme-toggle-btn" class="btn theme-toggle-btn">
    <i id="theme-icon" class="bi <?= $theme === 'dark' ? 'bi-sun' : 'bi-moon' ?>"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/theme-toggle.js"></script>
</body>

<footer>
<?php include '../template/footer.php'; ?>
</footer>
</html>
