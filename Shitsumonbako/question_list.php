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
$GoalsDAO = new GoalsDAO();
$goal_data = $GoalsDAO->getGoalByUserId($member->user_id);

// 入力値保持（POSTの場合）
$goal_value = $_POST['goal'] ?? ($goal_data->goal ?? '');
$milestone_value = $_POST['mile_stone'] ?? ($goal_data->mile_stone ?? '');

// 目標日を YYYY-MM-DD 形式に変換して表示
$goal_date_value = '';
if (!empty($_POST['goal_date'])) {
    $goal_date_value = $_POST['goal_date'];
} elseif (!empty($goal_data->goal_date)) {
    $dateObj = new DateTime($goal_data->goal_date);
    $goal_date_value = $dateObj->format('Y-m-d');
}

// 残り日数計算
$days_left = null;
if ($goal_date_value) {
    $today = new DateTime();
    $target_date = new DateTime($goal_date_value);
    $interval = $today->diff($target_date);
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

    <title>目標登録</title>

    <style>
        .char-count { font-weight: bold; }
        .char-count.exceed { color: red; }
        @media (min-aspect-ratio: 16/9) {
            .goal-form { display: flex; gap: 2rem; }
            .goal-form .form-left { flex: 1; }
            .goal-form .form-right { flex: 1; }
        }
    </style>
</head>

<body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">
    <div class="d-flex w-100 min-vh-100">
        <!-- サイドバー -->
        <div class="d-none d-md-block">
            <?php include 'side.php'; ?>
        </div>

        <!-- メイン -->
        <main class="main-content flex-grow-1 p-4">
            <h1 class="mt-5">目標登録</h1>

            <form method="POST" action="save_goal.php" class="goal-form mt-4">
                <!-- 左側：目標・マイルストーン -->
                <div class="form-left">
                    <div class="mb-3">
                        <label for="goal" class="form-label">目標</label>
                        <textarea id="goal" name="goal" class="form-control" rows="3" maxlength="100"><?= htmlspecialchars($goal_value) ?></textarea>
                        <div class="text-end char-count" id="goal-count">0 / 100</div>
                    </div>

                    <div class="mb-3">
                        <label for="mile_stone" class="form-label">中間目標（マイルストーン）</label>
                        <textarea id="mile_stone" name="mile_stone" class="form-control" rows="3" maxlength="100"><?= htmlspecialchars($milestone_value) ?></textarea>
                        <div class="text-end char-count" id="milestone-count">0 / 100</div>
                    </div>
                </div>

                <!-- 右側：目標日 -->
                <div class="form-right">
                    <label for="goal_date_input" class="form-label">目標日</label>
                    <input type="date" id="goal_date_input" name="goal_date"
                           class="form-control"
                           value="<?= htmlspecialchars($goal_date_value) ?>">
                    <?php if ($days_left !== null): ?>
                        <p class="mt-2">残り日数: <?= $days_left ?>日</p>
                    <?php endif; ?>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">保存</button>
                </div>
            </form>
        </main>
    </div>

    <!-- テーマ切替 -->
    <button id="theme-toggle-btn" class="btn theme-toggle-btn">
        <i id="theme-icon" class="bi <?= $theme === 'dark' ? 'bi-sun' : 'bi-moon' ?>"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // 文字数カウント
        function setupCharCount(id, counterId) {
            const input = document.getElementById(id);
            const counter = document.getElementById(counterId);

            function update() {
                const len = input.value.length;
                counter.textContent = `${len} / 100`;
                counter.classList.toggle('exceed', len > 100);
            }

            input.addEventListener('input', update);
            update(); // 初期表示
        }

        setupCharCount('goal', 'goal-count');
        setupCharCount('mile_stone', 'milestone-count');
    </script>
</body>

<footer>
    <?php include '../template/footer.php'; ?>
</footer>
</html>
