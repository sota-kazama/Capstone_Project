<<<<<<< HEAD
<?php
require_once './helpers/updateDAO.php';

// PHPでテーマを取得（Cookie が無ければ light）
$theme = $_COOKIE['theme'] ?? 'light';
?>



=======
>>>>>>> ff9a182911c6d247bc4c0c34dba4b33712622196
<!DOCTYPE html>
<html>
    <head>
<<<<<<< HEAD
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
=======
        <!--こっちのheadは変更しない-->
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
        <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        />
        <link href="css/BaseDesignData.css" rel="stylesheet" />
        <link href="css/side.css" rel="stylesheet" />
        <?php include 'template/header.php'; ?>
    </head>

    <head>
        <!--こっちのheadを変更しない-->
        <title>メインページ</title>
    </head>

    <body>
        <div class="d-flex w-100 min-vh-100">
            <?php include 'template/side.php';?>
>>>>>>> ff9a182911c6d247bc4c0c34dba4b33712622196

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

<<<<<<< HEAD
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
=======
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    <footer>
        <?php include 'template/footer.php'; ?>
    </footer>
>>>>>>> ff9a182911c6d247bc4c0c34dba4b33712622196
</html>
