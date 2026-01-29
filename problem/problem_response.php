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

/* ===== 選択肢を配列にまとめる ===== */
$choices = [
    1 => $question->choices1,
    2 => $question->choices2,
    3 => $question->choices3,
    4 => $question->choices4,
];

/* ===== 正解番号（JSON -> 配列） ===== */
$correctAnswers = json_decode($question->correct_answers, true) ?? [];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link href="../css/BaseDesignData.css" rel="stylesheet" />
    <link href="../css/side.css" rel="stylesheet" />
    <title>問題回答</title>
</head>
<body>
    <div class="d-flex w-100 min-vh-100">
        <?php include '../template/side.php'; ?>

        <main class="main-content">
            <h1>問題回答</h1>
            <h2>第<?= htmlspecialchars($question->q_number) ?>問</h2>
            <h3><?= htmlspecialchars($question->q_content) ?></h3>

            <?php if (!empty($question->image_path)): ?>
                <img src="../uploads/<?= htmlspecialchars($question->image_path) ?>" class="img-fluid mb-3">
            <?php endif; ?>

            <!-- 回答フォーム -->
            <form action="problem_answer.php?i=<?= $i ?>" method="post">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 10%">選択</th>
                            <th>内容</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($choices as $key => $value): ?>

                            <?php
                            // 正解判定（複数正解にも対応）
                            $isCorrect = in_array($key, $correctAnswers, true);

                            // ボタンの色（正解なら緑）
                            $btnClass = $isCorrect ? 'btn-success' : 'btn-outline-primary';

                            // 正解表示
                            $displayText = htmlspecialchars($value);
                            if ($isCorrect) {
                                $displayText .= " (正解)";
                            }
                            ?>

                            <tr>
                                <td>
                                    <!-- value は番号（1〜4）を送る -->
                                    <button name="answer" value="<?= $key ?>"
                                            class="btn <?= $btnClass ?>">
                                        選択肢<?= $key ?>
                                    </button>
                                </td>
                                <td><?= $displayText ?></td>
                            </tr>

                        <?php endforeach; ?>
                    </tbody>
                </table>
            </form>
        </main>
    </div>
</body>
</html>
