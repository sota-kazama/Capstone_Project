<?php
// ヘルパーファイルのパスが相対パスで正しいか確認
require_once '../helpers/MemberDAO.php';
$theme = $_COOKIE['theme'] ?? 'light';

$mail_address = '';
$errs = [];

// セッションを開始
session_start();

// 既にログインしている場合はindex.phpへリダイレクト
if (!empty($_SESSION['member'])) {
    header('Location:index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail_address = $_POST['mail_address'] ?? '';
    $pass_word = $_POST['pass_word'] ?? '';

    // メールアドレスのバリデーション
    if ($mail_address === '') {
        $errs['mail_address'] = 'メールアドレスを入力してください。';
    } elseif (!filter_var($mail_address, FILTER_VALIDATE_EMAIL)) {
        $errs['mail_address'] = 'メールアドレスの形式に誤りがあります。';
    }

    // パスワードのバリデーション
    if ($pass_word === '') {
        $errs['pass_word'] = 'パスワードを入力してください。';
    }

    // バリデーションエラーがなければログイン処理を実行
    if (empty($errs)) {
        $memberDAO = new MemberDAO();

        // MemberDAO::get_member を呼び出す
        // ※このメソッド（前回修正済み）の内部で、認証成功時に
        //   自動的に最終アクセス日(access_date)を更新する処理が実行されます。
        $member = $memberDAO->get_member($mail_address, $pass_word);

        if ($member !== false) {
            // ログイン成功
            session_regenerate_id(true);
            $_SESSION['member'] = $member;
            header('Location: ../index.php');
            exit;
        } else {
            // ログイン失敗
            $errs[] = 'メールアドレスまたはパスワードに誤りがあります。';
        }
    }
}
?>


<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8" />
        <title>ログイン</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
        <link href="../css_theme/base.css" rel="stylesheet">
        <link rel="stylesheet" href="../css_theme/LoginDesign.css" />
        <?php include '../template/header2.php'; ?>
        <link id="theme-css" rel="stylesheet" href="../css_theme/<?= htmlspecialchars($theme) ?>.css" />
        <link href="../css_theme/toggle-button.css" rel="stylesheet" />

    </head>
    <body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">

<div class="d-flex justify-content-center align-items-center min-vh-100">
    <main class="w-100" style="max-width: 420px;">
        <div class="card p-4 shadow-sm">
            <h3 class="text-center mb-4">ログイン</h3>

            <?php if (!empty($errs) && isset($errs[0])): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($errs[0], ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">メールアドレス</label>
                    <input
                        type="email"
                        name="mail_address"
                        class="form-control"
                        value="<?= htmlspecialchars($mail_address, ENT_QUOTES, 'UTF-8') ?>"
                        required
                    >
                    <div class="text-danger small"><?= $errs['mail_address'] ?? '' ?></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">パスワード</label>
                    <input type="password" name="pass_word" class="form-control" required>
                    <div class="text-danger small"><?= $errs['pass_word'] ?? '' ?></div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        ログイン
                    </button>
                </div>
            </form>
        </div>

        <div class="card mt-3 p-3 text-center">
            <h6>初めての利用の方</h6>
            <p class="mb-2 small">ログインするには会員登録が必要です。</p>
            <a href="signup.php" class="btn btn-outline-secondary btn-sm">
                新規会員登録はこちら
            </a>
        </div>
    </main>
</div>

<button id="theme-toggle-btn" class="btn btn-primary theme-toggle-btn">
    <i id="theme-icon" class="bi <?= $theme === 'dark' ? 'bi-sun' : 'bi-moon' ?>"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/theme-toggle.js"></script>
</body>

<footer>
<?php include '../template/footer.php'; ?>
</footer>
</html>