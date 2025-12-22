<?php
require_once './helpers/updateDAO.php';

// PHPでテーマを取得（Cookie が無ければ light）
$theme = $_COOKIE['theme'] ?? 'light';
?>



<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />

        <!-- カスタムCSS -->
        <link href="./css/BaseDesignData.css" rel="stylesheet" />
        <link href="./css/side.css" rel="stylesheet" />
        <link id="theme-css" rel="stylesheet" href="./css_theme/<?= htmlspecialchars($theme) ?>.css" />
        <link href="./css_theme/toggle-button.css" rel="stylesheet" />

        <title>トップページ</title>
    </head>
    <body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">

        <!-- ヘッダー -->
        <?php include './template/header.php'; ?>

        <div class="d-flex w-100 min-vh-100">
            <!-- サイドバー（中サイズ以上のみ表示） -->
            <div class="d-none d-md-block">
                <?php include './template/side.php'; ?>
            </div>

            <!-- メインコンテンツ -->
            <main class="main-content">
                <!--ここに記載する-->
                <h1>トップページ</h1>
                <p>ここに、メインとなるページの内容が生成されます。</p>

                <!-- 更新情報セクション -->
                <section class="mt-4">
                    <h2 class="mb-3">更新情報</h2>
                    <?php
                        $updateDAO = new UpdateDAO();
                        $updates = $updateDAO->getInfo(); // 最新3件取得

                        if (!empty($updates)) {
                            echo '<div class="p-4 rounded shadow-sm">'; // 角丸＋影の外枠

                            foreach ($updates as $update) {
                                $date = date('Y-m-d', strtotime($update->created_ad));
                                $content = htmlspecialchars($update->up_info);

                                echo <<<HTML
                                <div class="mb-2 p-2 rounded border">
                                    <div class="d-flex align-items-center gap-3">
                                        <small class="text-muted">{$date}</small>
                                        <span>{$content}</span>
                                    </div>
                                </div>
                                HTML;
                            }

                            echo '</div>';
                        } else {
                            echo '<p>更新情報はありません。</p>';
                        }
                    ?>
                </section>

            </main>
        </div>

        <!-- テーマ切替ボタン -->
        <button id="theme-toggle-btn" class="btn theme-toggle-btn">
            <i id="theme-icon" class="bi <?= $theme === 'dark' ? 'bi-sun' : 'bi-moon' ?>"></i>
        </button>

        <!-- JavaScript -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="./js/theme-toggle_top.js"></script>

        <!-- フッター -->
        <footer>
            <?php include './template/footer.php'; ?>
        </footer>

    </body>
</html>
