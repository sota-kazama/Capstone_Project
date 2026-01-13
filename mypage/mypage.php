<?php
require_once '../helpers/MemberDAO.php';
require_once '../helpers/u_goalsDAO.php';

session_start();


// 未ログインの場合
if (!isset($_SESSION['member'])) {
    header('Location: login.php');
    exit;
}

$member = $_SESSION['member'];

//DBからログインユーザーの目標を取得
$GoalsDAO = new GoalsDAO();
$goal_data = $GoalsDAO->getGoalByUserId($member->user_id);

// 目標日までの日数計算
// $goal_date = null; //初期値
// if ($goal_data === null) {
//     $goal_date = '目標が設定されていません';
// } else {
//     $today = new DateTime();
//     $goal_date_obj = new DateTime($goal_data->goal_date);
//     $interval = $today->diff($goal_date_obj);
//     $goal_date = $interval->days;
//     if ($today > $goal_date_obj) {
//         $goal_date = 0; // 目標日を過ぎている場合
//     }
// }

$days_left = null;
if ($goal_data && !empty($goal_data->goal_date)) {
    // 残り日数の計算
    $today = new DateTime();
    $target_date = new DateTime($goal_data->goal_date);
    $interval = $today->diff($target_date);
    // 日付が過ぎている場合は0、そうでなければ日数を取得
    $days_left = $target_date > $today ? $interval->days : 0;
}

// テーマ（Cookie なければ light）
$theme = $_COOKIE['theme'] ?? 'light';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link href="../css/BaseDesignData.css" rel="stylesheet" />
    <link href="../css/side.css" rel="stylesheet" />
    <link id="theme-css" rel="stylesheet" href="../css_theme/<?= htmlspecialchars($theme) ?>.css" />
    <link href="../css_theme/toggle-button.css" rel="stylesheet" />

    <title>マイページ</title>

    <?php include '../template/header.php'; ?>
</head>

<body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">
    <div class="d-flex w-100 min-vh-100">

        <!-- サイドバー -->
        <div class="d-none d-md-block">
            <?php include 'side.php'; ?>
        </div>

        <!-- メイン -->
        <main class="main-content flex-grow-1 p-4">
            <h1 class="mt-5">マイページ</h1>

            <!-- 目標日表示 -->
            <div class="card p-4 mt-3">
                <h4>目標情報</h4>
                <?php if ($goal_data): ?>
                    <p>目標日: <?= htmlspecialchars($goal_data->goal_date) ?></p>
                    <p>残り日数: <?= $days_left ?>日</p>
                <?php else: ?>
                    <p class="text-muted">目標はまだ設定されていません。</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p class="text-muted">目標を立ててみましょう！
            <a href="goal.php" class="btn btn-outline-primary btn-sm">目標を設定する</a></p>
        <?php endif; ?>
    </div>
        <h2>目標達成状況</h2><br>
        <!-- マイルストーンを表示、達成なら赤マス、未達成なら白マス -->
        <h2>成績表</h2>
        <!-- 正誤表 -->
        <!-- 途中から回答する -->
        <!-- 目標日 -->
    </main>
</div>

    <!-- テーマ切替 -->
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