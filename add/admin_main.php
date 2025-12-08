<?php
require_once '../helpers/MemberDAO.php';

session_start();

// 未ログインの場合
if (!isset($_SESSION['member'])) {
    header('Location: login.php');
    exit;
}

$member = $_SESSION['member'];
$theme = $_COOKIE['theme'] ?? 'light';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />

    <!-- 共通CSS -->
    <link href="../css/BaseDesignData.css" rel="stylesheet" />
    <link href="../css/side.css" rel="stylesheet" />

    <!-- テーマCSS -->
    <link id="theme-css" href="../css_theme/<?= $theme ?>.css" rel="stylesheet" />

    <!-- トグルボタンCSS -->
    <link href="../css_theme/toggle-button.css" rel="stylesheet" />

    <title>管理者ページ</title>
</head>

<body>

    <!-- ここに移動 -->
    <?php include '../template/header2.php'; ?>

    <div class="d-flex w-100 min-vh-100">

        <!-- サイドバー -->
        <?php include 'side.php'; ?>

        <main class="main-content">
            <h1>管理者ページトップ</h1>
            <h3>サイドバーより項目を選択して管理してください</h3>
        </main>
    </div>

    <!-- テーマ切替ボタン -->
    <button id="theme-toggle-btn" class="btn theme-toggle-btn">
        <i id="theme-icon" class="bi <?= $theme === 'dark' ? 'bi-sun' : 'bi-moon' ?>"></i>
    </button>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/theme-toggle.js"></script>

    <footer>
        <?php include '../template/footer.php'; ?>
    </footer>

</body>
</html>
