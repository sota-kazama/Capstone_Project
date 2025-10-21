<?php
require_once './helpers/MemberDAO.php';

$mail_address = '';
$errs = [];

session_start();

if(!empty($_SESSION['member'])) {
    header('Location:index.php');
exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail_address = $_POST['mail_address'] ?? '';
    $pass_word = $_POST['pass_word'] ?? '';


    if ($mail_address === '') {
        $errs['mail_address'] = 'メールアドレスを入力してください。';
    } else if (!filter_var($mail_address, FILTER_VALIDATE_EMAIL)) {
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
<html>

<head>
    <meta charset="utf-8" />
    <link href="css/BaseDesignData.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/LoginDesign.css">
    <title>ログイン</title>
    <?php include 'template/header2.php'; ?>
</head>

<body>

 

<form action="" method="POST">
    <table id="LoginTable" class="box">
        <tr>
            <th colspan="2">ログイン</th>
        </tr>
        <tr>
            <td>メールアドレス</td>
            <td>
                <input type="email" name="mail_address" 
                       value="<?= htmlspecialchars($mail_address, ENT_QUOTES, 'UTF-8') ?>" required />
                       <span style="color: red"><?= @$errs['mail_address'] ?></span>
            </td>
        </tr>
        <tr>
            <td>パスワード</td>
            <td>
                <input type="password" name="pass_word" required />
                <span style="color: red"><?= @$errs['pass_word'] ?></span>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align:center;">
                <input type="submit" value="ログイン" />
            </td>
        </tr>

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

<?php
// if($_SERVER['REQUEST_METHOD'] === 'POST') {
//         $mail_address = $_POST['mail_address'];
//         $pass_word = $_POST['pass_word'];

// //     if($mail_address === '') {
// //         $errs[] = 'メールアドレスを入力してください。';
// //     }
// //     else if(!filter_var($mail_address, FILTER_VALIDATE_EMAIL)) {
// //         $errs[] = 'メールアドレスの形式に誤りがあります。';
// //     }
// //     if($pass_word === '') {
// //         $errs[] = 'パスワードを入力してください。';  
// //     }
// //      if(empty($errs)) {

//     $memberDAO = new MemberDAO();

//     $member = $memberDAO->get_member($mail_address, $pass_word);



//     if($member !== false) {
//         session_regenerate_id(true);
//         $_SESSION['member'] = $member;

//         header('Location: index.php');
//         exit;
//      }

//     else {
//         $errs[] = 'メールアドレスまたはパスワードに誤りがあります。';
//     }
//      }

?>
