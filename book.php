<?php
require_once './helpers/BookDAO.php';

// セッションの開始
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. キーワードの取得
// GETリクエストから 'keyword' を取得。なければ空文字列。
$keyword = $_GET['keyword'] ?? '';
$books = [];

// 2. キーワードが入力されている場合にのみ検索を実行
if (!empty($keyword)) {
    $BookDAO = new BookDAO();
    $books = $BookDAO->searchBooks($keyword);
}
?>


<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>書籍検索</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
        <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        />
        <link href="css/BaseDesignData.css" rel="stylesheet" />
        <?php include 'template/header.php'; ?>
        <link href="css/side.css" rel="stylesheet" />
    </head>
    <body>
        <div class="d-flex w-100 min-vh-100">
            <?php include 'template/side.php';?>
            <main class="main-content">
                <div class="container-fluid py-4">
                    <h1 class="mb-4">図書検索</h1>

                    <form action="book.php" method="get" class="mb-4">
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

                    <?php if (!empty($keyword)): ?>
                    <div class="search-results mt-4">
                        <?php if (!empty($books)): ?>
                        <h2>検索結果 (<?= count($books) ?>件)</h2>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>コード</th>
                                    <th>書籍名</th>
                                    <th>作者名</th>
                                    <th>出版社</th>
                                    <th>Amazon</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                        foreach ($books as $book):
                                        ?>
                                <tr>
                                    <td><?= htmlspecialchars($book->book_code) ?></td>
                                    <td><?= htmlspecialchars($book->book_name) ?></td>
                                    <td><?= htmlspecialchars($book->sakusya) ?></td>
                                    <td><?= htmlspecialchars($book->syuppan) ?></td>

                                    <td>
                                        <?php
                                                // 検索クエリとして書籍コードのみを使用
                                                $search_query = urlencode($book->book_code); // Amazonの検索URL
                                        $amazon_url = "https://www.amazon.co.jp/s?k=" . $search_query; ?>
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
                        <?php else: ?>
                        <p>「<?= htmlspecialchars($keyword) ?>」に関するする書籍は見つかりませんでした。</p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    <footer>
        <?php include 'template/footer.php'; ?>
    </footer>
</html>
