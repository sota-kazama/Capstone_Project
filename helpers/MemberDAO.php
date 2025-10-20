<?php
require_once 'DAO.php';

class Member
{
    public int    $user_id;   //ユーザーID
    public string $user_name;      //ユーザーネーム
    public string $mail_address; //メールアドレス
    public string $pass_word;    //パスワード
    public int    $u_correct_count; //ユーザー背頭数カウント
    public int    $u_answers_count;//
    public string $created_ad;        //
    public string $update_at;   //
    public string $access_date;
    public string $u_admin;
    public string $member_type;
}

class MemberDAO {
    public function get_member(string $mail_address, string $pass_word) {

        $dbh = DAO::get_db_connect();
        
        $sql = "SELECT *
        FROM master_user
        WHERE mail_address = :mail_address";

        $stmt = $dbh->prepare($sql);
        
        $stmt->bindValue(':mail_address', $mail_address, PDO::PARAM_STR);

        $stmt->execute();

        $member = $stmt->fetchObject('master_user');

        if($member !== false) {
            if(password_verify($pass_word, $member->pass_word)) {
                return $member;
            }
        }

        return false;
    }



    public function insert(Member $member) {

        $dbh = DAO::get_db_connect();

        $sql = "INSERT INTO master_user(mail_address, user_name, pass_word) 
                VALUES(:mail_address, :user_name, :pass_word)";
        $stmt = $dbh->prepare($sql);

        $password = password_hash($member->pass_word, PASSWORD_DEFAULT);

        $stmt->bindValue(":mail_address", $member->mail_address, PDO::PARAM_STR);
        $stmt->bindValue(":user_name", $member->user_name, PDO::PARAM_STR);
        // $stmt->bindValue(":tel", $member->tel, PDO::PARAM_STR);
        $stmt->bindValue(":pass_word", $member->pass_word, PDO::PARAM_STR);

        $stmt->execute();

    }

    public function email_exists(string $mail_address) {
        
        $dbh = DAO::get_db_connect();
        
        $sql = "SELECT * FROM master_user WHERE mail_address = :mail_address";
                    
        $stmt = $dbh->prepare($sql);
        
        $stmt->bindValue(":mail_address", $mail_address, PDO::PARAM_STR);
        
        $stmt->execute();
        
        if($stmt->fetch() !== false) {
            return true;
        }
        else {
             return false;
        }
    }
}
