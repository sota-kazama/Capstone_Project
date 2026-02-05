<?php
require_once '../helpers/MemberDAO.php';
require_once '../helpers/ShitumonDAO.php';

session_start();

// 未ログインの場合はログインページへリダイレクト
if (!isset($_SESSION['member'])) {
    header('Location: ../auth/login.php');
    exit;
}

// 管理者チェック
$member = $_SESSION['member'];
$is_admin = false;
if (is_object($member) && isset($member->u_admin)) {
    $is_admin = ($member->u_admin == 1);
} elseif (is_array($member) && isset($member['u_admin'])) {
    $is_admin = ($member['u_admin'] == 1);
}
if (!$is_admin) {
    die('管理者権限がありません。');
}

// テーマ設定（cookieがなければlight）
$theme = $_COOKIE['theme'] ?? 'light';

// DAOインスタンス
$shitumonDAO = new ShitumonDAO();

// 質問一覧を取得
$questions = $shitumonDAO->getAll();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap & アイコン -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- カスタムCSS -->
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

                <?php if (!empty($questions)): ?>
                    <div class="list-group mt-3">
                        <?php foreach ($questions as $q): 
                            $askedDate = new DateTime($q->asked_date);
                            $formattedDate = $askedDate->format('Y/m/d H:i');
                        ?>
                            <div class="list-group-item d-flex justify-content-between align-items-start mb-2">

                                <div class="flex-grow-1 me-3">
                                    <div><strong><?= htmlspecialchars($q->shitu_title) ?></strong></div>

                                    <div class="text-muted small mb-1">
                                        <?= htmlspecialchars($q->shitu_content) ?>
                                    </div>

                                    <div class="text-muted small">
                                        受付状態: <?= $q->reception_status == 1 ? '受付中' : '受付終了' ?> |
                                        投稿日時: <?= $formattedDate ?>
                                    </div>

                                    <div class="mt-1">
                                        <a href="../Shitsumonbako/question_answer.php?shitu_number=<?= $q->shitu_number ?>"
                                           class="btn btn-sm btn-outline-primary">
                                            回答を見る
                                        </a>
                                    </div>
                                </div>

                                <!-- 削除ボタン -->
                                <div class="text-end">
                                    <form action="../Shitsumonbako/question_delete.php"
                                          method="post"
                                          onsubmit="return confirm('この質問を削除します。本当によろしいですか？');">
                                        <input type="hidden" name="shitu_number" value="<?= htmlspecialchars($q->shitu_number) ?>">
                                        <input type="hidden" name="return_to" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">削除</button>
                                    </form>
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

    <!-- テーマ切替ボタン -->
    <button id="theme-toggle-btn" class="btn btn-primary theme-toggle-btn">
        <i id="theme-icon" class="bi <?= $theme === 'dark' ? 'bi-sun' : 'bi-moon' ?>"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/theme-toggle.js"></script>

    <footer>
        <?php include '../template/footer.php'; ?>
    </footer>

</body>
</html>
