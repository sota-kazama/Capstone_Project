<?php
require_once '../helpers/MemberDAO.php';
require_once '../helpers/ProblemDAO.php';
require_once '../helpers/QuestionDAO.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$member = $_SESSION['member'] ?? null;
$daoMember = new MemberDAO();
$daoProblem = new ProblemDAO();

// 現在のテーマ
$theme = $_SESSION['theme'] ?? 'light';

// 分野一覧
$categories = $daoProblem->getProblemName();

/* 新しく解き始める */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['area_number'])) {
    $area_number = $_POST['area_number'];
    $problemIds = $daoProblem->getProblemIdsByArea($area_number);

    if (empty($problemIds)) {
        $_SESSION['error_message'] = 'この分野の問題は現在登録されていません';
        header("Location: problem_top.php");
        exit;
    }

    $_SESSION['area_number'] = $area_number;
    $_SESSION['problemArray'] = $problemIds;

    if ($member) {
        $member->question_hold = implode('_', $problemIds);
        $daoMember->updateUserProblem($member->user_id, $member->question_hold);
        $_SESSION['member'] = $member;
    }

    header("Location: problem_response.php?i=0&area_number=" . urlencode($area_number));
    exit;
}

$area_number = $_SESSION['area_number'] ?? null;

$theme = $_COOKIE['theme'] ?? 'light';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>問題トップ</title>

<!-- Bootstrap & Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<!-- 自作CSS -->
<link href="../css/BaseDesignData.css" rel="stylesheet">
<link href="../css/side.css" rel="stylesheet">
<link href="../css_theme/toggle-button.css" rel="stylesheet">

<!-- テーマ切替CSS -->
<link id="theme-css" href="../css_theme/<?= htmlspecialchars($theme) ?>.css" rel="stylesheet">
</head>

<body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">

<?php include '../template/header.php'; ?>

<div class="d-flex w-100 min-vh-100">
    <div class="d-none d-md-block">
        <?php include '../template/side.php'; ?>
    </div>

    <main class="main-content p-4">
        <h1>問題分野選択</h1>

        <!-- エラーメッセージ -->
        <?php if (!empty($_SESSION['error_message'])): ?>
            <div class="alert alert-warning text-center">
                <?= htmlspecialchars($_SESSION['error_message']) ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <!-- 新しく解き始める -->
        <form method="post" class="mb-4">
            <label class="form-label">分野</label>
            <select class="form-select mb-3" name="area_number" required>
                <?php foreach ($categories as $row): ?>
                    <option value="<?= htmlspecialchars($row['area_number']) ?>">
                        <?= htmlspecialchars($row['area_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div class="text-center">
                <button type="submit" class="btn btn-outline-primary">
                    問題を解きはじめる
                </button>
            </div>
        </form>

        <!-- 続きから -->
        <?php if ($member && !empty($member->question_hold) && !empty($_SESSION['problemArray'])): ?>
            <?php
            $problemIds = $_SESSION['problemArray'];
            $nextQ = intval(explode('_', $member->question_hold)[0]);
            $nextIndex = array_search($nextQ, $problemIds, true);
            $nextIndex = ($nextIndex === false) ? 0 : $nextIndex;
            ?>
            <div class="text-center mt-4">
                <a href="problem_response.php?i=<?= $nextIndex ?>&area_number=<?= urlencode($area_number) ?>"
                   class="btn btn-outline-success">
                    前回の続きから解き始める
                </a>
            </div>
        <?php endif; ?>
    </main>
</div>

<!-- テーマ切替ボタン -->
<button id="theme-toggle-btn" class="btn btn-primary theme-toggle-btn">
    <i id="theme-icon" class="bi <?= $theme === 'dark' ? 'bi-sun' : 'bi-moon' ?>"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/theme-toggle.js"></script>
</body>

<footer>
<?php include '../template/footer.php'; ?>
</footer>
</html>
