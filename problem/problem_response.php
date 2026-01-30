<?php
require_once '../helpers/MemberDAO.php';
require_once '../helpers/ProblemDAO.php';
require_once '../helpers/QuestionDAO.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$member = $_SESSION['member'] ?? null;
$area_number = $_SESSION['area_number'] ?? null;

if ($area_number === null) {
    header('Location: category_select.php');
    exit;
}

$daoProblem = new ProblemDAO();
$questions = $daoProblem->getQuestionsByArea($area_number);
$i = isset($_GET['i']) ? intval($_GET['i']) : 0;

if (empty($questions) || !isset($questions[$i])) {
    echo '問題が存在しません。';
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

// ラベル
$labels = [1=>'A',2=>'B',3=>'C',4=>'D'];

// テーマ
$theme = $_SESSION['theme'] ?? 'light';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>問題回答</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="../css/BaseDesignData.css" rel="stylesheet">
<link href="../css/side.css" rel="stylesheet">
<link id="theme-css" href="../css_theme/<?= htmlspecialchars($theme) ?>.css" rel="stylesheet">
</head>
<body class="<?= $theme==='dark'?'dark-mode':'light-mode' ?>">
<?php include '../template/header.php'; ?>

<div class="d-flex w-100 min-vh-100">
    <div class="d-none d-md-block"><?php include '../template/side.php'; ?></div>
    <main class="main-content">
        <h1>問題回答</h1>
        <h2>第<?= htmlspecialchars($question->q_number) ?>問</h2>
        <h3><?= htmlspecialchars($question->q_content) ?></h3>

        <?php if (!empty($question->image_path)): ?>
            <img src="../uploads/<?= htmlspecialchars($question->image_path) ?>" class="img-fluid mb-3">
        <?php endif; ?>

        <form action="problem_answer.php?i=<?= $i ?>" method="post">
            <table class="table">
                <thead>
                    <tr><th style="width:10%">選択</th><th>内容</th></tr>
                </thead>
                <tbody>
                    <?php foreach($choices as $k=>$v): ?>
                    <tr>
                        <td>
                            <button type="submit" name="answer" value="<?= $k ?>" class="btn btn-outline-primary btn-sm">
                                <?= $labels[$k] ?>
                            </button>
                        </td>
                        <td><?= htmlspecialchars($v) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </form>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/theme-toggle.js"></script>
</body>
<footer><?php include '../template/footer.php'; ?></footer>
</html>
