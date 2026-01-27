<?php
require_once '../helpers/MemberDAO.php';
require_once '../helpers/u_goalsDAO.php';


//セッションを開始する
session_start();

// 未ログインの場合
if (!isset($_SESSION['member'])) {
    header('Location: ../auth/login.php');
    exit;
}

//ログイン中の会員データを取得
$member = $_SESSION['member'];

// テーマ
$theme = $_COOKIE['theme'] ?? 'light';
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />

        <!-- Bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
            rel="stylesheet"
        />

        <!-- CSS -->
        <link href="../css/BaseDesignData.css" rel="stylesheet" />
        <link href="../css/side.css" rel="stylesheet" />
        <link id="theme-css" href="../css_theme/<?= htmlspecialchars($theme) ?>.css" rel="stylesheet" />
        <link href="../css_theme/toggle-button.css" rel="stylesheet" />

        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <title></title>
    </head>

    <body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">
    <?php include '../template/header.php'; ?>

    <div class="d-flex w-100 min-vh-100">
        <div class="d-none d-md-block">
            <?php include 'side.php'; ?>
        </div> <main class="main-content flex-grow-1 p-4">
            <h1 class="mb-4">成果登録</h1>
            
            </main>
    </div>

        <!-- テーマ切替 -->
        <button id="theme-toggle-btn" class="btn btn-primary theme-toggle-btn">
            <i id="theme-icon" class="bi <?= $theme === 'dark' ? 'bi-sun' : 'bi-moon' ?>"></i>
        </button>

        <!-- JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="../js/theme-toggle.js"></script>

        <!-- フッター -->
        <footer>
            <?php include '../template/footer.php'; ?>
        </footer>
    </body>
</html>
