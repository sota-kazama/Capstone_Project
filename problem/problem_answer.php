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

// 初回アクセス時の alpha/beta 初期化
if (!isset($_SESSION['alpha']) || !isset($_SESSION['beta'])) {
    $_SESSION['alpha'] = $dao->getProblemIdString($area_number);
    $_SESSION['beta'] = '';
}

// POST送信があれば alpha/beta 更新
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedAnswer = $_POST['answer'] ?? '';
    
    // alphaの先頭削除
    $_SESSION['alpha'] = $dao->removeHeadFromAlpha($_SESSION['alpha']);
    
    // betaに追加
    $_SESSION['beta'] = $dao->addToBeta($_SESSION['beta'], $question->q_number);

    // ユーザーデータ更新（ログイン中のみ）
    if ($member) {
        $daoMember = new MemberDAO();
        $daoMember->updateUserProblem($member->user_id, $_SESSION['alpha']);
    }

    // 正解判定
    $isCorrect = ($selectedAnswer === $question->answer_content);
} else {
    $selectedAnswer = null;
    $isCorrect = null;
}

// リファラー判定（前ページ）
$referer = $_SERVER['HTTP_REFERER'] ?? '';
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
    <?php include '../template/header.php'; ?>
    <title>問題解説</title>
</head>

<body>
<div class="d-flex w-100 min-vh-100">
    <?php include '../template/side.php'; ?>

    <main class="main-content">
        <div class="d-flex align-items-center mb-3">
            <h1>問題解説</h1>
            <!-- しおり -->
            <?php if ($member && strpos($referer, 'problem_response.php') !== false): ?>
            <form action="problem.php" method="post" class="ms-auto">
                <input type="hidden" name="bookmark_q_number" value="<?= htmlspecialchars($question->q_number) ?>">
                <input type="hidden" name="area_number" value="<?= htmlspecialchars($area_number) ?>">
                <input type="hidden" name="i" value="<?= htmlspecialchars($i) ?>">
                <button type="submit" class="btn btn-outline-primary">ブックマーク</button>
            </form>
            <?php endif; ?>
        </div>

        <h2>第<?= htmlspecialchars($question->q_number) ?>問</h2>
        <h3><?= htmlspecialchars($question->q_content) ?></h3>
        <?php if (!empty($question->image_path)): ?>
            <img src="../uploads/<?= htmlspecialchars($question->image_path) ?>" class="img-fluid mb-3">
        <?php endif; ?>

        <table class="table">
            <thead>
                <tr>
                    <th style="width: 10%">選択</th>
                    <th>内容</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $options = [
                    'A' => $question->answer_content,
                    'B' => $question->wrong_answer1,
                    'C' => $question->wrong_answer2,
                    'D' => $question->wrong_answer3
                ];
                foreach ($options as $label => $text):
                    $btnClass = '';
                    $displayText = htmlspecialchars($text);

                    if ($selectedAnswer !== null) {
                        $btnClass = ($selectedAnswer === $text) ? 'btn-success' : 'btn-danger';
                        if ($selectedAnswer !== $text && $text === $question->answer_content) {
                            $displayText = htmlspecialchars($text) . " (正解)";
                        }
                    }
                ?>
                <tr>
                    <td>
                        <button class="btn <?= $btnClass ?>" disabled><?= $label ?></button>
                    </td>
                    <td><?= $displayText ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- 次の問題リンク -->
        <div class="d-flex justify-content-center mb-3">
            <div style="width: 13rem">
                <?php if (!empty($questions) && isset($questions[$i+1])): ?>
                    <a href="problem_response.php?i=<?= $i + 1 ?>" class="btn btn-outline-primary w-100">次の問題</a>
                <?php else: ?>
                    <a href="problem_result.php" class="btn btn-outline-primary w-100">結果を見る</a>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($member): ?>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-outline-success ms-auto" disabled>1</button>
            <button type="button" class="btn btn-outline-warning" disabled>2</button>
            <button type="button" class="btn btn-outline-danger" disabled>3</button>
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
