<?php
require_once './helpers/BookDAO.php';
require_once './helpers/MemberDAO.php';

// セッション開始
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cookieのテーマ読み込み（無ければlight）
$theme = $_COOKIE['theme'] ?? 'light';

// GETパラメータ
$keyword = $_GET['keyword'] ?? '';
$books = [];

if (!empty($keyword)) {
    $BookDAO = new BookDAO();
    $books = $BookDAO->searchBooks($keyword);
}

// ログイン
$member = $_SESSION['member'] ?? null;
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />

    <link href="./css/BaseDesignData.css" rel="stylesheet" />
    <link href="./css/side.css" rel="stylesheet" />
    <link id="theme-css" rel="stylesheet" href="./css_theme/<?= htmlspecialchars($theme) ?>.css" />
    <link href="./css_theme/toggle-button.css" rel="stylesheet" />
    <title>図書検索</title>
</head>

<body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">
<?php include './template/header.php'; ?>

<div class="d-flex w-100 min-vh-100">

    <div class="d-none d-md-block">
        <?php include 'template/side.php'; ?>
    </div>

    <main class="flex-grow-1 p-4">

        <h1 class="mb-3">図書検索</h1>

        <!-- ================= 検索フォーム ================= -->
        <div class="card p-4 mt-3">
            <form action="book.php" method="get">
                <div class="input-group">
                    <input
                        type="text"
                        name="keyword"
                        placeholder="書籍を検索"
                        required
                        class="form-control"
                        value="<?= htmlspecialchars($keyword) ?>"
                    />
                    <button type="submit" class="btn btn-primary">検索</button>
                </div>
            </form>
        </div>

        <!-- ================= 検索結果 ================= -->
        <?php if (!empty($keyword)): ?>
            <div class="card p-4 mt-3">
                <?php if (!empty($books)): ?>
                    <h4>検索結果 (<?= count($books) ?>件)</h4>

                    <div class="table-responsive mt-3">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>書籍コード</th>
                                    <th>書籍名</th>
                                    <th>作者名</th>
                                    <th>出版社</th>
                                    <th>Amazon</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($books as $book): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($book->book_code) ?></td>
                                        <td><?= htmlspecialchars($book->book_name) ?></td>
                                        <td><?= htmlspecialchars($book->sakusya) ?></td>
                                        <td><?= htmlspecialchars($book->syuppan) ?></td>
                                        <td>
                                            <?php $amazon_url = "https://www.amazon.co.jp/s?k=" . urlencode($book->book_code); ?>
                                            <a
                                                href="<?= $amazon_url ?>"
                                                target="_blank"
                                                class="btn btn-warning btn-sm"
                                                title="<?= htmlspecialchars($book->book_code) ?>で検索"
                                            >
                                                Amazonで検索
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="mt-2">「<?= htmlspecialchars($keyword) ?>」の検索結果はありませんでした。</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </main>
</div>

<!-- ================= テーマ切替 ================= -->
<button id="theme-toggle-btn" class="btn btn-primary theme-toggle-btn">
    <i id="theme-icon" class="bi <?= $theme === 'dark' ? 'bi-sun' : 'bi-moon' ?>"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="./js/theme-toggle_top.js"></script>

<footer>
    <?php include './template/footer.php'; ?>
</footer>
</body>
</html>
