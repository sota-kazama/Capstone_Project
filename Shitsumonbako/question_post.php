<?php
require_once '../helpers/DAO.php';
require_once '../helpers/MemberDAO.php';
// セッションを開始して、ログインユーザーの情報を取得
session_start();
$member = $_SESSION['member'] ?? null;

// ユーザーがログインしていない場合、エラーメッセージを表示してリダイレクト
if (!$member || !isset($member->user_id)) {
    $_SESSION['error'] = 'ログインが必要です。';
    header('Location: login.php'); // ログインページにリダイレクト
    exit;
}

$user_id = $member->user_id; // user_idをオブジェクトのプロパティとして取得

// q_categories から分野一覧を取得
$dbh = DAO::get_db_connect();
$stmt = $dbh->query("SELECT area_number, area_name FROM q_categories ORDER BY area_name ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title>質問投稿</title>
</head>
<body>
    <?php include '../template/header.php'; ?>
<div class="d-flex w-100 min-vh-100">
    <div class="d-none d-md-block">
        <?php include '../template/side.php'; ?>
    </div>

    <main class="main-content p-4">
        <h1 class="mb-4"><i class="bi bi-chat-dots"></i> 質問投稿</h1>

        <?php
        // セッションメッセージの表示
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($_SESSION['error']) . '</div>';
            unset($_SESSION['error']);
        }
        ?>

        <form action="question_post_process.php" method="post" class="needs-validation" novalidate>
            <!-- user_id を隠しフィールドとして追加 -->
            <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">

            <!-- 質問タイトル -->
            <div class="mb-3">
                <label for="shitu_title" class="form-label">質問タイトル</label>
                <input type="text" class="form-control" id="shitu_title" name="shitu_title" placeholder="例：PHP" required>
                <div class="invalid-feedback">タイトルを入力してください。</div>
            </div>

            <!-- 分野選択 -->
            <div class="mb-3">
                <label for="area_number" class="form-label">分野</label>
                <select class="form-select" id="area_number" name="area_number" required>
                    <option value="">選択してください</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat['area_number']) ?>">
                            <?= htmlspecialchars($cat['area_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">分野を選択してください。</div>
            </div>

            <!-- 質問内容 -->
            <div class="mb-3">
                <label for="shitu_content" class="form-label">質問内容</label>
                <textarea class="form-control" id="shitu_content" name="shitu_content" rows="6" placeholder="質問内容を詳しく記載してください。" required></textarea>
                <div class="invalid-feedback">質問内容を入力してください。</div>
            </div>

            <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> 投稿する</button>
            <button type="reset" class="btn btn-secondary">リセット</button>
        </form>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(() => {
    "use strict";
    const forms = document.querySelectorAll(".needs-validation");
    Array.from(forms).forEach((form) => {
        form.addEventListener("submit", (event) => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add("was-validated");
        }, false);
    });
})();
</script>

<footer>
    <?php include '../template/footer.php'; ?>
</footer>
</body>
</html>
