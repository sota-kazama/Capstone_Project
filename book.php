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
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>書籍検索</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />

    <link href="css_theme/base.css" rel="stylesheet" />
    <link href="css_theme/layout-common.css" rel="stylesheet" />
    <link href="css_theme/side.css" rel="stylesheet" />

    <!-- テーマCSS -->
    <link id="theme-css" href="css_theme/<?= htmlspecialchars($theme) ?>.css" rel="stylesheet" />

    <style>
        .theme-toggle-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            font-size: 1.2rem;
        }
    </style>
</head>

<body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">

    <?php include 'template/header.php'; ?>

    <div class="d-flex w-100 min-vh-100">
        <div class="d-none d-md-block">
            <?php include 'template/side.php'; ?>
        </div>

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
                                        <a href="<?= $amazon_url ?>" target="_blank" class="btn btn-warning btn-sm" title="<?= htmlspecialchars($book->book_code) ?>で検索">
                                            Amazonで検索
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>「<?= htmlspecialchars($keyword) ?>」の検索結果はありませんでした。</p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- テーマ切替ボタン -->
    <button id="theme-toggle-btn" class="theme-toggle-btn btn btn-primary">
        <i id="theme-icon" class="bi <?= $theme === 'dark' ? 'bi-sun' : 'bi-moon' ?>"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function updateServerTheme(theme) {
            fetch("theme_set.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "theme=" + theme
            })
            .then(res => res.json())
            .then(data => console.log("テーマ更新:", data))
            .catch(err => console.error(err));
        }

        function toggleTheme() {
            const themeLink = document.getElementById("theme-css");
            const icon = document.getElementById("theme-icon");
            const body = document.body;

            const currentTheme = themeLink.getAttribute("href");
            let newTheme;

            if (currentTheme.includes("light.css")) {
                themeLink.href = "css_theme/dark.css";
                icon.classList.replace("bi-moon", "bi-sun");
                body.classList.replace("light-mode", "dark-mode");
                newTheme = "dark";
            } else {
                themeLink.href = "css_theme/light.css";
                icon.classList.replace("bi-sun", "bi-moon");
                body.classList.replace("dark-mode", "light-mode");
                newTheme = "light";
            }

            updateServerTheme(newTheme);
        }

        document.addEventListener("DOMContentLoaded", () => {
            document.getElementById("theme-toggle-btn").addEventListener("click", toggleTheme);
        });
    </script>

    <?php include 'template/footer.php'; ?>
</body>
</html>
