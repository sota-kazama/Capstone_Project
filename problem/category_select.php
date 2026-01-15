<?php
require_once '../helpers/QuestionDAO.php';
require_once '../helpers/ProblemDAO.php';
$dao = new ProblemDAO();
$category = $dao->getProblemName();

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
                <h1>問題分野選択</h1>    
                <form action="problem.php" method="post">
                    <div class="mb-4">
                    <label for="receipt" class="form-label">分野</label>
                    <select class="form-select" name="area_number">
                    <?php foreach ($category as $name): ?>
                        <option value="<?= htmlspecialchars($name) ?>">
                            <?= htmlspecialchars($name) ?>
                        </option>
                    <?php endforeach; ?>
                    </select>
                    </div>
                    <div class="grid gap-3">
                        <div class="d-flex flex-wrap justify-content-center p-2 g-col-6 gap-4">
                            <div style="width: 20rem;">
                                <input type="submit" value="次へ" class="btn btn-outline-primary">
                            </div>
                        </div>
                    </div>
                </form>
            </main>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    <footer>
        <?php include '../template/footer.php'; ?>
    </footer>
</html>
