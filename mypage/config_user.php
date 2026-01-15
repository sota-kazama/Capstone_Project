<?php
require_once '../helpers/MemberDAO.php';

session_start();

// 未ログインチェック
if (!isset($_SESSION['member'])) {
    header('Location: ../auth/login.php');
    exit;
}

$member = $_SESSION['member'];

// テーマ
$theme = $_COOKIE['theme'] ?? 'light';

// メッセージ
$message = '';
$error   = '';

// ユーザーID取得（object / array 両対応）
$user_id = null;
if ($member !== null) {
    if (is_object($member) && isset($member->user_id)) {
        $user_id = $member->user_id;
    } elseif (is_array($member) && isset($member['user_id'])) {
        $user_id = $member['user_id'];
    }
}

if ($user_id === null) {
    header('Location: ../login.php');
    exit;
}

$dao = new MemberDAO();

// ユーザー情報取得
try {
    $userData = $dao->getMemberById($user_id);
} catch (Exception $e) {
    $error = 'ユーザー情報取得中にエラーが発生しました。';
}

// POST処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $user_name    = $_POST['user_name'] ?? null;
        $mail_address = $_POST['mail_address'] ?? null;
        $pass_word    = $_POST['pass_word'] ?? null;

        $password_hashed = !empty($pass_word)
            ? password_hash($pass_word, PASSWORD_DEFAULT)
            : null;

        $success = $dao->updateMemberSelf(
            $user_id,
            $user_name,
            $mail_address,
            $password_hashed
        );

        if ($success) {
            $message = 'プロフィールを更新しました。';
            $updated = $dao->getMemberById($user_id);
            $_SESSION['member'] = $updated;
            $userData = $updated;
        } else {
            $error = 'プロフィール更新に失敗しました。';
        }
    } catch (Exception $e) {
        $error = '更新中にエラーが発生しました。';
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- CSS -->
    <link href="../css/BaseDesignData.css" rel="stylesheet">
    <link href="../css/side.css" rel="stylesheet">
    <link id="theme-css" rel="stylesheet" href="../css_theme/<?= htmlspecialchars($theme) ?>.css">
    <link href="../css_theme/toggle-button.css" rel="stylesheet">

    <title>プロフィール編集</title>
</head>

<body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">

<!-- ヘッダー -->
<?php include '../template/header.php'; ?>

<div class="d-flex w-100 min-vh-100">

    <!-- サイドバー -->
    <div class="d-none d-md-block">
        <?php include 'side.php'; ?>
    </div>

    <!-- メインコンテンツ -->
    <main class="main-content flex-grow-1 p-4">

        <div class="d-flex align-items-center mb-3">
            <h1 class="m-0">プロフィール編集</h1>
        </div>

        <!-- メッセージ -->
        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- 登録情報フォーム -->
        <div class="card p-4 mt-3">
            <h4>登録情報</h4>

            <form method="POST" class="mt-3">

                <div class="mb-3">
                    <label class="form-label">ユーザー名</label>
                    <input
                        type="text"
                        class="form-control"
                        name="user_name"
                        value="<?= htmlspecialchars($userData['user_name']) ?>"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">メールアドレス</label>
                    <input
                        type="email"
                        class="form-control"
                        name="mail_address"
                        value="<?= htmlspecialchars($userData['mail_address']) ?>"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">パスワード（変更時のみ）</label>
                    <input
                        type="password"
                        class="form-control"
                        name="pass_word"
                        placeholder="変更する場合のみ入力"
                    >
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> 更新
                </button>

            </form>
        </div>

    </main>
</div>

<!-- テーマ切替 -->
<button id="theme-toggle-btn" class="btn btn-primary theme-toggle-btn">
    <i id="theme-icon" class="bi <?= $theme === 'dark' ? 'bi-sun' : 'bi-moon' ?>"></i>
</button>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/theme-toggle.js"></script>

<!-- フッター -->
<footer>
    <?php include '../template/footer.php'; ?>
</footer>

</body>
</html>
