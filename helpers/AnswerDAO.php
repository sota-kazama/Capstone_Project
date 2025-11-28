<?php
class AnswerDAO {
    private $pdo;

    public function __construct() {
        $this->pdo = new PDO('mysql:host=localhost;dbname=your_db;charset=utf8', 'username', 'password');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // 質問番号に紐づく回答一覧を取得
    public function getByQuestionNumber($shitu_number) {
        $stmt = $this->pdo->prepare("SELECT * FROM answers WHERE shitu_number = :num ORDER BY created_at ASC");
        $stmt->execute(['num' => $shitu_number]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 回答を追加
    public function insert($shitu_number, $answer_content) {
        $stmt = $this->pdo->prepare("INSERT INTO answers (shitu_number, answer_content, created_at) VALUES (:num, :content, NOW())");
        $stmt->execute([
            'num' => $shitu_number,
            'content' => $answer_content
        ]);
    }
}
