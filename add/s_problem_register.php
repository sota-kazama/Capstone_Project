<?php
require_once '../helpers/FieldDAO.php';
require_once '../helpers/ShikakuDAO.php';
require_once '../helpers/MemberDAO.php';

session_start();

// ログインチェック
if (!isset($_SESSION['member'])) {
    header('Location: login.php');
    exit;
}

$member = $_SESSION['member'];
$fieldDAO = new FieldDAO();
$shikakuDAO = new ShikakuDAO();

// メッセージ初期化
$message = "";

// テーマ
$theme = $_COOKIE['theme'] ?? 'light';

// 資格一覧
$shikakuList = $shikakuDAO->getAll();

// --- 検索処理 ---
$searchKeyword = trim($_GET['search'] ?? '');

// --- POST処理 ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $areaCode  = trim($_POST['area_code'] ?? '');
    $fieldName = trim($_POST['field_name'] ?? '');
    $sName     = trim($_POST['s_name'] ?? '');

    // 資格名 → 資格番号
    $sNumber = null;
    foreach ($shikakuList as $s) {
        if ($s->s_name === $sName) {
            $sNumber = $s->s_number;
            break;
        }
    }

    if (isset($_POST['delete'])) {
        $fieldDAO->delete($areaCode);
        $message = "分野を削除しました。";
    } elseif ($sNumber === null && !isset($_POST['delete'])) {
        $message = "選択された資格が存在しません。";
    } else {
        if (isset($_POST['update'])) {
            $fieldDAO->update($areaCode, $fieldName, $sNumber);
            $message = "分野を更新しました。";
        } elseif (isset($_POST['add'])) {
            $fieldDAO->insert($areaCode, $fieldName, $sNumber);
            $message = "分野を追加しました。";
        }
    }

    header("Location: " . $_SERVER['PHP_SELF'] . "?msg=" . urlencode($message));
    exit;
}

// --- GET表示 ---
if (isset($_GET['msg'])) {
    $message = $_GET['msg'];
}

// 分野一覧取得（検索対応）
$fields = $fieldDAO->getAll();
if ($searchKeyword !== '') {
    $fields = array_filter($fields, function($f) use ($searchKeyword) {
        return mb_stripos($f->area_name, $searchKeyword) !== false
            || mb_stripos($f->s_name ?? '', $searchKeyword) !== false;
    });
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />

    <link href="../css/BaseDesignData.css" rel="stylesheet" />
    <link href="../css/side.css" rel="stylesheet" />
    <link id="theme-css" rel="stylesheet" href="../css_theme/<?= htmlspecialchars($theme) ?>.css" />
    <link href="../css_theme/toggle-button.css" rel="stylesheet" />

    <title>問題分野管理</title>
    <?php include '../template/header2.php'; ?>
</head>

<body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">
<div class="d-flex w-100 min-vh-100">
    <?php include 'side.php'; ?>

    <main class="main-content flex-grow-1 p-4">
        <div class="d-flex align-items-center mb-3">
            <h1 class="m-0">問題分野管理</h1>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- 検索フォーム -->
        <div class="card p-4 mt-3">
            <form method="get" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="分野名または資格名で検索"
                       value="<?= htmlspecialchars($searchKeyword) ?>" />
                <button type="submit" class="btn btn-outline-primary">検索</button>
                <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-outline-secondary">リセット</a>
            </form>
        </div>

        <!-- 新規登録フォーム -->
        <div class="card p-4 mt-4">
            <h4>新しい分野を登録</h4>

            <form method="post" class="row g-3 mt-1">
                <div class="col-md-3">
                    <input type="text" name="area_code" class="form-control" placeholder="分野コード" required />
                </div>
                <div class="col-md-4">
                    <input type="text" name="field_name" class="form-control" placeholder="分野名" required />
                </div>
                <div class="col-md-3">
                    <select name="s_name" class="form-select" required>
                        <option value="">資格を選択</option>
                        <?php foreach ($shikakuList as $s): ?>
                            <option value="<?= htmlspecialchars($s->s_name) ?>">
                                <?= htmlspecialchars($s->s_name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" name="add" class="btn btn-primary w-100">登録</button>
                </div>
            </form>
        </div>

        <!-- 分野一覧 -->
        <div class="card p-4 mt-4">
            <h4>登録済み分野一覧</h4>

            <table class="table table-striped align-middle mt-3">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>分野コード</th>
                        <th>分野名</th>
                        <th>資格名</th>
                        <th>作成日時</th>
                        <th>更新日時</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fields as $index => $field): ?>
                    <tr>
                        <form method="post">
                            <td><?= $index + 1 ?></td>
                            <td>
                                <input type="text" name="area_code" class="form-control"
                                       value="<?= htmlspecialchars($field->area_number) ?>" readonly />
                            </td>
                            <td>
                                <input type="text" name="field_name" class="form-control"
                                       value="<?= htmlspecialchars($field->area_name) ?>" />
                            </td>
                            <td>
                                <select name="s_name" class="form-select" required>
                                    <?php foreach ($shikakuList as $s): ?>
                                        <option value="<?= htmlspecialchars($s->s_name) ?>"
                                            <?= ($field->s_number === $s->s_number) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($s->s_name) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><?= htmlspecialchars($field->created_ad ?? '') ?></td>
                            <td><?= htmlspecialchars($field->update_at ?? '') ?></td>
                            <td class="d-flex gap-1">
                                <button type="submit" name="update" class="btn btn-sm btn-success">更新</button>
                                <button type="submit" name="delete"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('削除しますか？');">
                                    削除
                                </button>
                            </td>
                        </form>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<!-- テーマ切替ボタン -->
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
