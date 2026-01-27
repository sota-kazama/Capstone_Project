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
$goalsDAO  = new GoalsDAO();
$goal_data = $goalsDAO->getLatestGoalByUserId($member->user_id);
$answers_count = $member->u_answers_count;
$correct_count = $member->u_correct_count;

// 目標日までの日数計算
$days_left  = null;
$goal_status = '';
$is_past     = false;

if ($goal_data && !empty($goal_data->goal_date)) {
    $today = new DateTime();
    $today->setTime(0, 0, 0);

    $target_date = new DateTime($goal_data->goal_date);
    $target_date->setTime(0, 0, 0);

    // 比較演算子で条件分岐を整理
    if ($today > $target_date) {
        // 今日が目標日より後の場合
        $goal_status = 'お疲れさまでした！';
        $is_past = true;
    } elseif ($today == $target_date) {
        // 今日が目標日の場合
        $goal_status = '最終日！';
    } else {
        // 今日が目標日より前の場合
        $interval = $today->diff($target_date);
        $days_left   = $interval->days;
        $goal_status = 'あと' . $days_left . '日！';
    }

    // カレンダー用
    $year  = (int)$target_date->format('Y');
    $month = (int)$target_date->format('m');

    $firstDay     = new DateTime("$year-$month-01");
    $startWeekDay = (int)$firstDay->format('w');
    $daysInMonth  = (int)$firstDay->format('t');
}

// 目標データがある場合、マイルストーンを配列にまとめる
$milestones = [];
if ($goal_data) {
    for ($i = 1; $i <= 5; $i++) {
        $prop = ($i === 1) ? 'mile_stone' : "mile_stone$i";
        if (!empty($goal_data->$prop)) {
            $milestones[] = $goal_data->$prop;
        }
    }
}

// テーマ
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

<!-- ヘッダー -->
<?php include '../template/header.php'; ?>

<div class="d-flex w-100 min-vh-100">

    <!-- サイドバー -->
    <div class="d-none d-md-block">
        <?php include 'side.php'; ?>
    </div>

    <!-- メインコンテンツ -->
    <main class="main-content flex-grow-1 p-4">

        <h1 class="mb-4">マイページ</h1>

        <!-- ===== 目標 ===== -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header fw-bold">
                        あなたの目標
                    </div>
                    <div class="card-body">

                        <?php if ($goal_data && !empty($goal_data->goal)): ?>
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <p class="fs-5 mb-0">
                                    <?= htmlspecialchars($goal_data->goal) ?>
                                </p>

                                <?php if ($goal_status): ?>
                                    <span class="badge bg-danger fs-6">
                                        <?= $goal_status ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="mt-3">
                                <?php if ($is_past): ?>
                                    <a href="results.php" class="btn btn-primary btn-sm">
                                        結果を記録する
                                    </a>
                                <?php else: ?>
                                    <a href="goal.php" class="btn btn-outline-primary btn-sm">
                                        目標を修正・変更する
                                    </a>
                                <?php endif; ?>
                            </div>

                        <?php else: ?>
                            <p class="text-muted mb-3">
                                まだ目標が設定されていません。
                            </p>
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
            <div class="card-header fw-bold">
                マイルストーン達成状況
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <?php if (!empty($milestones)): ?>
                        <?php foreach ($milestones as $index => $stone): 
                            // --- 達成判定ロジック（ここを環境に合わせて調整してください） ---
                            // 例：1つ目のマスは回答10以上、2つ目は20以上...で達成とする場合
                            $threshold = ($index + 1) * 10; 
                            $is_achieved = ($answers_count >= $threshold);
                            
                            // もしDBに「どこまで達成したか」の数値（1〜5）があるなら：
                            // $is_achieved = ($goal_data->achieved_level > $index);
                            // -------------------------------------------------------
                            
                            // 背景スタイルの決定
                            $bg_style = $is_achieved 
                                ? "background-image: url('../images/sakura.png'); background-size: cover; background-position: center; border: none;" 
                                : "background-color: #f8f9fa;";
                            
                            // 達成時のテキスト色調整（画像で見えにくい場合）
                            $text_color = $is_achieved ? "color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.7);" : "";
                        ?>
                            <div class="border rounded d-flex align-items-center justify-content-center p-2 text-center milestone-box" 
                                style="width: 100%; max-width: 80px; aspect-ratio: 1/1; cursor: default; <?= $bg_style ?>"
                                title="<?= htmlspecialchars($stone) ?>">
                                
                                <span style="font-size: 0.7rem; 
                                            line-height: 1.2; 
                                            overflow: hidden; 
                                            display: -webkit-box; 
                                            -webkit-box-orient: vertical; 
                                            -webkit-line-clamp: 3;
                                            <?= $text_color ?>"> 
                                    <?= htmlspecialchars($stone) ?>
                                </span>
                                
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted mb-0">設定されたマイルストーンはありません。</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

        <!-- ===== 下段 ===== -->
        <div class="row g-4">

            <!-- 左：成績 -->
            <div class="col-12 col-md-6">

                <div class="card mb-4 shadow-sm">
                    <div class="card-header fw-bold">
                        成績表
                    </div>
                    <div class="card-body">

                        <?php if ($answers_count > 0): ?>
                            <p>回答数：<?= (int)$answers_count ?> 問</p>
                            <p>正解数：<?= (int)$correct_count ?> 問</p>

                            <div class="position-relative mx-auto" style="max-width: 400px; height: 300px;">
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
                        <div class="card-header fw-bold text-warning">
                            途中から再開
                        </div>
                        <div class="card-body">
                            <p class="mb-3">
                                未完了の問題があります。
                            </p>
                            <a href="../question.php" class="btn btn-outline-warning btn-sm">
                                問題ページへ
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <!-- 右：カレンダー -->
            <div class="col-12 col-md-6">

                <?php if (!empty($goal_data->goal_date)): ?>
                    <div class="card shadow-sm h-100">
                        <div class="card-header fw-bold">
                            目標日カレンダー
                        </div>
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
                                        // 1. 今日の日付を取得（フォーマットを合わせる）
                                        $todayStr = (new DateTime())->format('Y-m-d');

                                        for ($i = 0; $i < $startWeekDay; $i++) {
                                            echo '<td></td>';
                                        }

                                        for ($day = 1; $day <= $daysInMonth; $day++) {
                                            $currentDate = sprintf('%04d-%02d-%02d', $year, $month, $day);

                                            // クラスの判定
                                            $class = '';
                                            if ($currentDate === $goal_data->goal_date) {
                                                // 目標日の場合（赤）
                                                $class = 'bg-danger text-white fw-bold';
                                            } elseif ($currentDate === $todayStr) {
                                                // 今日の場合（青：Bootstrapのbg-infoやbg-primaryを使用）
                                                $class = 'bg-primary text-white fw-bold';
                                            }

                                            echo '<td class="' . $class . '">' . $day . '</td>';

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

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/theme-toggle.js"></script>

<?php if ($answers_count > 0): ?>
<script>
    const answered  = <?= (int)$answers_count ?>;
    const correct   = <?= (int)$correct_count ?>;
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
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
<?php endif; ?>

<!-- フッター -->
<footer>
    <?php include '../template/footer.php'; ?>
</footer>

</body>
</html>
