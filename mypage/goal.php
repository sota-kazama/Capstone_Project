<?php
require_once '../helpers/MemberDAO.php';
require_once '../helpers/u_goalsDAO.php';

session_start();

// 未ログインチェック
if (!isset($_SESSION['member'])) {
    header('Location: login.php');
    exit;
}

$member = $_SESSION['member'];
$error_message = null;

// POST時（登録処理）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $goal = $_POST['goal'] ?? '';
    $mile_stone = $_POST['mile_stone'] ?? '';
    $goal_date = $_POST['goal_date'] ?? null;

    if (empty($goal)) {
        $error_message = '目標を入力してください。';
    } elseif (mb_strlen($goal) > 100 || mb_strlen($mile_stone) > 100) {
        $error_message = '文字数は100文字以内で入力してください。';
    } else {
        $GoalsDAO = new GoalsDAO();
        $result = $GoalsDAO->insert(
            $member->user_id,
            $goal,
            $mile_stone,
            $goal_date
        );

        if ($result) {
            header('Location: mypage.php');
            exit;
        } else {
            $error_message = '目標の登録に失敗しました。';
        }
    }
}

$theme = $_COOKIE['theme'] ?? 'light';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link href="../css/BaseDesignData.css" rel="stylesheet" />
    <link href="../css/side.css" rel="stylesheet" />
    <link id="theme-css" rel="stylesheet" href="../css_theme/<?= htmlspecialchars($theme) ?>.css" />
    <link href="../css_theme/toggle-button.css" rel="stylesheet" />

    <!-- flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <title>目標登録</title>

    <?php include '../template/header.php'; ?>
</head>

<body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">
<div class="d-flex w-100 min-vh-100">

    <!-- サイドバー -->
    <div class="d-none d-md-block">
        <?php include 'side.php'; ?>
    </div>

    <!-- メイン -->
    <main class="main-content flex-grow-1 p-4">
        <h1 class="mt-5">目標登録</h1>

        <div class="card p-4 mt-3">
            <?php if ($error_message): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <!-- 目標 -->
                <div class="mb-3">
                    <label class="form-label">
                        目標 <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        name="goal"
                        id="goal"
                        class="form-control"
                        value="<?= htmlspecialchars($_POST['goal'] ?? '') ?>"
                        required
                    >
                    <div class="text-end small">
                        <span id="goal-count">0</span> / 100 文字
                    </div>
                </div>

                <!-- マイルストーン -->
                <div class="mb-3">
                    <label class="form-label">マイルストーン</label>
                    <input
                        type="text"
                        name="mile_stone"
                        id="mile_stone"
                        class="form-control"
                        value="<?= htmlspecialchars($_POST['mile_stone'] ?? '') ?>"
                    >
                    <div class="text-end small">
                        <span id="mile-stone-count">0</span> / 100 文字
                    </div>
                </div>

                <!-- 目標日（カレンダー常時表示） -->
                <div class="mb-3">
                    <label class="form-label">目標日</label>

                    <!-- 実際に送信される hidden -->
                    <input type="hidden" name="goal_date" id="goal_date"
                           value="<?= htmlspecialchars($_POST['goal_date'] ?? '') ?>">

                    <!-- カレンダー表示用 -->
                    <div id="calendar"></div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" id="submit-btn" class="btn btn-primary">
                        登録
                    </button>
                    <a href="mypage.php" class="btn btn-secondary">
                        戻る
                    </a>
                </div>
            </form>
        </div>
    </main>
</div>

<!-- テーマ切替 -->
<button id="theme-toggle-btn" class="btn theme-toggle-btn">
    <i id="theme-icon" class="bi <?= $theme === 'dark' ? 'bi-sun' : 'bi-moon' ?>"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/theme-toggle.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<!-- 文字数制御 -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const MAX_LENGTH = 100;

    const goalInput = document.getElementById('goal');
    const goalCount = document.getElementById('goal-count');
    const mileStoneInput = document.getElementById('mile_stone');
    const mileStoneCount = document.getElementById('mile-stone-count');
    const submitBtn = document.getElementById('submit-btn');

    const updateCount = (input, counter) => {
        const length = input.value.length;
        counter.textContent = length;
        if (length > MAX_LENGTH) {
            counter.classList.add('text-danger');
            return false;
        } else {
            counter.classList.remove('text-danger');
            return true;
        }
    };

    const checkForm = () => {
        submitBtn.disabled = !(
            updateCount(goalInput, goalCount) &&
            updateCount(mileStoneInput, mileStoneCount)
        );
    };

    goalInput.addEventListener('input', checkForm);
    mileStoneInput.addEventListener('input', checkForm);
    checkForm();
});
</script>

<!-- カレンダー（常時表示） -->
<script>
flatpickr("#calendar", {
    inline: true,
    dateFormat: "Y-m-d",
    defaultDate: document.getElementById('goal_date').value || null,
    onChange: function(selectedDates, dateStr) {
        document.getElementById('goal_date').value = dateStr;
    }
});
</script>

</body>

<footer>
    <?php include '../template/footer.php'; ?>
</footer>
</html>
