<?php
require_once '../helpers/MemberDAO.php';

session_start();

// 未ログインの場合
if (!isset($_SESSION['member'])) {
    header('Location: login.php');
    exit;
}

$member = $_SESSION['member'];

// テーマ（Cookie なければ light）
$theme = $_COOKIE['theme'] ?? 'light';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <!-- こっちのheadは変更しない -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    />
    <link href="../css/BaseDesignData.css" rel="stylesheet" />
    <link href="../css/side.css" rel="stylesheet" />
    <?php include '../template/header.php'; ?>
</head>

<head>
    <!-- こっちのheadを変更する -->
    <title>マイページ</title>
    <link id="theme-css" rel="stylesheet" href="../css_theme/<?= htmlspecialchars($theme) ?>.css" />
    <link href="../css_theme/toggle-button.css" rel="stylesheet" />
</head>

<body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">

<div class="d-flex w-100 min-vh-100">
    <div class="d-none d-md-block">
        <?php include 'side.php'; ?>
    </div>

    <main class="main-content container mt-4">
        <h1 class="mt-5">マイページ</h1>

        <!-- 正誤表 -->
        <!-- 途中から回答する -->
        <!-- 目標日 -->
    </main>
</div>

<button id="theme-toggle-btn" class="btn theme-toggle-btn">
    <i id="theme-icon" class="bi <?= $theme === 'dark' ? 'bi-sun' : 'bi-moon' ?>"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/theme-toggle.js"></script>

</body>

<footer>
<?php include '../template/footer.php'; ?>
</footer>
</html>
