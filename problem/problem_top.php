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
$daoQuestion = new QuestionDAO();

// テーマ取得
$theme = $_SESSION['theme'] ?? 'light';

// 分野一覧取得
$categories = $daoProblem->getProblemName();

// POST処理（新しく解き始める）
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['area_number'])) {
    $area_number = $_POST['area_number'];
    $_SESSION['area_number'] = $area_number;

    // 選択分野の問題ID配列を取得してセッションに保存
    $problemString = $daoProblem->getProblemIdString($area_number);
    $problemArray = array_filter(explode(',', $problemString), fn($v) => $v !== '');
    $_SESSION['problemArray'] = $problemArray;

    // ログインユーザーの場合は question_hold を最初からに更新
    if ($member) {
        $daoMember->updateUserProblem($member->user_id, implode('_', $problemArray));
        $member->question_hold = implode('_', $problemArray);
        $_SESSION['member'] = $member;
    }

    // 最初の問題番号でリダイレクト
    $firstQuestionNumber = intval($problemArray[0] ?? 0);
    header("Location: problem_response.php?i={$firstQuestionNumber}&area_number=" . urlencode($area_number));
    exit;
}

// 現在の分野番号（ページロード時用）
$area_number = $_SESSION['area_number'] ?? null;
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>問題トップ</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="../css/BaseDesignData.css" rel="stylesheet">
<link href="../css/side.css" rel="stylesheet">
<link id="theme-css" href="../css_theme/<?= htmlspecialchars($theme) ?>.css" rel="stylesheet">
<link href="../css_theme/toggle-button.css" rel="stylesheet">
</head>

<body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">

<?php include '../template/header.php'; ?>

<div class="d-flex w-100 min-vh-100">
    <div class="d-none d-md-block">
        <?php include '../template/side.php'; ?>
    </div>

    <main class="main-content">
        <h1>問題分野選択</h1>

        <!-- 分野選択フォーム（新しく解き始める） -->
        <form method="post" class="mb-4">
            <div class="mb-4">
                <label class="form-label">分野</label>
                <select class="form-select" name="area_number" required>
                    <?php foreach ($categories as $row): ?>
                        <option value="<?= htmlspecialchars($row['area_number']) ?>"
                            <?= $area_number == $row['area_number'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($row['area_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-outline-primary">
                    <?= ($member && !empty($member->question_hold)) ? '新しく解き始める' : '問題を解きはじめる' ?>
                </button>
            </div>
        </form>

        <!-- 続きから解き始めるボタン -->
        <?php if ($member && !empty($member->question_hold) && !empty($_SESSION['problemArray'])): ?>
            <?php
            $nextQuestionNumber = intval(explode('_', $member->question_hold)[0] ?? 0);
            ?>
            <div class="text-center mt-4">
                <a href="problem_response.php?i=<?= $nextQuestionNumber ?>&area_number=<?= urlencode($area_number) ?>" 
                   class="btn btn-outline-success">
                    前回の続きから解き始める
                </a>
            </div>
        <?php endif; ?>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/theme-toggle.js"></script>
</body>

<footer>
<?php include '../template/footer.php'; ?>
</footer>
</html>
