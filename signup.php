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

