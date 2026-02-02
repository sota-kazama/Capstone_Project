<?php
require_once '../helpers/MemberDAO.php';
require_once '../helpers/ProblemDAO.php';
require_once '../helpers/QuestionDAO.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$member = $_SESSION['member'] ?? null;
$area_number = $_SESSION['area_number'] ?? null;

/* =========================
   分野未選択なら戻す
========================= */
if ($area_number === null) {
    header('Location: category_select.php');
    exit;
}

$daoProblem = new ProblemDAO();
$questions = $daoProblem->getQuestionsByArea($area_number);

// ★ 問題が1問もない場合
if (empty($questions)) {
    $_SESSION['error_message'] = 'この分野には問題がありません。';
    unset($_SESSION['area_number']);
    unset($_SESSION['problemArray']);
    header('Location: category_select.php');
    exit;
}

// URLパラメータから「何問目か」を取得
$i = isset($_GET['i']) ? intval($_GET['i']) : 0;

// ★ 指定された問題番号が不正な場合
if (!isset($questions[$i])) {
    $_SESSION['error_message'] = '指定された問題が存在しません。';
    header('Location: category_select.php');
    exit;
}

// 今回の問題
$question = $questions[$i];

// 選択肢配列
$choices = [
    1 => $question->choices1,
    2 => $question->choices2,
    3 => $question->choices3,
    4 => $question->choices4,
];

// ラベル
$labels = [1 => 'A', 2 => 'B', 3 => 'C', 4 => 'D'];

// =========================
// テーマ（Cookie優先、未設定ならlight）
$theme = $_COOKIE['theme'] ?? 'light';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>問題回答</title>

<!-- Bootstrap & Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<!-- カスタムCSS -->
<link href="../css/BaseDesignData.css" rel="stylesheet">
<link href="../css/side.css" rel="stylesheet">
<link href="../css_theme/toggle-button.css" rel="stylesheet">

<!-- ★ テーマCSS -->
<link id="theme-css" href="../css_theme/<?= htmlspecialchars($theme) ?>.css" rel="stylesheet">
</head>

<body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">

<?php include '../template/header.php'; ?>

<!-- ★ テーマ切替ボタン -->
<button id="theme-toggle-btn" class="btn btn-primary theme-toggle-btn">
    <i id="theme-icon" class="bi <?= $theme === 'dark' ? 'bi-sun' : 'bi-moon' ?>"></i>
</button>

<div class="d-flex w-100 min-vh-100">
    <!-- サイドバー -->
    <div class="d-none d-md-block">
        <?php include '../template/side.php'; ?>
    </div>

    <!-- メインコンテンツ -->
    <main class="main-content p-4 flex-grow-1">
        <h1>問題回答</h1>

        <!-- 配列順で表示 -->
        <h2>第<?= $i + 1 ?>問（全<?= count($questions) ?>問）</h2>
        <h3><?= htmlspecialchars($question->q_content) ?></h3>

        <?php if (!empty($question->image_path)): ?>
            <img src="../uploads/<?= htmlspecialchars($question->image_path) ?>" class="img-fluid mb-3">
        <?php endif; ?>

        <form action="problem_answer.php?i=<?= $i ?>" method="post">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:10%">選択</th>
                        <th>内容</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($choices as $k => $v): ?>
                    <tr>
                        <td>
                            <button type="submit"
                                    name="answer"
                                    value="<?= $k ?>"
                                    class="btn btn-outline-primary btn-sm">
                                <?= $labels[$k] ?>
                            </button>
                        </td>
                        <td><?= htmlspecialchars($v) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </form>

        <!-- 次の問題 / 結果 -->
        <div class="d-flex justify-content-center mt-3">
            <?php if (isset($questions[$i + 1])): ?>
                <a href="problem_response.php?i=<?= $i + 1 ?>"
                   class="btn btn-outline-primary w-25">
                    次の問題へ
                </a>
            <?php else: ?>
                <a href="problem_result.php"
                   class="btn btn-outline-success w-25">
                    結果を見る
                </a>
            <?php endif; ?>
        </div>
    </main>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- テーマ切替JS -->
<script src="../js/theme-toggle.js"></script>

<footer class="mt-4">
<?php include '../template/footer.php'; ?>
</footer>

</body>
</html>
