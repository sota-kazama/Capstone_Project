<?php
require_once '../helpers/MemberDAO.php';
require_once '../helpers/ProblemDAO.php';
require_once '../helpers/QuestionDAO.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$member = $_SESSION['member'] ?? null;
$area_number = $_SESSION['area_number'] ?? null;

if ($area_number === null) {
    header('Location: category_select.php');
    exit;
}

$dao = new ProblemDAO();
$questions = $dao->getQuestionsByArea($area_number);
$i = isset($_GET['i']) ? intval($_GET['i']) : 0;

if (empty($questions) || !isset($questions[$i])) {
    echo '問題が存在しません。';
    exit;
}

$question = $questions[$i];

// 選択肢を配列にまとめる
$choices = [
    1 => $question->choices1,
    2 => $question->choices2,
    3 => $question->choices3,
    4 => $question->choices4,
];

// 正解番号（JSON -> 配列）
$correctAnswers = json_decode($question->correct_answers, true) ?? [];

// ユーザーの選択（番号）
$selectedAnswer = isset($_POST['answer']) ? (int)$_POST['answer'] : null;

// 正誤判定
$isCorrect = $selectedAnswer !== null
    && in_array($selectedAnswer, $correctAnswers, true);
?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="../css/BaseDesignData.css" rel="stylesheet">
    <link href="../css/side.css" rel="stylesheet">
    <title>問題解説</title>
</head>

<body>
<div class="d-flex w-100 min-vh-100">
<?php include '../template/side.php'; ?>

<main class="main-content p-4">

<h1>問題解説</h1>
<h2>第<?= htmlspecialchars($question->q_number) ?>問</h2>
<h3><?= htmlspecialchars($question->q_content) ?></h3>

<?php if (!empty($question->image_path)): ?>
    <img src="../uploads/<?= htmlspecialchars($question->image_path) ?>"
         class="img-fluid mb-3">
<?php endif; ?>

<hr>

<p>
<strong>あなたの選択：</strong>
<?= $selectedAnswer ? htmlspecialchars($choices[$selectedAnswer]) : '未回答' ?>
</p>

<p>
<strong>正解：</strong>
<?php
$correctTexts = array_map(
    fn($n) => $choices[$n] ?? '',
    $correctAnswers
);
echo htmlspecialchars(implode(' / ', $correctTexts));
?>
</p>

<p class="<?= $isCorrect ? 'text-success' : 'text-danger' ?>">
    <?= $isCorrect ? '正解です！' : '不正解です。' ?>
</p>

<p><strong>解説：</strong><?= htmlspecialchars($question->q_source) ?></p>

<!-- 次へ -->
<div class="d-flex justify-content-center mb-3">
<div style="width: 13rem">
<?php if (isset($questions[$i + 1])): ?>
    <a href="problem_response.php?i=<?= $i + 1 ?>"
       class="btn btn-outline-primary w-100">
        次の問題
    </a>
<?php else: ?>
    <a href="problem_result.php"
       class="btn btn-outline-primary w-100">
        結果を見る
    </a>
<?php endif; ?>
</div>
</div>

</main>
</div>
</body>
</html>
