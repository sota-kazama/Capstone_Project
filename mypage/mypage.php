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

// DBからログインユーザーの目標を取得
$GoalsDAO = new GoalsDAO();
$goal_data = $GoalsDAO->getGoalByUserId($member->user_id);
$answers_count = $member->u_answers_count;
$correct_count = $member->u_correct_count;

// 目標日までの日数計算
$days_left = null;
$goal_status = '';
$is_past = false;

if ($goal_data && !empty($goal_data->goal_date)) {
    $today = new DateTime();
    $today->setTime(0, 0, 0);
    
    $target_date = new DateTime($goal_data->goal_date);
    $target_date->setTime(0, 0, 0);
    
    $interval = $today->diff($target_date);
    
    if ($target_date < $today) {
        $goal_status = 'お疲れさまでした！';
        $is_past = true;
    } elseif ($target_date == $today) {
        $goal_status = '最終日！';
    } else {
        $days_left = $interval->days;
        $goal_status = 'あと' . $days_left . '日！';
    }

    // カレンダー用
    $year  = (int)$target_date->format('Y');
    $month = (int)$target_date->format('m');

    $firstDay = new DateTime("$year-$month-01");
    $startWeekDay = (int)$firstDay->format('w');
    $daysInMonth  = (int)$firstDay->format('t');
}

// テーマ
$theme = $_COOKIE['theme'] ?? 'light';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link href="../css/BaseDesignData.css" rel="stylesheet" />
    <link href="../css/side.css" rel="stylesheet" />
    <?php include '../template/header.php'; ?>

    <title>マイページ</title>
    <link id="theme-css" rel="stylesheet" href="../css_theme/<?= htmlspecialchars($theme) ?>.css" />
    <link href="../css_theme/toggle-button.css" rel="stylesheet" />

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="d-flex w-100 min-vh-100">
    <div class="d-none d-md-block">
        <?php include 'side.php'; ?>
    </div>

    <main class="main-content container mt-4">
        <h1 class="mt-5">マイページ</h1>

        <div class="mb-4">
            <h2>あなたの目標</h2>
            <?php if ($goal_data && !empty($goal_data->goal)): ?>
                <div class="d-flex align-items-baseline gap-2">
                    <p class="fs-4 mb-0"><?= htmlspecialchars($goal_data->goal) ?></p>
                    <?php if ($is_past): ?>
                        <a href="result_record.php" class="btn btn-primary btn-sm">結果を記録する</a>
                    <?php else: ?>
                        <a href="goal.php" class="btn btn-outline-primary btn-sm">目標を修正・変更する</a>
                    <?php endif; ?>
                    <?php if ($goal_status): ?>
                        <span class="badge bg-danger fs-6"><?= $goal_status ?></span>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <p class="text-muted">
                    目標を立ててみましょう！
                    <a href="goal.php" class="btn btn-outline-primary btn-sm">目標を設定する</a>
                </p>
            <?php endif; ?>
        </div>

        <!-- 成績表（円グラフ） -->
        <?php if ($answers_count > 0): ?>
    <h2>成績表</h2>
    <p>回答数：<?= htmlspecialchars($answers_count, ENT_QUOTES, 'UTF-8') ?> 問</p>
    <p>正解数：<?= htmlspecialchars($correct_count, ENT_QUOTES, 'UTF-8') ?> 問</p>

    <canvas id="scoreChart" width="300" height="300"></canvas>

    <script>
        const answered = <?= (int)$answers_count ?>;
        const correct = <?= (int)$correct_count ?>;
        const incorrect = answered - correct;

        const ctx = document.getElementById('scoreChart').getContext('2d');

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['正解', '不正解'],
                datasets: [{
                    data: [correct, incorrect],
                    backgroundColor: ['#dc3545', '#adb5bd']
                }]
            },
            options: {
                cutout: '65%',
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    </script>
<?php else: ?>
    <p>回答履歴がありません。問題を解いて成績を記録しましょう！</p>
<?php endif; ?>

        <!-- 途中から回答 -->
        <?php if(!empty($member->question_hold)): ?>
            <h2>test文字列</h2>
            <a href="../question.php" class="btn btn-outline-primary btn-sm">問題ページへ</a>
        <?php endif; ?>

        <!-- 目標日カレンダー -->
        <?php if(!empty($goal_data->goal_date)): ?>
            <h2>目標日</h2>
            <h4 class="text-center mb-3"><?= $year ?>年 <?= $month ?>月</h4>
            <table class="table table-bordered text-center">
                <thead class="table-light">
                    <tr>
                        <th class="text-danger">日</th>
                        <th>月</th>
                        <th>火</th>
                        <th>水</th>
                        <th>木</th>
                        <th>金</th>
                        <th class="text-primary">土</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <?php
                        for ($i = 0; $i < $startWeekDay; $i++) echo '<td></td>';

                        for ($day = 1; $day <= $daysInMonth; $day++) {
                            $currentDate = sprintf('%04d-%02d-%02d', $year, $month, $day);
                            if ($currentDate === $goal_data->goal_date) {
                                echo '<td class="bg-danger text-white fw-bold">' . $day . '</td>';
                            } else {
                                echo '<td>' . $day . '</td>';
                            }
                            if ( ($day + $startWeekDay) % 7 === 0 ) echo '</tr><tr>';
                        }
                        ?>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>

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
