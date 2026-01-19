<?php
require_once '../helpers/BookDAO.php';
require_once '../helpers/MemberDAO.php';

session_start();

if (!isset($_SESSION['member'])) {
    header('Location: login.php');
    exit;
}

$member = $_SESSION['member'];
$dao = new BookDAO();

$message = '';
$error = '';
$theme = $_COOKIE['theme'] ?? 'light';

/* 一覧 */
$books = $dao->getAllBooks();

/* 検索 */
$isSearching = false;
$searchResults = [];

if (isset($_GET['search'])) {
    $keyword = trim($_GET['keyword'] ?? '');
    if ($keyword !== '') {
        $searchResults = $dao->searchBooks($keyword);
        $isSearching = true;
    }
}

/* 登録 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    try {
        $book_code = $_POST['book_code'] ?? '';
        $book_name = $_POST['book_name'] ?? '';
        $sakusya   = $_POST['sakusya'] ?? '';
        $syuppan   = $_POST['syuppan'] ?? '';

        if ($book_code === '' || $book_name === '' || $sakusya === '' || $syuppan === '') {
            throw new Exception('全ての項目を入力してください。');
        }

        if ($dao->insertBook($book_code, $book_name, $sakusya, $syuppan)) {
            $message = '書籍を登録しました。';
            $books = $dao->getAllBooks();
        } else {
            $error = '登録に失敗しました。';
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>書籍登録</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="../css/BaseDesignData.css" rel="stylesheet">
<link href="../css/side.css" rel="stylesheet">
<link id="theme-css" href="../css_theme/<?= $theme ?>.css" rel="stylesheet">
</head>

<body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">
    <?php include '../template/header2.php'; ?>
<div class="d-flex min-vh-100">
<?php include 'side.php'; ?>

<main class="flex-grow-1 p-4">

<h1>書籍登録</h1>

<?php if ($message): ?>
<div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- ================= 新規登録 ================= -->
<div class="card p-4 mt-4">
<h4>書籍新規登録</h4>

<form method="POST">
<input type="hidden" name="register">

<div class="mb-3">
<label>ISBN-13</label>
<input type="text" name="book_code" class="form-control" required>
</div>

<div class="mb-3">
<label>書籍名</label>
<input type="text" name="book_name" class="form-control" required>
</div>

<div class="mb-3">
<label>作者</label>
<input type="text" name="sakusya" class="form-control" required>
</div>

<div class="mb-3">
<label>出版社</label>
<input type="text" name="syuppan" class="form-control" required>
</div>

<button class="btn btn-primary">登録</button>
</form>
</div>



<!-- ================= 検索結果 ================= -->
<?php if ($isSearching): ?>
<div class="card p-4 mt-4">
<h4>検索結果（<?= count($searchResults) ?>件）</h4>

<table class="table table-striped mt-3">
<thead>
<tr>
<th>ISBN</th>
<th>書籍名</th>
<th>作者</th>
<th>出版社</th>
<th>操作</th>
</tr>
</thead>
<tbody>
<?php foreach ($searchResults as $b): ?>
<tr>
<td><?= htmlspecialchars($b->book_code) ?></td>
<td><?= htmlspecialchars($b->book_name) ?></td>
<td><?= htmlspecialchars($b->sakusya) ?></td>
<td><?= htmlspecialchars($b->syuppan) ?></td>
<td>
<a href="book_edit.php?code=<?= $b->book_code ?>" class="btn btn-sm btn-warning">編集</a>
<a href="book_delete.php?code=<?= $b->book_code ?>"
   class="btn btn-sm btn-danger"
   onclick="return confirm('削除しますか？');">削除</a>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php endif; ?>

<!-- ================= 検索（← 登録の下） ================= -->
<div class="card p-4 mt-5">
<h4>書籍検索</h4>

<form method="GET" class="d-flex gap-2">
<input type="text" name="keyword" class="form-control"
       value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>"
       placeholder="ISBN・書籍名・作者・出版社">
<button class="btn btn-secondary" name="search">検索</button>
</form>
</div>

<!-- ================= 一覧（検索中は最小） ================= -->
<div class="card p-4 mt-5">
<h4>
書籍一覧
<button class="btn btn-sm btn-outline-secondary ms-2"
        data-bs-toggle="collapse"
        data-bs-target="#bookList">
表示切替
</button>
</h4>

<div id="bookList" class="collapse <?= $isSearching ? '' : 'show' ?>">
<table class="table table-striped mt-3">
<thead>
<tr>
<th>ISBN</th>
<th>書籍名</th>
<th>作者</th>
<th>出版社</th>
<th>操作</th>
</tr>
</thead>
<tbody>
<?php foreach ($books as $b): ?>
<tr>
<td><?= htmlspecialchars($b->book_code) ?></td>
<td><?= htmlspecialchars($b->book_name) ?></td>
<td><?= htmlspecialchars($b->sakusya) ?></td>
<td><?= htmlspecialchars($b->syuppan) ?></td>
<td>
<a href="book_edit.php?code=<?= $b->book_code ?>" class="btn btn-sm btn-warning">編集</a>
<a href="book_delete.php?code=<?= $b->book_code ?>"
   class="btn btn-sm btn-danger"
   onclick="return confirm('削除しますか？');">削除</a>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</div>

</main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
