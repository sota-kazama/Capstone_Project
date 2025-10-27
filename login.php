<?php
require_once './helpers/MemberDAO.php';

$errs = [];

session_start();

if(!empty($_SESSION['member'])) {
    header('Location:index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail_address = $_POST['mail_address'] ?? '';
    $pass_word = $_POST['pass_word'] ?? '';

    if($mail_address === ''){
        $errs[] = 'メールアドレスを入力してください。';
    } else if(!filter_var($mail_address,FILTER_VALIDATE_EMAIL)){
        $errs[] = 'メールアドレスの形式に誤りがあります。';
    }

    if($pass_word === ''){
        $errs[] = 'パスワードを入力してください。';
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
<html>

<head>
    <meta charset="utf-8" />
    <link href="css/BaseDesignData.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/LoginDesign.css">
    <title>ログイン</title>

</head>

<body>
        <?php include 'template/header2.php'; ?>
<form action="" method="POST">
    <table id="LoginTable" class="box">
        <tr>
            <th colspan="2">ログイン</th>
        </tr>
        <tr>
            <td>メールアドレス</td>
            <td>
                <input type="email" name="mail_address" required autofocus />
            </td>
        </tr>
        <tr>
            <td>パスワード</td>
            <td>
                <input type="password" name="pass_word" required autofocus/>
            </td>
        </tr>
        <tr>
            <td>
                <input type="submit" value="ログイン" />
            </td>
        </tr>
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($errs)): ?>
        <tr>
            <td colspan="2">
                <?php foreach ($errs as $e): ?>
                    <span style="color:red"><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></span><br>
                <?php endforeach; ?>
            </td>
        </tr>
        <?php endif; ?>

    </table>
</form>
                    
    <table class="box">
        <tr>
            <th>初めての利用の方</th>
        </tr>
        <tr>
            <td>ログインするには会員登録が必要です。</td>
        </tr>
        <tr>
            <td><a href="signup.php">新規会員登録はこちら</a></td>
        </tr>
    </table>
</body>
</html>

