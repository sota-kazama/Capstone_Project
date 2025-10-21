<?php
require_once './helpers/MemberDAO.php';

// $errs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail_address = $_POST['mail_address'] ?? '';
    $pass_word = $_POST['pass_word'] ?? '';
    $pass_word2 = $_POST['pass_word2'] ?? '';
    $user_name = $_POST['user_name'] ?? '';

    $memberDAO = new MemberDAO();

    if (!filter_var($mail_address, FILTER_VALIDATE_EMAIL)) {
        $errs['mail_address'] = 'メールアドレスの形式が正しくありません。';
    } elseif ($memberDAO->email_exists($mail_address) === true) {
        $errs['mail_address'] = 'このメールアドレスはすでに登録されています。';
    }

    if (!preg_match('/\A.{4,}\Z/', $pass_word)) {
        $errs['pass_word'] = 'パスワードは4文字以上で入力してください。';
    } elseif ($pass_word != $pass_word2) {
        $errs['pass_word'] = 'パスワードが一致しません。';
    }

    if ($user_name === "") {
        $errs['user_name'] = 'ユーザー名を入力してください。';
    }

    if(empty($errs)) {
        $member = new Member();
        $member->mail_address = $mail_address;
        $member->pass_word = $pass_word;
        $member->user_name = $user_name;

        $memberDAO->insert($member);
        header('Location:signupEnd.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>新規会員登録</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="../css/sidebar.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/SignupDesign.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link href="css/BaseDesignData.css" rel="stylesheet" />
    <?php include 'template/header2.php'; ?>
    <?php include 'template/hamburger.html'; ?>
</head>

<body>
    <div class="page-container">
        <?php include 'template/side.php'; ?>

        <div class="signup-wrapper">

            <div class="signup-container">


                <h2>新規会員登録</h2>
                <p>以下の項目を入力し、登録ボタンをクリックしてください(*は必須)</p>


                <form action="signup.php" method="POST">
                    <table>

                        <tr>
                            <td>ユーザー名*</td>
                            <td><input type="text" placeholder="例)電子 太郎" name="user_name">
                                <span style="color: red"><?= @$errs['user_name'] ?></span>

                            </td>
                        </tr>
                        <tr>
                            <td>メールアドレス</td>
                            <td><input type="email" placeholder="例)aaa@aaa" name="mail_address">
                                <span style="color: red"><?= @$errs['mail_address'] ?></span>


                            </td>
                        </tr>
                        <tr>
                            <td>パスワード(4文字以上)*</td>
                            <td><input type="password" minlength="4" name="pass_word">
                                <span style="color: red"><?= @$errs['pass_word'] ?></span>


                            </td>
                        </tr>
                        <tr>
                            <td>パスワード(再入力)*</td>
                            <td><input type="password" name="pass_word2"></td>
                        </tr>

                        </tr>
                    </table>
                    <input type="submit" value="登録">
                </form>

                <div class="link">
                    <a href="login.php">すでにアカウントをお持ちの方はこちら</a>
                </div>
                

               
            </div>
              
        </div>
        
    </div>



</body>

<footer>
    <?php include 'template/footer.php'; ?>
</footer>

</html>