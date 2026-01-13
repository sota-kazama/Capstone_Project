<?php
require_once '../helpers/MemberDAO.php';
require_once '../helpers/ShitumonDAO.php';

session_start();

// 未ログインならログインページへ
if (!isset($_SESSION['member'])) {
    header('Location: login.php');
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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />

    <link href="../css/BaseDesignData.css" rel="stylesheet" />
    <link href="../css/side.css" rel="stylesheet" />
    <link id="theme-css" rel="stylesheet" href="../css_theme/<?= htmlspecialchars($theme) ?>.css" />
    <link href="../css_theme/toggle-button.css" rel="stylesheet" />

    <title>質問箱の管理</title>
</head>

<body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">
<?php include '../template/header2.php'; ?>

<div class="d-flex w-100 min-vh-100">
    <?php include 'side.php'; ?>

    <main class="main-content flex-grow-1 p-4">
        <div class="d-flex align-items-center mb-3">
            <h1 class="m-0">質問箱の管理</h1>
        </div>

        <div class="alert alert-warning">
            ※ 不適切な質問以外は削除しないでください。
        </div>

        <div class="card p-4 mt-3">
            <h4>投稿された質問一覧</h4>

            <table class="table table-striped align-middle mt-3">
                <thead>
                    <tr>
                        <th>質問タイトル</th>
                        <th>質問内容</th>
                        <th>受付状態</th>
                        <th>投稿日時</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($questions) > 0): ?>
                    <?php foreach ($questions as $question): ?>
                        <?php
                            $askedDate = new DateTime($question->asked_date);
                            $formattedDate = $askedDate->format('Y/m/d H:i');
                        ?>
                        <tr id="question-row-<?= $question->shitu_number ?>">
                            <td>
                                <a href="../Shitsumonbako/question_answer.php?shitu_number=<?= $question->shitu_number ?>">
                                    <?= htmlspecialchars($question->shitu_title) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($question->shitu_content) ?></td>
                            <td>
                                <?= $question->reception_status == 1 ? '受付中' : '受付終了' ?>
                            </td>
                            <td><?= $formattedDate ?></td>
                            <td>
                                <button
                                    class="btn btn-sm btn-danger delete-question-btn"
                                    data-shitu_number="<?= $question->shitu_number ?>">
                                    削除
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">投稿された質問はありません。</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
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
