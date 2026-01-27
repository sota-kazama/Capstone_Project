<?php
require_once '../helpers/MemberDAO.php';
// 必要に応じて他のDAO (QuestionDAO, ShikakuDAO, FieldDAO) を読み込む

session_start();

// 未ログインの場合
if (!isset($_SESSION['member'])) {
    header('Location: ../auth/login.php');
    exit;
}

$member = $_SESSION['member'];
// Cookieからテーマ（存在しなければ light）
$theme = $_COOKIE['theme'] ?? 'light';

// ★ 仮の統計データ (管理者トップ向け)
$stats = [
    'total_questions' => 1250,
    'total_members' => 85,
    'total_shikaku' => 5,
];

$message = ""; // メッセージ変数を初期化
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <title>管理者トップ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="../css_theme/base.css" rel="stylesheet">
    <link href="../css_theme/side.css" rel="stylesheet">
    <link
        id="theme-css"
        href="../css_theme/<?= htmlspecialchars($theme) ?>.css"
        rel="stylesheet"
    >
    <link href="../css_theme/toggle-button.css" rel="stylesheet">
</head>

<body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">
<?php include '../template/header2.php'; ?>

<div class="d-flex w-100 min-vh-100">
    <?php include 'side.php'; ?>

    <main class="main-content flex-grow-1 p-4">
        <div class="d-flex align-items-center mb-3">
            <h1 class="m-0">管理者トップ</h1>
        </div>

        <div class="card p-4 mt-3">
            <h4>システム情報</h4>
            <p class="mb-0">
                管理トップページです。<br />
                サイドバーから各管理機能に移動してください。
            </p>
        </div>
    </main>
</div>

<button id="theme-toggle-btn" class="btn btn-primary theme-toggle-btn">
    <i id="theme-icon" class="bi <?= $theme === 'dark' ? 'bi-sun' : 'bi-moon' ?>"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/theme-toggle.js"></script>
</body>

<footer>
<?php include '../template/footer.php'; ?>
</footer>
</html>
