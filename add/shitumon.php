<?php
require_once '../helpers/MemberDAO.php';
require_once '../helpers/ShitumonDAO.php';

session_start();

// 未ログインの場合
if (!isset($_SESSION['member'])) {
    header('Location: ../auth/login.php');
    exit;
}

// テーマ
$theme = $_COOKIE['theme'] ?? 'light';

// ログインユーザー
$member = $_SESSION['member'];

// DAO
$shitumonDAO = new ShitumonDAO();

// 質問一覧取得
$questions = $shitumonDAO->getAll();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="../css_theme/base.css" rel="stylesheet">
<link href="../css_theme/side.css" rel="stylesheet">
<link id="theme-css" href="../css_theme/<?= htmlspecialchars($theme) ?>.css" rel="stylesheet">
<link href="../css_theme/toggle-button.css" rel="stylesheet">
<title>質問箱の管理</title>
</head>

<body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">
<?php include '../template/header2.php'; ?>

<div class="d-flex w-100 min-vh-100">
<?php include 'side.php'; ?>

<main class="main-content flex-grow-1 p-4">
<h1>質問箱の管理</h1>

<div class="alert alert-warning">
    ※ 不適切な質問以外は削除しないでください。
</div>

<div class="card p-4 mt-3">
<h4>投稿された質問一覧</h4>

<?php if (count($questions) > 0): ?>
<div class="list-group mt-3">
<?php foreach ($questions as $q): ?>
<?php
$askedDate = new DateTime($q->asked_date);
$formattedDate = $askedDate->format('Y/m/d H:i');
?>
<div class="list-group-item d-flex justify-content-between align-items-start mb-2">
    <div class="flex-grow-1 me-3">
        <div><strong><?= htmlspecialchars($q->shitu_title) ?></strong></div>
        <div class="text-muted small mb-1"><?= htmlspecialchars($q->shitu_content) ?></div>
        <div class="text-muted small">
            受付状態: <?= $q->reception_status == 1 ? '受付中' : '受付終了' ?> |
            投稿日時: <?= $formattedDate ?>
        </div>
        <div>
            <a href="../Shitsumonbako/question_answer.php?shitu_number=<?= $q->shitu_number ?>" class="btn btn-sm btn-outline-primary mt-1">
                回答を見る
            </a>
        </div>
    </div>
    <div class="text-end">
        <button class="btn btn-sm btn-danger delete-question-btn" data-shitu_number="<?= $q->shitu_number ?>">
            削除
        </button>
    </div>
</div>
<?php endforeach; ?>
</div>
<?php else: ?>
<div class="text-center p-3 text-muted">
    投稿された質問はありません。
</div>
<?php endif; ?>
</div>

</main>
</div>

<!-- テーマ切替 -->
<button id="theme-toggle-btn" class="btn btn-primary theme-toggle-btn">
<i id="theme-icon" class="bi <?= $theme === 'dark' ? 'bi-sun' : 'bi-moon' ?>"></i>
</button>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/theme-toggle.js"></script>

<!-- 削除処理 -->
<script>
$('.delete-question-btn').click(function () {
    const shitu_number = $(this).data('shitu_number');

    if (!confirm('この質問を削除します。本当によろしいですか？')) {
        return;
    }

    $.ajax({
        type: 'POST',
        url: 'update_status.php',
        dataType: 'json',
        data: {
            action: 'delete',
            shitu_number: shitu_number
        },
        success: function (data) {
            if (data.success) {
                alert('質問を削除しました');
                location.reload();
            } else {
                alert('削除に失敗しました');
            }
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            alert('通信に失敗しました');
        }
    });
});
</script>

<footer>
<?php include '../template/footer.php'; ?>
</footer>
</body>
</html>
