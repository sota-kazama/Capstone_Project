
 <?php
require_once './helpers/MemberDAO.php';

$mail_address = '';
$errs = [];

session_start();

if (!empty($_SESSION['member'])) {
    header('Location:index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail_address = $_POST['mail_address'] ?? '';
    $pass_word = $_POST['pass_word'] ?? '';

    if ($mail_address === '') {
        $errs['mail_address'] = 'メールアドレスを入力してください。';
    } elseif (!filter_var($mail_address, FILTER_VALIDATE_EMAIL)) {
        $errs['mail_address'] = 'メールアドレスの形式に誤りがあります。';
    }

    if ($pass_word === '') {
        $errs['pass_word'] = 'パスワードを入力してください。';
    }

    if (empty($errs)) {
        $memberDAO = new MemberDAO();
        $member = $memberDAO->get_member($mail_address, $pass_word);
        if ($member !== false) {
            session_regenerate_id(true);
            $_SESSION['member'] = $member;
            header('Location: index.php');
            exit;
        } else {
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/BaseDesignData.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/LoginDesign.css">
    <?php include 'template/header2.php'; ?>
</head>

<body class="login-body">

<div class="login-container">
    <form action="" method="POST" class="login-box">
        <h3 class="login-title">ログイン</h3>

        <?php if (!empty($errs) && isset($errs[0])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($errs[0], ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <div class="form-group">
            <label>メールアドレス</label>
            <input type="email" name="mail_address" class="form-control"
                   value="<?= htmlspecialchars($mail_address, ENT_QUOTES, 'UTF-8') ?>" required>
            <div class="error-text"><?= @$errs['mail_address'] ?></div>
        </div>

        <div class="form-group">
            <label>パスワード</label>
            <input type="password" name="pass_word" class="form-control" required>
            <div class="error-text"><?= @$errs['pass_word'] ?></div>
        </div>

        <div class="button-area">
            <input type="submit" value="ログイン" class="btn btn-primary login-btn">
        </div>
    </form>

    <div class="signup-box">
        <h5>初めての利用の方</h5>
        <p>ログインするには会員登録が必要です。</p>
        <a href="signup.php" class="btn btn-outline-secondary">新規会員登録はこちら</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
