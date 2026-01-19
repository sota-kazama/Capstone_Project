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

// テーマ
$theme = $_COOKIE['theme'] ?? 'light';

/* ===== POST処理 ===== */
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

    header("Location: shikaku_manage.php?msg=" . urlencode($message));
    exit;
}

if (isset($_GET['msg'])) {
    $message = $_GET['msg'];
}

/* ===== 一覧 ===== */
$list = $dao->getAll();

/* ===== 検索 ===== */
$isSearching = false;
$searchResults = [];

if (isset($_GET['search'])) {
    $keyword = trim($_GET['keyword'] ?? '');
    if ($keyword !== '') {
        $searchResults = $dao->search($keyword);
        $isSearching = true;
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>資格管理</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="../css/BaseDesignData.css" rel="stylesheet">
<link href="../css/side.css" rel="stylesheet">
<link id="theme-css" href="../css_theme/<?= htmlspecialchars($theme) ?>.css" rel="stylesheet">
<link href="../css_theme/toggle-button.css" rel="stylesheet">
</head>

<body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">
<?php include '../template/header2.php'; ?>

<div class="d-flex w-100 min-vh-100">
<?php include 'side.php'; ?>

<main class="main-content flex-grow-1 p-4">

<h1>資格管理</h1>

<?php if ($message): ?>
<div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<!-- ================= 新規追加 ================= -->
<div class="card p-4 mt-3">
<h4>新しい資格を追加</h4>

<form method="post" class="mt-3">
<div class="mb-3">
<label class="form-label">資格名</label>
<input type="text" name="s_name" class="form-control" required>
</div>
<button type="submit" name="add" class="btn btn-primary">追加</button>
</form>
</div>

<!-- ================= 検索 ================= -->
<div class="card p-4 mt-4">
<h4>資格検索</h4>

<form method="get" class="d-flex gap-2">
<input type="text" name="keyword" class="form-control"
       value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>"
       placeholder="資格名で検索">
<button type="submit" name="search" class="btn btn-secondary">検索</button>
</form>
</div>

<!-- ================= 検索結果 ================= -->
<?php if ($isSearching): ?>
<div class="card p-4 mt-4">
<h4>検索結果（<?= count($searchResults) ?>件）</h4>

<table class="table table-striped align-middle mt-3">
<thead>
<tr>
<th>ID</th>
<th>資格名</th>
<th>登録日</th>
<th>更新日</th>
<th>操作</th>
</tr>
</thead>
<tbody>
<?php foreach ($searchResults as $row): ?>
<tr>
<form method="post">
<td><?= htmlspecialchars($row->s_number) ?></td>
<td>
<input type="text" name="s_name"
       value="<?= htmlspecialchars($row->s_name) ?>"
       class="form-control">
</td>
<td><?= htmlspecialchars($row->created_ad) ?></td>
<td><?= htmlspecialchars($row->update_at) ?></td>
<td class="d-flex gap-1">
<input type="hidden" name="s_number" value="<?= $row->s_number ?>">
<button name="update" class="btn btn-sm btn-success">更新</button>
<button name="delete" class="btn btn-sm btn-danger"
        onclick="return confirm('削除しますか？');">削除</button>
</td>
</form>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php endif; ?>

<!-- ================= 一覧（検索中は最小） ================= -->
<div class="card p-4 mt-5">
<h4>
登録済み資格一覧
<button class="btn btn-sm btn-outline-secondary ms-2"
        data-bs-toggle="collapse"
        data-bs-target="#shikakuList">
表示切替
</button>
</h4>

<div id="shikakuList" class="collapse <?= $isSearching ? '' : 'show' ?>">
<table class="table table-striped align-middle mt-3">
<thead>
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
<input type="text" name="s_name"
       value="<?= htmlspecialchars($row->s_name) ?>"
       class="form-control">
</td>
<td><?= htmlspecialchars($row->created_ad) ?></td>
<td><?= htmlspecialchars($row->update_at) ?></td>
<td class="d-flex gap-1">
<input type="hidden" name="s_number" value="<?= $row->s_number ?>">
<button name="update" class="btn btn-sm btn-success">更新</button>
<button name="delete" class="btn btn-sm btn-danger"
        onclick="return confirm('削除しますか？');">削除</button>
</td>
</form>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</div>

</main>
</div>

<button id="theme-toggle-btn" class="btn btn-primary theme-toggle-btn">
<i id="theme-icon" class="bi <?= $theme === 'dark' ? 'bi-sun' : 'bi-moon' ?>"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/theme-toggle.js"></script>

<footer><?php include '../template/footer.php'; ?></footer>
</body>
</html>
