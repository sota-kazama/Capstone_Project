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
    <div class="d-none d-md-block">
        <?php include 'side.php'; ?>
    </div>

    <!-- メインコンテンツ -->
    <main class="main-content container mt-4">
        <h1 class="mt-5">マイページ</h1>

        <!-- ユーザーの質問一覧表示 -->
        <div class="col-md-12">
            <h2>あなたの投稿した質問</h2>

            <!-- 質問一覧 -->
            <table class="table table-bordered table-striped">
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
                    <?php
                    // 質問が存在する場合
                    if (count($questions) > 0):
                        foreach ($questions as $question):
                            // 投稿日時のフォーマットを変更
                            $askedDate = new DateTime($question->asked_date);
                            $formattedDate = $askedDate->format('Y/m/d H:i');
                    ?>
                    <tr id="question-row-<?= $question->shitu_number ?>">
                        <td>
                            <!-- 質問タイトルをクリックすると、詳細ページに飛べるリンク -->
                            <a href="question_answer.php?shitu_number=<?= $question->shitu_number ?>">
                                <?= htmlspecialchars($question->shitu_title) ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($question->shitu_content) ?></td>
                        <td id="status-<?= $question->shitu_number ?>">
                            <?= $question->reception_status == 1 ? '受付中' : '受付終了' ?>
                        </td>
                        <td><?= $formattedDate ?></td>
                        <td>
                            <!-- 受付終了ボタン -->
                            <?php if ($question->reception_status == 1): ?>
                                <button class="btn btn-warning end-reception-btn" data-shitu_number="<?= $question->shitu_number ?>">受付終了</button>
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
        </div>
    </main>
</div>

<!-- テーマ切替ボタン -->
<button id="theme-toggle-btn" class="btn theme-toggle-btn">
    <i id="theme-icon" class="bi <?= $theme === 'dark' ? 'bi-sun' : 'bi-moon' ?>"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/theme-toggle.js"></script>

<script>
// 受付終了ボタンのクリックイベント
$('.end-reception-btn').click(function() {
    const shitu_number = $(this).data('shitu_number');
    
    $.ajax({
        type: 'POST',
        url: 'update_status.php',
        data: {
            action: 'end_reception',
            shitu_number: shitu_number
        },
        success: function(response) {
            // レスポンスをJSONとしてパース
            const data = JSON.parse(response);
            if (data.success) {
                // 受付終了が成功したら、受付状態を更新
                $('#status-' + shitu_number).text('受付終了');
                alert('受付終了しました');
            } else {
                // 処理失敗のメッセージ
                alert('受付終了に失敗しました: ' + (data.message || '未知のエラー'));
            }
        },
        error: function(xhr, status, error) {
            // AJAX通信自体に失敗した場合
            alert('通信に失敗しました。もう一度試してください。');
        }
    });
});
</script>

</body>

<!-- フッター部分 -->
<footer>
    <?php include '../template/footer.php'; ?>
</footer>
</html>
