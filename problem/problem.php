<?php
require_once '../helpers/QuestionDAO.php';
require_once '../helpers/ProblemDAO.php';

$dao = new QuestionDAO();
$dao2 = new ProblemDAO();

// ブックマーク保存
if (isset($_POST['bookmark_q_number'])) {
    $_SESSION['bookmark_q_number'] = (int)$_POST['bookmark_q_number'];
}

// ブックマークされた問題を取得
$bookmarkQuestion = null;
if (isset($_SESSION['bookmark_q_number'])) {
    $bookmarkQuestion = $dao->findById($_SESSION['bookmark_q_number']);
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $area_number = $_POST['area_number'] ?? '';
    $dao2->getProblem($area_number);
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
        <link href="../css/BaseDesignData.css" rel="stylesheet" />
        <link href="../css/side.css" rel="stylesheet" />
        <?php include '../template/header.php'; ?>
    </head>

    <head>
        <!--こっちのheadを変更しない-->
        <title>問題</title>
    </head>

    <body>
        <div class="d-flex w-100 min-vh-100">
            <?php include '../template/side.php';?>
            <main class="main-content">
                <!--ここに記載する-->
                <h1>問題トップページ</h1>
            <div class="grid gap-3">
                <div class="d-flex flex-wrap justify-content-center p-2 g-col-6 gap-4">
                    <div style="width: 20rem;">
                        <a href="problem_response.php" class="btn btn-outline-primary w-100">問題開始</a>
                    </div>
                    <?php if ($bookmarkQuestion !== null) : ?>
                        <div style="width: 20rem;">
                            <a href="problem_response.php?i=<?= $bookmarkQuestion->q_number - 1 ?>"
                            class="btn btn-outline-primary w-100">
                                続きから（<?= $bookmarkQuestion->q_number ?>問目）
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if(isset($member)) : ?>
                    <div class="d-flex flex-wrap justify-content-center p-2 g-col-6 gap-4">
                        <div class="card shadow-sm" style="width: 14rem;">
                            <div class="card-body">
                                <h5 class="card-title fw-semibold">レベル1(復習不要)</h5>
                                <h5 class="card-title fw-semibold">〇問</h5>
                                <a href="problem_review.php" class="btn btn-outline-success w-100">復習</a>
                            </div>
                        </div>
                        <div class="card shadow-sm" style="width: 14rem;">
                            <div class="card-body">
                                <h5 class="card-title fw-semibold">レベル2(復習要)</h5>
                                <h5 class="card-title fw-semibold">〇問</h5>
                                <a href="problem_review.php" class="btn btn-outline-warning w-100">復習</a>
                            </div>
                        </div>
                        <div class="card shadow-sm" style="width: 14rem;">
                            <div class="card-body">
                                <h5 class="card-title fw-semibold">レベル3(復習必要)</h5>
                                <h5 class="card-title fw-semibold">〇問</h5>
                                <a href="problem_review.php" class="btn btn-outline-danger w-100">復習</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            </main>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    <footer>
        <?php include '../template/footer.php'; ?>
    </footer>
</html>
