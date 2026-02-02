<?php
require_once '../helpers/ThreadBoardDAO.php';
require_once '../helpers/MemberDAO.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 未ログインならリダイレクト
if (!isset($_SESSION['member'])) {
    header('Location: ../auth/login.php');
    exit;
}

// テーマ取得
$theme = $_COOKIE['theme'] ?? 'light';

// Memberオブジェクト
$member = $_SESSION['member'];
$user_id = $member->user_id;

// DAO
$dao = new ThreadBoardDAO();

// GETでスレッド番号取得
$thread_number = $_GET['thread_number'] ?? null;
if (!$thread_number) {
    die('スレッドが指定されていません');
}

// スレッド情報取得
$thread = $dao->getThreadTitle((int)$thread_number);
if (!$thread) {
    die('スレッドが存在しません');
}

// 投稿一覧取得
$posts = $dao->getPostsByThread((int)$thread_number);

// 新規投稿処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['content'])) {
    $content = trim($_POST['content']);
    if ($content !== '') {
        $dao->insertPost((int)$thread_number, $user_id, $content);
        $dao->updateThreadTime((int)$thread_number);
        header("Location: board.php?thread_number={$thread_number}");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- CSS -->
    <link href="../css/BaseDesignData.css" rel="stylesheet">
    <link href="../css/side.css" rel="stylesheet">
    <link id="theme-css" href="../css_theme/<?= htmlspecialchars($theme) ?>.css" rel="stylesheet">
    <link href="../css_theme/toggle-button.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<title><?= htmlspecialchars($thread->thread_name) ?> - スレッド</title>


</head>
<body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">

<?php include __DIR__ . '/../template/header.php'; ?>

<div class="d-flex w-100 min-vh-100">
    <aside class="d-none d-md-block">
        <?php include __DIR__ . '/../template/side.php'; ?>
    </aside>

    <main class="main-content flex-grow-1 p-4">
        <h1 class="mb-4"><?= htmlspecialchars($thread->thread_name) ?></h1>

        <!-- 投稿一覧 -->
        <div class="mb-4">
            <?php if (!empty($posts)): ?>
                <?php foreach ($posts as $post): ?>
                    <?php
                        // 投稿者名取得
                        $postMember = (new MemberDAO())->getMemberById($post->user_id);
                        $userName = $postMember ? $postMember['user_name'] : '不明';
                        $postedAt = date('Y-m-d H:i', strtotime($post->created_ad));
                    ?>
                    <div class="post-card">
                        <div class="post-header">
                            <div class="post-user">
                                &gt;<?= $post->toukou_number ?> <?= htmlspecialchars($userName) ?>
                            </div>
                            <div class="post-date">
                                <?= $postedAt ?>
                            </div>
                        </div>
                        <div class="post-content">
                            <?= nl2br(htmlspecialchars($post->post_content)) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info">まだ投稿はありません。</div>
            <?php endif; ?>
        </div>

        <!-- 投稿ボタン -->
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#postModal">
            新規投稿
        </button>

        <!-- 投稿モーダル -->
        <div class="modal fade" id="postModal" tabindex="-1" aria-labelledby="postModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post">
                        <div class="modal-header">
                            <h5 class="modal-title" id="postModalLabel">新規投稿</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="閉じる"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">本文</label>
                                <textarea name="content" class="form-control" rows="4" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                            <button type="submit" class="btn btn-primary">投稿する</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </main>
</div>

<?php include __DIR__ . '/../template/footer.php'; ?>

<!-- テーマ切替 -->
<button id="theme-toggle-btn" class="btn btn-primary theme-toggle-btn">
    <i id="theme-icon" class="bi <?= $theme === 'dark' ? 'bi-sun' : 'bi-moon' ?>"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/theme-toggle.js"></script>
</body>
</html>
