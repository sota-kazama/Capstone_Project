<?php
// PHPでテーマを取得（Cookie が無ければ light）
$theme = $_COOKIE['theme'] ?? 'light';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />

    <!-- 基本 CSS -->
    <link href="css/BaseDesignData.css" rel="stylesheet" />
    <link href="css/side.css" rel="stylesheet" />

    <!-- テーマ CSS（自動切替） -->
    <link id="theme-css" href="css_theme/<?= $theme ?>.css" rel="stylesheet" />

    <!-- カスタム CSS -->
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

    <?php include 'template/header.php'; ?>
    <title>メインページ</title>
</head>
<body>
    <div class="d-flex w-100 min-vh-100">
        <div class="d-none d-md-block">
            <?php include 'template/side.php'; ?>
        </div>

        <main class="main-content">
            <h1>トップページ</h1>
            <p>ここに、メインとなるページの内容が生成されます。</p>
            <div style="height: 1500px; background-color: #f8f9fa">長いコンテンツの例</div>
        </main>
    </div>

    <!-- テーマ切替ボタン -->
    <button id="theme-toggle-btn" class="theme-toggle-btn btn btn-primary">
        <i id="theme-icon" class="bi <?= $theme === 'dark' ? 'bi-sun' : 'bi-moon' ?>"></i>
    </button>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- テーマ切替 JS -->
    <script>
        // サーバーにテーマを反映
        function updateServerTheme(theme) {
            fetch('theme_set.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'theme=' + theme
            }).then(res => res.json())
              .then(data => console.log('テーマ更新:', data))
              .catch(err => console.error(err));
        }

        // テーマ切替関数（css_theme 対応済）
        function toggleTheme() {
            const themeLink = document.getElementById("theme-css");
            const icon = document.getElementById("theme-icon");

            let newTheme;

            if (themeLink.href.includes("light.css")) {
                // ダークテーマへ
                themeLink.href = "css_theme/dark.css";
                icon.classList.remove("bi-moon");
                icon.classList.add("bi-sun");
                newTheme = 'dark';
            } else {
                // ライトテーマへ
                themeLink.href = "css_theme/light.css";
                icon.classList.remove("bi-sun");
                icon.classList.add("bi-moon");
                newTheme = 'light';
            }

            // サーバー側に反映
            updateServerTheme(newTheme);
        }

        // ボタンイベント登録
        document.addEventListener("DOMContentLoaded", () => {
            document.getElementById("theme-toggle-btn")
                .addEventListener("click", toggleTheme);
        });
    </script>
</body>
<footer>
    <?php include 'template/footer.php'; ?>
</footer>
</html>
