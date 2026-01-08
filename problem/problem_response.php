<?php
require_once '../helpers/QuestionDAO.php';

$dao = new QuestionDAO();
$questions = $dao->getAll();

$i = isset($_GET['i']) ? intval($_GET['i']) : 0;

// 問題が存在するかチェック
if (!empty($questions) && isset($questions[$i])) {
    $question = $questions[$i];
} else {
    $question = null;
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
            <?php include '../template/side.php'; ?>

            <main class="main-content">
                <!--ここに記載する-->
                <div class="d-flex flex-wrap">
                    <h1>問題回答</h1>
                    <!--しおり--> 
                    <?php if (isset($member)) : ?>
                        <form action="problem.php" method="post" class="ms-auto">
                            <input type="hidden" name="bookmark_q_number" value="<?= $question->q_number ?>">
                            <input type="submit" class="btn btn-outline-primary" value="ブックマーク">
                        </form>
                    <?php endif; ?>
                </div>

                <h2>第<?php echo $question->q_number; ?>問</h2>
                <h3><?php echo $question->q_content; ?></h3>

                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 10%">選択肢</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td><a class="btn btn-outline-primary" href="problem_answer.php?i=<?php echo $i; ?>" role="button">A</a></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><a class="btn btn-outline-primary" href="problem_answer.php?i=<?php echo $i; ?>" role="button">B</a></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><a class="btn btn-outline-primary" href="problem_answer.php?i=<?php echo $i; ?>" role="button">C</a></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><a class="btn btn-outline-primary" href="problem_answer.php?i=<?php echo $i; ?>" role="button">D</a></td>
                            <td></td>
                        </tr>

                    </tbody>

                </table>
                <!--ラベリング -->        
                <?php if (isset($member)) : ?>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-outline-success ms-auto" disabled>1</button>
                    <button type="button" class="btn btn-outline-warning" disabled>2</button>
                    <button type="button" class="btn btn-outline-danger" disabled>3</button>
                </div>
                <?php endif; ?>
            </main>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            
        </script>

    </body>

    <footer>
        <?php include '../template/footer.php'; ?>
    </footer>
</html>
