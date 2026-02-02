<?php
require_once '../helpers/MemberDAO.php';
require_once '../helpers/ProblemDAO.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$member = $_SESSION['member'] ?? null;
$area_number = $_SESSION['area_number'] ?? null;

/* 分野未選択なら戻す */
if ($area_number === null) {
    header('Location: category_select.php');
    exit;
}

$daoProblem = new ProblemDAO();

// 問題一覧取得
$questions = $daoProblem->getQuestionsByArea($area_number);
$totalCount = count($questions);

// ★ 問題が存在しない場合は元に戻す
if ($totalCount === 0) {
    $_SESSION['error_message'] = 'この分野の問題は現在登録されていません';
    unset($_SESSION['area_number']);
    unset($_SESSION['problemArray']);
    header('Location: category_select.php');
    exit;
}

// ログインユーザー用成績
$answered = 0;
$correct  = 0;

if ($member) {
    $answered = $member->u_answers_count ?? 0;
    $correct  = $member->u_correct_count ?? 0;
}

$accuracy = ($answered > 0) ? round(($correct / $answered) * 100, 1) : 0.0;

// テーマ
$theme = $_SESSION['theme'] ?? 'light';

// セッション初期化（結果表示後）
unset($_SESSION['problemArray']);
unset($_SESSION['area_number']);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<title>結果</title>
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

</head>

<body class="<?= $theme==='dark'?'dark-mode':'light-mode' ?>">
<?php include '../template/header.php'; ?>

<!-- ★ テーマ切替ボタン -->
<button id="theme-toggle-btn" class="btn btn-primary theme-toggle-btn">
    <i id="theme-icon" class="bi <?= $theme==='dark' ? 'bi-sun' : 'bi-moon' ?>"></i>
</button>

<div class="d-flex w-100 min-vh-100">
    <!-- サイドバー -->
    <div class="d-none d-md-block">
        <?php include '../template/side.php'; ?>
    </div>

    <!-- メイン -->
    <main class="main-content p-4 flex-grow-1">
        <h1 class="mb-4">結果</h1>

        <div class="card mb-4">
            <div class="card-body text-center">
                <h3 class="mb-3">今回の成績</h3>
                <p class="fs-5">出題数：<?= $totalCount ?> 問</p>

                <?php if ($member): ?>
                    <p class="fs-5">解答数：<?= $answered ?> 問</p>
                    <p class="fs-5">正解数：<?= $correct ?> 問</p>
                    <p class="fs-4 fw-bold">正答率：<?= $accuracy ?> %</p>
                <?php else: ?>
                    <p class="text-muted">※ ログインすると成績が記録されます</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- 問題別リスト -->
        <div class="mb-4">
            <h3 class="mb-3">問題一覧</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width:10%">No.</th>
                        <th>内容</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($questions as $index => $q): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($q->q_content) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center gap-3">
            <a href="category_select.php" class="btn btn-outline-primary">分野選択へ戻る</a>
            <a href="top.php" class="btn btn-outline-secondary">トップへ</a>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/theme-toggle.js"></script>

<footer class="mt-4">
<?php include '../template/footer.php'; ?>
</footer>
</body>
</html>
