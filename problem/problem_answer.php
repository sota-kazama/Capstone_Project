<?php
require_once '../helpers/MemberDAO.php';
require_once '../helpers/ProblemDAO.php';
require_once '../helpers/QuestionDAO.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answer_content = $_POST['answer_content'] ?? '';
    $A = $_POST['A'] ?? '';
    $B = $_POST['B'] ?? '';
    $C = $_POST['C'] ?? '';
    $D = $_POST['D'] ?? '';
}
$member = $_SESSION['member'] ?? null;
$area_number = $_SESSION['area_number'];

$dao = new ProblemDAO();
$dao2 = new MemberDAO();
$dao3 = new QuestionDAO();
$i = 0;

$questions = $dao->getQuestionsByArea($area_number);
$i = isset($_GET['i']) ? intval($_GET['i']) : 0;
// 問題が存在するかチェック
if (!empty($questions) && isset($questions[$i])) {
    $question = $questions[$i];
} else {
    $question = null;
}
$referer = $_SERVER['HTTP_REFERER'] ?? '';

// 初回アクセス時だけ初期化
if (!isset($_SESSION['alpha']) || !isset($_SESSION['beta'])) {
    $problemString = $dao->getProblemIdString($area_number);
    $_SESSION['alpha'] = $problemString;
    $beta = '';
    $_SESSION['beta'] = $beta;
}

// 現在のalphaを取得
$alpha = $_SESSION['alpha'];

// 先頭を削除
$a = $dao->removeHeadFromAlpha($alpha);

// 表示
echo $a;

// セッションを更新
$_SESSION['alpha'] = $a;

$beta = $_SESSION['beta'];

$b = $dao->addToBeta($beta, $question->q_number);

echo $b;

$_SESSION['beta'] = $b;

if (isset($member)) {
    $dao2->updateUserProblem($member->user_id, $a);
}
?>

<!DOCTYPE html>
<html>
    <head>
        <!--こっちのheadは変更しない-->
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
        <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        />
        <link href="css/BaseDesignData.css" rel="stylesheet" />
        <link href="../css/side.css" rel="stylesheet" />
        <?php include '../template/header.php'; ?>
    </head>

    <head>
        <!--こっちのheadを変更しない-->
        <title>メインページ</title>
    </head>

    <body>
        <div class="d-flex w-100 min-vh-100">
            <?php include '../template/side.php';?>

            <main class="main-content">
                <!--ここに記載する-->
                <div class="d-flex flex-wrap">
                    <h1>問題解説</h1>

                    <?php if (isset($member) && strpos($referer, 'problem_response.php') !== false) : ?>
                        <form action="problem.php" method="post" class="ms-auto">
                            <input type="hidden" name="bookmark_q_number" value="<?= $question->q_number ?>">
                            <input type="submit" class="btn btn-outline-primary" value="ブックマーク">
                        </form>
                    <?php endif; ?>
                    
                </div>
                <h2>第<?php echo $question->q_number; ?>問</h2>
                <h3><?= htmlspecialchars($question->q_content) ?></h3>
                <?php if($question->image_path !== null) : ?>
                    <img src="../uploads/<?= $question->image_path?>" alt="">
                <?php endif;?>

                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 10%">選択肢</th>
                            <th>問題文</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php if($A === $answer_content) : ?>
                                <td><a class="btn btn-success" role="button">A</a></td>
                                <td><?= $question->answer_content?></td>
                            <?php else :?>
                                <td><a class="btn btn-danger" role="button">A</a></td>
                                <td></td>                                
                            <?php endif;?>
                        </tr>
                        <tr>
                            <?php if($B === $answer_content) : ?>
                                <td><a class="btn btn-success" role="button">B</a></td>
                                <td><?= $question->answer_content?></td>
                            <?php else :?>
                                <td><a class="btn btn-danger" role="button">B</a></td>
                                <td><?= $question->wrong_answer1?></td>
                            <?php endif;?>
                        </tr>
                        <tr>
                            <?php if($C === $answer_content) : ?>
                                <td><a class="btn btn-success" role="button">C</a></td>
                                <td><?= $question->answer_content?></td>
                            <?php else :?>
                                <td><a class="btn btn-danger" role="button">C</a></td>
                                <td><?= $question->wrong_answer2?></td>                                
                            <?php endif;?>
                        </tr>
                        <tr>
                            <?php if($D === $answer_content) : ?>
                                <td><a class="btn btn-success" role="button">D</a></td>
                                <td><?= $question->answer_content?></td>
                            <?php else :?>
                                <td><a class="btn btn-danger" role="button">D</a></td>
                                <td><?= $question->wrong_answer3?></td>
                            <?php endif;?>
                        </tr>
                    </tbody>
                </table>
                <div class="d-flex flex-wrap justify-content-center">
                    <div style="width: 13rem">
                        <?php if(strpos($referer, 'problem_response.php') !== false) : ?>
                            <?php if(!empty($questions) && isset($questions[$i+1])) : ?>
                                <a href="problem_response.php?i=<?php echo $i + 1; ?>" class="btn btn-outline-primary w-100">
                                    次の問題
                                </a>
                            <?php else : ?>
                                <a href="problem_result.php" class="btn btn-outline-primary w-100">結果を見る</a>
                            <?php endif;?>
                        <?php else :?>
                            <?php if(!empty($questions) && isset($questions[$i+1])) : ?>
                                <a href="problem_review.php?i=<?php echo $i + 1; ?>" class="btn btn-outline-primary w-100">
                                    次の問題
                                </a>
                            <?php else : ?>
                                <a href="problem_result.php" class="btn btn-outline-primary w-100">結果を見る</a>
                            <?php endif;?>
                        <?php endif;?>
                    </div>
                </div>
                <?php if (isset($member)) : ?>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-outline-success ms-auto" disabled>1</button>
                    <button type="button" class="btn btn-outline-warning" disabled>2</button>
                    <button type="button" class="btn btn-outline-danger" disabled>3</button>
                </div>
                <?php endif; ?>
                <div class="container">        <!-- 全体を囲むコンテナ -->
  
            </main>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    <footer>
        <?php include '../template/footer.php'; ?>
    </footer>
</html>
