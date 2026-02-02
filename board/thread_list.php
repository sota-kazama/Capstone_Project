<?php
require_once './helpers/ThreadBoardDAO.php';
require_once './helpers/config.php';
require_once './helpers/MemberDAO.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ===== Memberクラスをページ内で定義（unserialize対策） =====
if (!class_exists('Member')) {
    class Member {
        public int $user_id;
        public string $user_name;
        public string $email; // 必要に応じて
    }
}

// 未ログインならリダイレクト
if (!isset($_SESSION['member'])) {
    header('Location: auth/login.php');
    exit;
}

$member = $_SESSION['member'];
$user_id = $member->user_id;

// ===== テーマ取得 =====
$theme = $_COOKIE['theme'] ?? 'light';

// ===== DAOインスタンス =====
$dao = new ThreadBoardDAO();

// ===== 検索・新規作成処理 =====
$threads = [];
$searchKeyword = '';
$newThreadError = '';

// 検索
if (isset($_GET['search']) && trim($_GET['search']) !== '') {
    $searchKeyword = trim($_GET['search']);
    $threads = $dao->searchThreads($searchKeyword);
} else {
    // 検索がない場合はすべて取得
    $threads = $dao->getAllThreads();
}

// 新規作成
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_thread_name'], $_POST['new_post_content'])) {
    $newName    = trim($_POST['new_thread_name']);
    $newContent = trim($_POST['new_post_content']);

    if ($newName === '' || $newContent === '') {
        $newThreadError = 'スレッド名と最初の投稿内容は必須です。';
    } else {
        // 1. スレッド作成
        $dao->insertThread($newName);

        // 2. 作成したスレッド番号を取得（最新のスレッド）
        $allThreads = $dao->getAllThreads();
        $newThread = $allThreads[0]; // update_at DESC で最新が先頭

        // 3. 最初の投稿を追加
        $dao->insertPost($newThread->thread_number, $user_id, $newContent);

        // リロード
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <link href="./css/BaseDesignData.css" rel="stylesheet">
    <link href="./css/side.css" rel="stylesheet">
    <link id="theme-css" rel="stylesheet" href="./css_theme/<?= htmlspecialchars($theme) ?>.css">
    <link href="./css_theme/toggle-button.css" rel="stylesheet">

    <title>スレッド一覧</title>
</head>
<body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">

<?php include './template/header.php'; ?>

<div class="d-flex w-100 min-vh-100">
    <div class="d-none d-md-block">
        <?php include './template/side.php'; ?>
    </div>

    <main class="main-content flex-grow-1 p-4">
        <h1 class="mb-4">スレッド一覧</h1>

        <!-- 検索フォーム -->
        <form class="d-flex mb-4" method="get">
            <input type="text" name="search" class="form-control me-2" placeholder="スレッド名で検索" value="<?= htmlspecialchars($searchKeyword) ?>">
            <button class="btn btn-outline-primary" type="submit">検索</button>
        </form>

        <!-- 新規作成ボタン -->
        <button class="btn btn-success mb-4" type="button" data-bs-toggle="collapse" data-bs-target="#newThreadForm" aria-expanded="false">
            新規スレッド作成
        </button>

        <!-- 新規作成フォーム -->
        <div class="collapse mb-4" id="newThreadForm">
            <div class="card p-4">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">スレッド名</label>
                        <input type="text" name="new_thread_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">最初の投稿内容</label>
                        <textarea name="new_post_content" class="form-control" rows="3" required></textarea>
                    </div>
                    <?php if ($newThreadError): ?>
                        <div class="text-danger mb-2"><?= htmlspecialchars($newThreadError) ?></div>
                    <?php endif; ?>
                    <button class="btn btn-primary">作成する</button>
                </form>
            </div>
        </div>

        <!-- スレッド一覧 -->
        <?php if (!empty($threads)): ?>
            <?php foreach ($threads as $thread): ?>
                <a href="board.php?thread_number=<?= $thread->thread_number ?>" class="text-decoration-none text-reset">
                    <div class="card mb-3 p-3 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1"><?= htmlspecialchars($thread->thread_name) ?></h5>
                                <small class="text-muted">
                                    作成日：<?= date('Y-m-d', strtotime($thread->created_ad)) ?>
                                </small>
                            </div>
                            <i class="bi bi-chevron-right fs-4 text-muted"></i>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">
                スレッドはまだ作成されていません。
            </div>
        <?php endif; ?>
    </main>
</div>

<?php include './template/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
