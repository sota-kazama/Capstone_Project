<?php
require_once 'DAO.php';

class Book
{
    public string $book_code;  //書籍コード
    public string $book_name;  //書籍名
    public string $sakusya;    //作者名
    public string $shuppan;    //出版社名
    public string $created_at; //登録日
    public string $update_at;  //更新日
}

?>