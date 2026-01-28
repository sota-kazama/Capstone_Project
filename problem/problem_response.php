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

// 問題存在チェック
if (empty($questions) || !isset($questions[$i])) {
    echo '問題が存在しません。';
    exit;
}

$question = $questions[$i];
?>
<!DOCTYPE html>
<html>
<head>
    <!--こっちのheadは変更しない-->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link href="../css/BaseDesignData.css" rel="stylesheet" />
    <link href="../css/side.css" rel="stylesheet" />

    <?php include '../template/header.php'; ?>
    <title>問題回答</title>
</head>

<body>
<div class="d-flex w-100 min-vh-100">
<?php include '../template/side.php'; ?>

<main class="main-content">
    <div class="d-flex align-items-center">
        <h1>問題回答</h1>

        <!-- ブックマーク -->
        <?php if ($member): ?>
        <form action="problem.php" method="post" class="ms-auto">
            <input type="hidden" name="bookmark_q_number" value="<?= htmlspecialchars($question->q_number) ?>">
            <input type="hidden" name="area_number" value="<?= htmlspecialchars($area_number) ?>">
            <button type="submit" class="btn btn-outline-primary">ブックマーク</button>
        </form>
        <?php endif; ?>
    </div>

    <h2>第<?= htmlspecialchars($question->q_number) ?>問</h2>
    <h3><?= htmlspecialchars($question->q_content) ?></h3>

    <?php if (!empty($question->image_path)): ?>
        <img src="../uploads/<?= htmlspecialchars($question->image_path) ?>" class="img-fluid mb-3">
    <?php endif; ?>

    <!-- 回答フォーム -->
    <form action="problem_answer.php?i=<?= $i ?>" method="post">
        <input type="hidden" name="correct_answer" value="<?= htmlspecialchars($question->answer_content) ?>">

        <table class="table">
            <thead>
                <tr>
                    <th style="width: 10%">選択</th>
                    <th>内容</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <button name="answer" value="<?= htmlspecialchars($question->answer_content) ?>"
                                class="btn btn-outline-primary">A</button>
                    </td>
                    <td><?= htmlspecialchars($question->answer_content) ?></td>
                </tr>
                <tr>
                    <td>
                        <button name="answer" value="<?= htmlspecialchars($question->wrong_answer1) ?>"
                                class="btn btn-outline-primary">B</button>
                    </td>
                    <td><?= htmlspecialchars($question->wrong_answer1) ?></td>
                </tr>
                <tr>
                    <td>
                        <button name="answer" value="<?= htmlspecialchars($question->wrong_answer2) ?>"
                                class="btn btn-outline-primary">C</button>
                    </td>
                    <td><?= htmlspecialchars($question->wrong_answer2) ?></td>
                </tr>
                <tr>
                    <td>
                        <button name="answer" value="<?= htmlspecialchars($question->wrong_answer3) ?>"
                                class="btn btn-outline-primary">D</button>
                    </td>
                    <td><?= htmlspecialchars($question->wrong_answer3) ?></td>
                </tr>
            </tbody>
        </table>
    </form>

    <!-- ラベリング -->
    <?php if ($member): ?>
    <div class="d-flex gap-2 justify-content-end">
        <button class="btn btn-outline-success" disabled>1</button>
        <button class="btn btn-outline-warning" disabled>2</button>
        <button class="btn btn-outline-danger" disabled>3</button>
    </div>
    <?php endif; ?>
</main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

<footer>
<?php include '../template/footer.php'; ?>
</footer>
</html>
