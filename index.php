<?php
// PHPでテーマを取得（Cookie が無ければ light）
$theme = $_COOKIE['theme'] ?? 'light';
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
            rel="stylesheet"
        />

        <link href="./css/BaseDesignData.css" rel="stylesheet" />
        <link href="./css/side.css" rel="stylesheet" />
        <link id="theme-css" rel="stylesheet" href="./css_theme/<?= htmlspecialchars($theme) ?>.css" />
        <link href="./css_theme/toggle-button.css" rel="stylesheet" />
        <title>トップページ</title>
    </head>
    <body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">
    <?php include './template/header.php'; ?>
        <div class="d-flex w-100 min-vh-100">
            <div class="d-none d-md-block">
                <?php include './template/side.php'; ?>
            </div>

            <main class="main-content">
                <h1>トップページ</h1>
                <p>ここに、メインとなるページの内容が生成されます。</p>
                <div style="height: 1500px; background-color: #f8f9fa">長いコンテンツの例</div>
            </main>
        </div>

        <button id="theme-toggle-btn" class="btn theme-toggle-btn">
            <i id="theme-icon" class="bi <?= $theme === 'dark' ? 'bi-sun' : 'bi-moon' ?>"></i>
        </button>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>

        <script src="./js/theme-toggle_top.js"></script>
    </body>

    <footer>
        <?php include './template/footer.php'; ?>
    </footer>
</html>
