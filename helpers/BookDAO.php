<?php
require_once 'DAO.php';

class Books
{
    public string $book_code;  //書籍コード
    public string $book_name;  //書籍名
    public string $sakusya;    //作者名
    public string $syuppan;    //出版社名
    public string $created_ad; //登録日
    public string $update_at;  //更新日
}

class BookDAO{
    public function searchBooks(string $keyword)
{
    $dbh = DAO::get_db_connect();

    $sql = "SELECT * FROM books
            WHERE book_code LIKE ? OR book_name LIKE ? OR
                sakusya LIKE ? OR syuppan LIKE ?
            ORDER BY book_code DESC"; //

    $stmt = $dbh->prepare($sql);

    $searchKeyword = '%' . $keyword . '%';
    $stmt->bindValue(1, $searchKeyword, PDO::PARAM_STR);
    $stmt->bindValue(2, $searchKeyword, PDO::PARAM_STR);
    $stmt->bindValue(3, $searchKeyword, PDO::PARAM_STR);
    $stmt->bindValue(4, $searchKeyword, PDO::PARAM_STR);

    // SQLを実行する
    $stmt->execute();

    $data = [];
    while ($row = $stmt->fetchObject('Books')) {
        $data[] = $row;
    }

    // 取得した結果を返す
    return $data;

}}
?>