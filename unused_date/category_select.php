<?php
require_once '../helpers/ProblemDAO.php';

$dao      = new ProblemDAO();
$category = $dao->getProblemName();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- CSS -->
    <link href="../css/BaseDesignData.css" rel="stylesheet">
    <link href="../css/side.css" rel="stylesheet">
    <link id="theme-css" href="../css_theme/<?= htmlspecialchars($theme) ?>.css" rel="stylesheet">
    <link href="../css_theme/toggle-button.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <title>問題分野選択</title>
</head>

<body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">

    <!-- ヘッダー -->
    <?php include '../template/header.php'; ?>

    <div class="d-flex w-100 min-vh-100">

        <!-- サイドバー -->
        <div class="d-none d-md-block">
            <?php include '../template/side.php'; ?>
        </div>

        <!-- メインコンテンツ -->
        <main class="main-content">
            <h1>問題分野選択</h1>

            <form action="problem.php" method="post">
                <div class="mb-4">
                    <label class="form-label">分野</label>
                    <select class="form-select" name="area_number" required>
                        <?php foreach ($category as $row): ?>
                            <option value="<?= htmlspecialchars($row['area_number']) ?>">
                                <?= htmlspecialchars($row['area_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="text-center">
                    <input
                        type="submit"
                        value="次へ"
                        class="btn btn-outline-primary"
                    >
                </div>
            </form>
        </main>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/theme-toggle.js"></script>

</body>

<footer>
    <?php include '../template/footer.php'; ?>
</footer>
</html>
