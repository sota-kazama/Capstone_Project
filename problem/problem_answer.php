<?php
require_once '../helpers/MemberDAO.php';
require_once '../helpers/ProblemDAO.php';
require_once '../helpers/QuestionDAO.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$member = $_SESSION['member'] ?? null;
$area_number = $_SESSION['area_number'] ?? null;

$daoProblem = new ProblemDAO();
$daoMember  = new MemberDAO();

/* 分野未選択 */
if ($area_number === null) {
    header('Location: category_select.php');
    exit;
}

// 問題一覧取得
$questions = $daoProblem->getQuestionsByArea($area_number);
$totalCount = count($questions);

// ★ 問題が1問もない場合
if ($totalCount === 0) {
    $_SESSION['error_message'] = 'この分野の問題は現在登録されていません';
    unset($_SESSION['area_number']);
    unset($_SESSION['problemArray']);
    header('Location: category_select.php');
    exit;
}

// 配列順のインデックス
$i = isset($_GET['i']) ? intval($_GET['i']) : 0;

// ★ 指定された問題が不正
if (!isset($questions[$i])) {
    $_SESSION['error_message'] = '指定された問題が存在しません。';
    header('Location: category_select.php');
    exit;
}

$question = $questions[$i];

// 選択肢配列
$choices = [
    1 => $question->choices1,
    2 => $question->choices2,
    3 => $question->choices3,
    4 => $question->choices4,
];
$labels = [1 => 'A', 2 => 'B', 3 => 'C', 4 => 'D'];

// 正解配列
$correctAnswers = array_map(
    'intval',
    json_decode($question->correct_answers, true) ?? []
);

// ユーザー選択
$selectedAnswer = isset($_POST['answer']) ? (int)$_POST['answer'] : null;

// 回答処理（ログインユーザー）
if ($member && $selectedAnswer !== null) {
    $isCorrect = in_array($selectedAnswer, $correctAnswers, true);

    $daoMember->incrementAnswerCount($member->user_id, $isCorrect);

    $member->u_answers_count += 1;
    if ($isCorrect) {
        $member->u_correct_count += 1;
    }

    // question_hold 更新
    $questionHold = array_filter(
        explode('_', $member->question_hold ?? ''),
        'strlen'
    );
    array_shift($questionHold);
    $member->question_hold = implode('_', $questionHold);
    $daoMember->updateUserProblem($member->user_id, $member->question_hold);

    $_SESSION['member'] = $member;
}

/* =========================
   テーマ（Cookie）
========================= */
$theme = $_COOKIE['theme'] ?? 'light';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>回答結果</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="../css/BaseDesignData.css" rel="stylesheet">
<link href="../css/side.css" rel="stylesheet">

<!-- ★ ダークモード -->
<link id="theme-css"
      href="../css_theme/<?= htmlspecialchars($theme) ?>.css"
      rel="stylesheet">
<link href="../css_theme/toggle-button.css" rel="stylesheet">
</head>

<body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">

<?php include '../template/header.php'; ?>

<div class="d-flex w-100 min-vh-100">
    <div class="d-none d-md-block">
        <?php include '../template/side.php'; ?>
    </div>

    <main class="main-content">
        <h1>回答結果</h1>

        <h2>第<?= $i + 1 ?>問（全<?= $totalCount ?>問）</h2>
        <h3><?= htmlspecialchars($question->q_content) ?></h3>

        <?php if (!empty($question->image_path)): ?>
            <img src="../uploads/<?= htmlspecialchars($question->image_path) ?>" class="img-fluid mb-3">
        <?php endif; ?>

        <table class="table">
            <thead>
                <tr>
                    <th style="width:10%">選択</th>
                    <th>内容</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($choices as $k => $v):
                $isCorrectChoice = in_array($k, $correctAnswers, true);
                $isSelected = ($k === $selectedAnswer);
                $btnClass = $isCorrectChoice
                    ? 'btn-success'
                    : ($isSelected ? 'btn-danger' : 'btn-outline-secondary');
                $displayText = htmlspecialchars($v)
                    . ($isCorrectChoice ? '（正解）' : '');
            ?>
                <tr>
                    <td>
                        <button class="btn btn-sm <?= $btnClass ?>" disabled>
                            <?= $labels[$k] ?>
                        </button>
                    </td>
                    <td><?= $displayText ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div class="d-flex justify-content-center mt-4">
            <div style="width:13rem">
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

<!-- ★ テーマ切替ボタン -->
<button id="theme-toggle-btn" class="btn btn-primary theme-toggle-btn">
    <i id="theme-icon"
       class="bi <?= $theme === 'dark' ? 'bi-sun' : 'bi-moon' ?>">
    </i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/theme-toggle.js"></script>
</body>

<footer>
<?php include '../template/footer.php'; ?>
</footer>
</html>
