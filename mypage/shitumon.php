<?php
require_once '../helpers/MemberDAO.php';
require_once '../helpers/ShitumonDAO.php'; // ShitumonDAOクラスのインクルード

session_start();

// 未ログインの場合、ログインページにリダイレクト
if (!isset($_SESSION['member'])) {
    header('Location: login.php');
    exit;
}

// テーマ（Cookie なければ light）
$theme = $_COOKIE['theme'] ?? 'light';

// MemberDAOインスタンスを生成して、ユーザー情報を取得
$member = $_SESSION['member']; // $_SESSION から Member オブジェクトを取得

// ShitumonDAOインスタンスを生成
$shitumonDAO = new ShitumonDAO();

// ユーザーIDを取得
$user_id = $member->user_id; // Member オブジェクトから user_id を取得

// 質問一覧を取得
$questions = $shitumonDAO->getAllByUser($user_id);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="../css/BaseDesignData.css" rel="stylesheet" />
    <link href="../css/side.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQueryの追加 -->
    <title>マイページ</title>
    <link id="theme-css" rel="stylesheet" href="../css_theme/<?= htmlspecialchars($theme) ?>.css" />
    <link href="../css_theme/toggle-button.css" rel="stylesheet" />
</head>

<body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">

<?php include '../template/header.php'; ?>

<div class="d-flex w-100 min-vh-100">
    <!-- サイドバー -->
    <aside class="d-none d-md-block">
        <?php include 'side.php'; ?>
    </aside>

    <!-- メインコンテンツ -->
    <main class="main-content container mt-4">
        <h1 class="mt-5">マイページ</h1>

        <section class="mt-4">
            <h2>あなたの投稿した質問</h2>
            <table class="table table-bordered table-striped mt-3">
                <thead class="table-light">
                    <tr>
                        <th>質問タイトル</th>
                        <th>質問内容</th>
                        <th>受付状態</th>
                        <th>投稿日時</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($questions)): ?>
                    <?php foreach ($questions as $q): 
                        $askedDate = (new DateTime($q->asked_date))->format('Y/m/d H:i');
                    ?>
                    <tr id="question-row-<?= $q->shitu_number ?>">
                        <td><a href="../Shitsumonbako/question_answer.php?shitu_number=<?= $q->shitu_number ?>"><?= htmlspecialchars($q->shitu_title) ?></a></td>
                        <td><?= htmlspecialchars($q->shitu_content) ?></td>
                        <td id="status-<?= $q->shitu_number ?>"><?= $q->reception_status == 1 ? '受付中' : '受付終了' ?></td>
                        <td><?= $askedDate ?></td>
                        <td>
                            <?php if ($q->reception_status == 1): ?>
                                <button class="btn btn-warning btn-sm end-reception-btn" data-shitu_number="<?= $q->shitu_number ?>">受付終了</button>
                            <?php else: ?>
                                <span class="text-muted">受付終了済み</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">投稿した質問はありません。</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
</div>

<!-- テーマ切替ボタン -->
<button id="theme-toggle-btn" class="btn theme-toggle-btn">
    <i id="theme-icon" class="bi <?= $theme === 'dark' ? 'bi-sun' : 'bi-moon' ?>"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/theme-toggle.js"></script>
<script>
$(document).ready(function() {
    // 受付終了ボタン
    $('.end-reception-btn').click(function() {
        const shitu_number = $(this).data('shitu_number');

        $.post('update_status.php', { action: 'end_reception', shitu_number }, function(response) {
            const data = JSON.parse(response);
            if (data.success) {
                alert('受付終了しました');
                // 成功したらページをリロード
                location.reload();
            } else {
                alert('受付終了に失敗しました: ' + (data.message || '未知のエラー'));
            }
        }).fail(function() {
            alert('通信に失敗しました。もう一度試してください。');
        });
    });
});

</script>

<footer>
    <?php include '../template/footer.php'; ?>
</footer>

</body>
</html>