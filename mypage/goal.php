<?php
require_once '../helpers/MemberDAO.php';
require_once '../helpers/u_goalsDAO.php';

session_start();

// 未ログインチェック
if (!isset($_SESSION['member'])) {
    header('Location: login.php');
    exit;
}

$member        = $_SESSION['member'];
$error_message = null;
$mode          = $_GET['mode'] ?? 'list'; // list / add / edit
$GoalsDAO      = new GoalsDAO();

/* =========================
   POST処理
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 削除処理
    if (isset($_POST['delete_goal_id'])) {
        $delete_goal_id = (int)$_POST['delete_goal_id'];
        $result         = $GoalsDAO->delete($delete_goal_id);

        if ($result) {
            header('Location: goal.php');
            exit;
        } else {
            $error_message = '削除に失敗しました。';
            $mode          = 'list';
        }

    // 追加 / 更新処理
    } else {
        $goal       = $_POST['goal'] ?? '';
        $mile_stone = $_POST['mile_stone'] ?? '';
        $goal_date  = $_POST['goal_date'] ?? '';
        $goal_id    = $_POST['goal_id'] ?? null;

        if (empty($goal)) {
            $error_message = '目標を入力してください。';
            $mode          = $goal_id ? 'edit' : 'add';
        } elseif (mb_strlen($goal) > 100 || mb_strlen($mile_stone) > 100) {
            $error_message = '文字数は100文字以内で入力してください。';
            $mode          = $goal_id ? 'edit' : 'add';
        } else {
            if ($goal_id) {
                // 更新処理
                $result = $GoalsDAO->update(
                    $goal_id,
                    $goal,
                    $mile_stone,
                    $goal_date,
                    null
                );
            } else {
                // 新規登録
                $result = $GoalsDAO->insert(
                    $member->user_id,
                    $goal,
                    $mile_stone,
                    $goal_date
                );
            }

            if ($result) {
                header('Location: goal.php');
                exit;
            } else {
                $error_message = '保存に失敗しました。';
                $mode          = $goal_id ? 'edit' : 'add';
            }
        }
    }
}

/* =========================
   目標取得（一覧用 or 編集用）
========================= */
if ($mode === 'list') {
    $goals = $GoalsDAO->getGoalsByUserId($member->user_id);
} elseif ($mode === 'edit' && isset($_GET['goal_id'])) {
    $goal = $GoalsDAO->getGoalByGoalId((int)$_GET['goal_id']);
} else {
    $goal = null;
}

$theme = $_COOKIE['theme'] ?? 'light';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="../css/BaseDesignData.css" rel="stylesheet">
    <link href="../css/side.css" rel="stylesheet">
    <link id="theme-css" rel="stylesheet" href="../css_theme/<?= htmlspecialchars($theme) ?>.css">
    <link href="../css_theme/toggle-button.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <title>目標管理</title>
    <?php include '../template/header.php'; ?>
</head>

<body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">

<div class="d-flex w-100 min-vh-100">

    <!-- サイドバー -->
    <div class="d-none d-md-block">
        <?php include 'side.php'; ?>
    </div>

    <!-- メインコンテンツ -->
    <main class="main-content flex-grow-1 p-4">

        <!-- 一覧表示 -->
        <?php if ($mode === 'list'): ?>
            <h1 class="mt-5">目標一覧</h1>

            <?php if (empty($goals)): ?>
                <div class="alert alert-info">まだ目標が登録されていません。</div>
                <a href="?mode=add" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> 新規追加
                </a>
            <?php else: ?>
                <table class="table mt-3">
                    <thead>
                        <tr>
                            <th>目標</th>
                            <th>マイルストーン</th>
                            <th>目標日</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($goals as $goal): ?>
                            <tr>
                                <td><?= htmlspecialchars($goal->goal) ?></td>
                                <td><?= htmlspecialchars($goal->mile_stone) ?></td>
                                <td><?= htmlspecialchars($goal->goal_date) ?></td>
                                <td>
                                    <a href="?mode=edit&goal_id=<?= $goal->goal_id ?>" class="btn btn-warning btn-sm">編集</a>
                                    <form method="post" style="display:inline;" onsubmit="return confirm('本当に削除しますか？');">
                                        <input type="hidden" name="delete_goal_id" value="<?= $goal->goal_id ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">削除</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <a href="?mode=add" class="btn btn-primary mt-3">
                    <i class="bi bi-plus-circle"></i> 新規追加
                </a>
            <?php endif; ?>

        <!-- 新規 / 更新フォーム -->
        <?php else: ?>
            <h1 class="mt-5"><?= $mode === 'edit' ? '目標更新' : '目標登録' ?></h1>

            <div class="card p-4 mt-3">

                <?php if ($error_message): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
                <?php endif; ?>

                <form method="post">
                    <?php if ($mode === 'edit' && $goal): ?>
                        <input type="hidden" name="goal_id" value="<?= htmlspecialchars($goal->goal_id) ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">目標 <span class="text-danger">*</span></label>
                        <input type="text" name="goal" id="goal" class="form-control"
                               value="<?= htmlspecialchars($_POST['goal'] ?? ($goal->goal ?? '')) ?>" required>
                        <div class="text-end small"><span id="goal-count">0</span> / 100 文字</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">マイルストーン</label>
                        <input type="text" name="mile_stone" id="mile_stone" class="form-control"
                               value="<?= htmlspecialchars($_POST['mile_stone'] ?? ($goal->mile_stone ?? '')) ?>">
                        <div class="text-end small"><span id="mile-stone-count">0</span> / 100 文字</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">目標日</label>
                        <input type="hidden" name="goal_date" id="goal_date"
                               value="<?= htmlspecialchars($_POST['goal_date'] ?? ($goal->goal_date ?? '')) ?>">
                        <div id="calendar"></div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn <?= $mode === 'edit' ? 'btn-warning' : 'btn-primary' ?>">
                            <?= $mode === 'edit' ? '更新' : '登録' ?>
                        </button>
                        <a href="goal.php" class="btn btn-secondary">戻る</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>

    </main>
</div>

<!-- テーマ切替ボタン -->
<button id="theme-toggle-btn" class="btn theme-toggle-btn">
    <i id="theme-icon" class="bi <?= $theme === 'dark' ? 'bi-sun' : 'bi-moon' ?>"></i>
</button>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/theme-toggle.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<?php if ($mode !== 'list'): ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const MAX_LENGTH = 100;
    const goalInput = document.getElementById('goal');
    const goalCount = document.getElementById('goal-count');
    const mileStoneInput = document.getElementById('mile_stone');
    const mileStoneCount = document.getElementById('mile-stone-count');
    const submitBtn = document.querySelector('button[type="submit"]');

    const updateCount = (input, counter) => {
        const length = input.value.length;
        counter.textContent = length;
        return length <= MAX_LENGTH;
    };

    const checkForm = () => {
        submitBtn.disabled = !(updateCount(goalInput, goalCount) && updateCount(mileStoneInput, mileStoneCount));
    };

    goalInput.addEventListener('input', checkForm);
    mileStoneInput.addEventListener('input', checkForm);
    checkForm();
});

flatpickr("#calendar", {
    inline: true,
    dateFormat: "Y-m-d",
    defaultDate: document.getElementById('goal_date').value || null,
    onChange: function(selectedDates, dateStr) {
        document.getElementById('goal_date').value = dateStr;
    }
});
</script>
<?php endif; ?>

<footer>
    <?php include '../template/footer.php'; ?>
</footer>
</body>
</html>
