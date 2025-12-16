
<?php
require_once '../helpers/ShitumonDAO.php';
require_once '../helpers/DAO.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $errors = [];

    $shitu_title   = trim($_POST['shitu_title'] ?? '');
    $shitu_content = trim($_POST['shitu_content'] ?? '');
    $area_number   = trim($_POST['area_number'] ?? '');

    // ----------------------
    // バリデーション
    // ----------------------
    if ($shitu_title === '') {
        $errors[] = '質問タイトルは必須です。';
    }
    if ($shitu_content === '') {
        $errors[] = '質問内容は必須です。';
    }
    if ($area_number === '') {
        $errors[] = '分野を選択してください。';
    }

    if (!empty($errors)) {
        foreach ($errors as $err) {
            echo "<p style='color:red;'>".htmlspecialchars($err)."</p>";
        }
        echo "<p><a href='question_post.php'>戻る</a></p>";
        exit;
    }

    // ----------------------
    // area_name を DB から取得
    // ----------------------
    $dbh = DAO::get_db_connect();
    $stmt = $dbh->prepare("SELECT area_name FROM q_categories WHERE area_number = ?");
    $stmt->execute([$area_number]);
    $cat = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cat) {
        die("不正な分野が選択されました。");
    }

    $area_name = $cat['area_name'];

    // ----------------------
    // DAO 登録
    // ----------------------
    $dao = new ShitumonDAO();
    $dao->insert(
        $shitu_title,
        $shitu_content,
        1,
        $area_number,
        $area_name
    );

    // ----------------------
    //投稿完了 → リストへ移動
    // ----------------------
    header("Location: question_list.php");
    exit;
}
?>
