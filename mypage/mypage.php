<?php
require_once '../helpers/MemberDAO.php';
require_once '../helpers/u_goalsDAO.php';

session_start();

/* =========================
   認証チェック
========================= */
if (!isset($_SESSION['member'])) {
    header('Location: ../auth/login.php');
    exit;
}

$member = $_SESSION['member'];

/* =========================
   データ取得
========================= */
$goalsDAO   = new GoalsDAO();
$goal_data  = $goalsDAO->getLatestGoalByUserId($member->user_id);

$answersCnt = (int)$member->u_answers_count;
$correctCnt = (int)$member->u_correct_count;

/* =========================
   目標日関連
========================= */
$goal_status = '';
$days_left   = null;
$is_past     = false;

if (!empty($goal_data->goal_date)) {

    $today  = (new DateTime())->setTime(0, 0, 0);
    $target = (new DateTime($goal_data->goal_date))->setTime(0, 0, 0);

    if ($today > $target) {
        $goal_status = 'お疲れさまでした！';
        $is_past = true;
    } elseif ($today == $target) {
        $goal_status = '最終日！';
    } else {
        $days_left   = $today->diff($target)->days;
        $goal_status = "あと{$days_left}日！";
    }

    // カレンダー用
    $year  = (int)$target->format('Y');
    $month = (int)$target->format('m');

    $firstDay     = new DateTime("{$year}-{$month}-01");
    $startWeekDay = (int)$firstDay->format('w');
    $daysInMonth  = (int)$firstDay->format('t');
}

/* =========================
   マイルストーン
========================= */
$milestones = [];

if ($goal_data) {
    for ($i = 1; $i <= 5; $i++) {
        $prop = ($i === 1) ? 'mile_stone' : "mile_stone{$i}";
        if (!empty($goal_data->$prop)) {
            $milestones[] = $goal_data->$prop;
        }
    }
}

/* =========================
   テーマ
========================= */
$theme = $_COOKIE['theme'] ?? 'light';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- CSS -->
    <link href="../css/BaseDesignData.css" rel="stylesheet">
    <link href="../css/side.css" rel="stylesheet">
    <link id="theme-css" href="../css_theme/<?= htmlspecialchars($theme) ?>.css" rel="stylesheet">
    <link href="../css_theme/toggle-button.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <title>マイページ</title>
</head>

<body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">

<?php include '../template/header.php'; ?>

<div class="d-flex w-100 min-vh-100">

    <!-- サイドバー -->
    <div class="d-none d-md-block">
        <?php include 'side.php'; ?>
    </div>

    <!-- メイン -->
    <main class="main-content flex-grow-1 p-4">

        <h1 class="mb-4">マイページ</h1>

        <!-- ===== 目標 ===== -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header fw-bold">あなたの目標</div>
                    <div class="card-body">

                        <?php if ($goal_data && !empty($goal_data->goal)): ?>

                            <?php 
                                // 結果登録済みかどうかの判定
                                $is_result_registered = isset($goal_data->is_achieved) && $goal_data->is_achieved !== null;
                            ?>
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <p class="fs-5 mb-0">
                                    <?= htmlspecialchars($goal_data->goal) ?>
                                </p>

                                <?php if ($is_result_registered): ?>
                                    <?php if ($goal_data->is_achieved == 1): ?>
                                        <span class="badge bg-success fs-6"><i class="bi bi-trophy-fill me-1"></i>達成</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary fs-6">未達成</span>
                                    <?php endif; ?>
                                <?php elseif ($goal_status): ?>
                                    <span class="badge bg-danger fs-6">
                                        <?= $goal_status ?>
                                    </span>
                                <?php endif; ?>
                                <?php if ($goal_status): ?>
                                    <span class="badge bg-danger fs-6">
                                        <?= $goal_status ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="mt-3">
                                 <?php if ($goal_data->is_achieved == 0): ?>
                                        <?php if ($is_past): ?>
                                    <a href="results.php" class="btn btn-primary btn-sm">
                                        結果を記録する
                                    </a>
                                <?php else: ?>
                                    <a href="goal.php" class="btn btn-outline-primary btn-sm">
                                        目標を修正・変更する
                                    </a>
                                <?php endif; ?>

                                <?php else: ?>
                                    <a href="goal.php" class="btn btn-outline-primary btn-sm">
                                        新しく目標を設定する
                                    </a>
                                <?php endif; ?>
                            </div>

                        <?php else: ?>
                            <p class="text-muted mb-3">まだ目標が設定されていません。</p>
                            <a href="goal.php" class="btn btn-outline-primary btn-sm">
                                目標を設定する
                            </a>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>

           <!-- ===== 中段 ===== -->
        <div class="row g-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header fw-bold">マイルストーン達成状況</div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            <?php if (!empty($milestones)): ?>
                                <?php foreach ($milestones as $index => $stone):
                                    // 1. DBの達成フラグ用プロパティ名を生成
                                    // テーブル設計が ms1_status, ms2_status... の場合を想定
                                    $num = $index + 1;
                                    $flag_prop = "ms{$num}_status";

                                    // 2. 達成判定 (1なら達成)
                                    $is_achieved = (isset($goal_data->$flag_prop) && $goal_data->$flag_prop == 1);
                                    // 3. 背景スタイルの決定
                                    if ($is_achieved) {
                                        // 達成済み：サクラ背景
                                        $bg_style = "background-image: url('../images/sakura.png');
                                                    background-size: cover;
                                                    background-position: center;
                                                    border: none;
                                                    color: #d63384;
                                                    font-weight: bold;
                                                    text-shadow: 1px 1px 2px rgba(255,255,255,0.8);";
                                    } else {
                                        // 未達成：デフォルト（薄いグレー）
                                        $bg_style = "background-color: #f8f9fa; color: #6c757d;";
                                    }
                                ?>
                                    <div class="border rounded d-flex align-items-center justify-content-center p-2 text-center milestone-box"
                                        style="width: 100%; max-width: 80px; aspect-ratio: 1/1; cursor: default; <?= $bg_style ?>"
                                        title="<?= htmlspecialchars($stone) ?>">
                                        <span style="font-size: 0.7rem;
                                                    line-height: 1.2;
                                                    overflow: hidden;
                                                    display: -webkit-box;
                                                    -webkit-box-orient: vertical;
                                                    -webkit-line-clamp: 3;">
                                            <?= htmlspecialchars($stone) ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted mb-0">設定されたマイルストーンはありません。</p>
                            <?php endif; ?>

                        </div>
                            <?php if (!empty($milestones)): ?>
                                <div class="ms-auto">

                                    <?php if ($goal_data->is_achieved == 0): ?>
                                        <a href="results.php" class="btn btn-success btn-sm d-flex align-items-center gap-1">
                                            <i class="bi bi-pencil-square"></i> 成果を記録する
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>



        <!-- ===== 下段 ===== -->
        <div class="row g-4">

            <!-- 左 -->
            <div class="col-12 col-md-6">
                <div class="card mb-4 shadow-sm">
                    <div class="card-header fw-bold">成績表</div>
                    <div class="card-body">

                        <?php if ($answersCnt > 0): ?>
                            <p>回答数：<?= $answersCnt ?> 問</p>
                            <p>正解数：<?= $correctCnt ?> 問</p>

                            <div class="position-relative mx-auto" style="max-width:400px;height:300px;">
                                <canvas id="scoreChart"></canvas>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info mb-0">
                                回答履歴がありません。
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

                <?php if (!empty($member->question_hold)): ?>
                    <div class="card shadow-sm border-warning">
                        <div class="card-header fw-bold text-warning">途中から再開</div>
                        <div class="card-body">
                            <p class="mb-3">未完了の問題があります。</p>
                            <a href="../question.php" class="btn btn-outline-warning btn-sm">
                                問題ページへ
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- 右 -->
            <div class="col-12 col-md-6">
                <?php if (!empty($goal_data->goal_date)): ?>
                    <div class="card shadow-sm h-100">
                        <div class="card-header fw-bold">目標日カレンダー</div>
                        <div class="card-body">

                            <h5 class="text-center mb-3">
                                <?= $year ?>年 <?= $month ?>月
                            </h5>

                            <table class="table table-bordered text-center mb-0">
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
                                        $todayStr = (new DateTime())->format('Y-m-d');

                                        for ($i = 0; $i < $startWeekDay; $i++) {
                                            echo '<td></td>';
                                        }

                                        for ($day = 1; $day <= $daysInMonth; $day++) {
                                            $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
                                            $class = '';

                                            if ($date === $goal_data->goal_date) {
                                                $class = 'bg-danger text-white fw-bold';
                                            } elseif ($date === $todayStr) {
                                                $class = 'bg-primary text-white fw-bold';
                                            }

                                            echo "<td class='{$class}'>{$day}</td>";

                                            if (($day + $startWeekDay) % 7 === 0) {
                                                echo '</tr><tr>';
                                            }
                                        }
                                        ?>
                                    </tr>
                                </tbody>
                            </table>

                            青マス：今日の日付<br>
                            赤マス：目標日
                        </div>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </main>
</div>

<!-- テーマ切替 -->
<button id="theme-toggle-btn" class="btn btn-primary theme-toggle-btn">
    <i id="theme-icon" class="bi <?= $theme === 'dark' ? 'bi-sun' : 'bi-moon' ?>"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/theme-toggle.js"></script>

<?php if ($answersCnt > 0): ?>
<script>
const answered  = <?= $answersCnt ?>;
const correct   = <?= $correctCnt ?>;
const incorrect = answered - correct;

new Chart(document.getElementById('scoreChart'), {
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
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});
</script>
<?php endif; ?>

<footer>
    <?php include '../template/footer.php'; ?>
</footer>

</body>
</html>
