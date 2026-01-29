<?php
// 1. クラス定義を最優先で読み込む
require_once '../helpers/MemberDAO.php'; 
require_once '../helpers/u_goalsDAO.php';

session_start();

// 2. ログインチェック
if (!isset($_SESSION['member'])) {
    header('Location: ../auth/login.php');
    exit;
}

$member = $_SESSION['member'];
$user_id = (int)$member->user_id;

// 3. フォームデータの受け取り
$selected_ms = isset($_POST['milestones']) ? $_POST['milestones'] : [];
$is_achieved = isset($_POST['is_achieved']) ? (int)$POST['is_achieved'] : 0;
$good_points = $_POST['good_points'] ?? '';
$bad_points  = $_POST['bad_points'] ?? '';
$memo        = $_POST['memo'] ?? '';

// 4. マイルストーンのステータス判定（1〜5）を個別の変数に展開
// GoalsDAO->updateGoalResult が連想配列を受け取るか個別の引数かによって調整が必要ですが、
// ここでは使いやすいように整理します。
$ms_status = [];
for ($i = 1; $i <= 5; $i++) {
    $ms_status[$i] = in_array($i, $selected_ms) ? 1 : 0;
}

// 5. DB更新処理
$goalsDAO = new GoalsDAO();
$goal_data = $goalsDAO->getLatestGoalByUserId($user_id);

if ($goal_data) {
    // DAOの引数設計に合わせて呼び出し
    $result = $goalsDAO->updateGoalResult(
        (int)$goal_data->goal_id,
        $ms_status, // [1=>1, 2=>0...] の配列
        $is_achieved,
        $good_points,
        $bad_points,
        $memo
    );

    if ($result) {
        $_SESSION['flash_message'] = "成果を記録しました！";
    }
}

header('Location: mypage.php');
exit;