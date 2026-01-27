<?php
require_once '../helpers/ProblemDAO.php';

$dao = new ProblemDAO();
$category = $dao->getProblemName();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="../css/BaseDesignData.css" rel="stylesheet" />
    <link href="../css/side.css" rel="stylesheet" />
    <?php include '../template/header.php'; ?>
    <title>問題</title>
</head>

<body>
<div class="d-flex w-100 min-vh-100">
<?php include '../template/side.php';?>

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
            <input type="submit" value="次へ" class="btn btn-outline-primary">
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
