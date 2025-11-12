

<?php
require_once __DIR__ . '/helpers/ShitumonDAO.php';

// POSTデータ取得
$title = trim($_POST['title'] ?? '');
$category = trim($_POST['category'] ?? '');
$content = trim($_POST['content'] ?? '');

if ($title === '' || $category === '' || $content === '') {
    header('Location: question_post.php');
    exit();
}

// DAO生成
$dao = new ShitumonDAO();

// shitu_content にタイトル + 内容をまとめて登録
$shitu_content = $title . "\n" . $content;

// 分野は null または必要なら category を変換
$dao->insert($shitu_content, null, 1);

// 登録後は質問一覧ページへ
header('Location: question_list.php');
exit();
?>


