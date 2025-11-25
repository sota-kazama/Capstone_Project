<?php
require_once 'helpers/ShitumonDAO.php';

if (!isset($_GET['shitu_number'])) {
    die('質問番号が指定されていません。');
}

$shitu_number = intval($_GET['shitu_number']);
$dao = new ShitumonDAO();

try {
    $dao->deleteWithAnswers($shitu_number);
    // 削除後に質問一覧へリダイレクト
    header("Location: question_list.php");
    exit;
} catch (PDOException $e) {
    echo "削除に失敗しました: " . htmlspecialchars($e->getMessage());
}
