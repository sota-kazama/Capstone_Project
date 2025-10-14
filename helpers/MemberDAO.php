<?php
require_once 'DAO.php';

class Member
{
    public int    $user_id;   //ユーザーID
    public string $user_name;      //ユーザーネーム
    public string $mail_address; //メールアドレス
    public string $pass_word;    //パスワード
    public int    $u_correct_count //ユーザー背頭数カウント
    public int    $u_answers_count//
    public string $created_ad;        //
    public string $update_at;   //
    public string $access_date;
    public string $u_admin;
    public string $member_type;
}