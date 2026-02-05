<?php
require_once '../helpers/MemberDAO.php';
require_once '../helpers/ShitumonDAO.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$dao = new ShitumonDAO();

// テーマ
$theme = $_COOKIE['theme'] ?? 'light';

// GET
$shitu_number = $_GET['shitu_number'] ?? null;
if (!$shitu_number) {
    die("質問番号が指定されていません。");
}

// 質問取得
$q = $dao->getByNumber((int)$shitu_number);
if (!$q) {
    die("指定された質問が見つかりません。");
}

// 受付終了判定
$is_ended = $q->reception_status == 0;

// 回答一覧
$answers = $dao->getAnswers((int)$shitu_number);

// --------------------
// ログインユーザー取得
// --------------------
$member = $_SESSION['member'] ?? null;
$login_user_id = null;

if (is_object($member) && isset($member->user_id)) {
    $login_user_id = $member->user_id;
} elseif (is_array($member) && isset($member['user_id'])) {
    $login_user_id = $member['user_id'];
}

// 投稿者本人か？
$is_owner = (
    $login_user_id !== null &&
    $q->user_id !== null &&
    (int)$login_user_id === (int)$q->user_id
);
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
    <link
        id="theme-css"
        href="../css_theme/<?= htmlspecialchars($theme) ?>.css"
        rel="stylesheet"
    >
    <link href="../css_theme/toggle-button.css" rel="stylesheet">
    <title>質問詳細</title>
</head>
<body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">

    <?php include '../template/header.php'; ?>

    <div class="d-flex w-100 min-vh-100">
        <div class="d-none d-md-block">
            <?php include '../template/side.php'; ?>
        </div>

        <main class="main-content flex-grow-1 p-4">
            <div class="d-flex align-items-center mb-3">
                <h1 class="m-0"><i class="bi bi-chat-dots"></i> 質問詳細</h1>
            </div>

            <!-- 質問内容 -->
            <div class="card p-4 mb-4">
                <h3 class="card-title"><?= htmlspecialchars($q->shitu_title) ?></h3>
                <p><?= nl2br(htmlspecialchars($q->shitu_content)) ?></p>
                <p class="text-muted mt-3">
                    投稿日：
                    <?php
                        if (!empty($q->update_at)) {
                            echo date("Y-m-d H:i:s", strtotime($q->update_at));
                        } elseif (!empty($q->asked_date)) {
                            echo date("Y-m-d H:i:s", strtotime($q->asked_date));
                        } else {
                            echo '不明';
                        }
                    ?>
                </p>
            </div>

            <!-- 回答一覧 -->
            <div class="card p-4 mb-4">
                <h4>回答</h4>
                <?php if (empty($answers)): ?>
                    <div class="alert alert-info mt-2">まだ回答はありません。</div>
                <?php else: ?>
                    <?php foreach ($answers as $a): ?>
                        <div class="card mb-2 shadow-sm p-3">
                            <p><?= nl2br(htmlspecialchars($a->ans_content)) ?></p>
                            <small class="text-muted">
                                投稿日: <?= !empty($a->update_at) ? date("Y-m-d H:i:s", strtotime($a->update_at)) : ($a->answer_date ?? '不明') ?>
                            </small>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- 回答フォーム（受付中のみ） -->
            <?php if (!$is_ended): ?>
            <div class="card p-4 mb-4">
                <h5>回答する</h5>
                <form action="question_answer_process.php" method="post">
                    <input type="hidden" name="shitu_number" value="<?= htmlspecialchars($shitu_number) ?>" />
                    <div class="mb-3">
                        <textarea class="form-control" name="ans_content" rows="4" placeholder="回答内容を入力してください" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> 回答する</button>
                </form>
            </div>
            <?php else: ?>
                <div class="alert alert-secondary">この質問は受付終了のため、回答できません。</div>
            <?php endif; ?>


            <!-- 一覧表示 -->
            <div class="d-flex gap-2 mt-3">
            <a href="question_list.php" class="btn btn-secondary">一覧に戻る</a>

            <!-- 削除部分 -->
            <?php if (!$is_ended && $is_owner): ?>
                <form action="question_delete.php" method="post"
                    onsubmit="return confirm('本当に質問を削除しますか？');" class="m-0">
                    <input type="hidden" name="shitu_number" value="<?= htmlspecialchars($shitu_number) ?>">
                    <input type="hidden" name="return_to" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> 質問を削除
                    </button>
                </form>
            <?php endif; ?>
</div>

        </main>
    </div>

    <!-- テーマ切替ボタン -->
    <button id="theme-toggle-btn" class="btn btn-primary theme-toggle-btn">
        <i id="theme-icon" class="bi <?= $theme === 'dark' ? 'bi-sun' : 'bi-moon' ?>"></i>
    </button>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/theme-toggle.js"></script>

    <!-- フッター -->
    <footer>
        <?php include '../template/footer.php'; ?>
    </footer>

</body>
</html>
