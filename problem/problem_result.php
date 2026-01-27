<?php
require_once '../helpers/ProblemDAO.php';

$dao = new ProblemDAO();

// POSTで分野番号を受け取る
$area_number = $_POST['area_number'] ?? '';

$problemIds = [];
$problemIdString = '';
$removedHeadString = '';

if ($area_number !== '') {
    // 指定分野の問題ID配列取得
    $problemIds = $dao->getProblemIdsByArea($area_number);

    // _ 区切り文字列作成
    $problemIdString = $dao->getProblemIdString($area_number);

    // 文字列操作：先頭を削除
    $removedHeadString = $dao->removeHeadFromAlpha($problemIdString);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link href="../css/BaseDesignData.css" rel="stylesheet" />
    <link href="../css/side.css" rel="stylesheet" />
    <?php include '../template/header.php'; ?>
    <title>問題</title>
</head>

<body>
<div class="d-flex w-100 min-vh-100">
    <?php include '../template/side.php';?>
    <main class="main-content">
        <h1>結果</h1>

        <?php if ($area_number === ''): ?>
            <div class="alert alert-warning">分野が選択されていません。</div>
        <?php else: ?>
            <h3>分野番号: <?= htmlspecialchars($area_number) ?></h3>

            <h5>問題ID一覧</h5>
            <ul>
                <?php foreach ($problemIds as $id): ?>
                    <li><?= htmlspecialchars($id) ?></li>
                <?php endforeach; ?>
            </ul>

            <h5>問題ID文字列 (_ 区切り)</h5>
            <p><?= htmlspecialchars($problemIdString) ?></p>

            <h5>先頭削除後の文字列</h5>
            <p><?= htmlspecialchars($removedHeadString) ?></p>
        <?php endif; ?>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
<footer>
    <?php include '../template/footer.php'; ?>
</footer>
</html>
