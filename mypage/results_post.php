<?php
require_once '../helpers/MemberDAO.php'; 
require_once '../helpers/u_goalsDAO.php';

session_start();

if (!isset($_SESSION['member'])) {
    header('Location: ../auth/login.php');
    exit;
}

$member = $_SESSION['member'];
$user_id = (int)$member->user_id;

// --- POSTデータ受け取り ---
$goal_id      = isset($_POST['goal_id']) ? (int)$_POST['goal_id'] : null;
$selected_ms  = isset($_POST['milestones']) ? $_POST['milestones'] : [];
$is_achieved  = isset($_POST['is_achieved']) ? (int)$_POST['is_achieved'] : 0;
$good_points  = $_POST['good_points'] ?? '';
$bad_points   = $_POST['bad_points'] ?? '';
$memo         = $_POST['memo'] ?? '';

// --- マイルストーン配列作成 ---
$ms_status = [];
for ($i = 1; $i <= 5; $i++) {
    $ms_status[$i] = in_array($i, $selected_ms) ? 1 : 0;
}

// --- DB更新 ---
$goalsDAO = new GoalsDAO();
if ($goal_id) {
    $result = $goalsDAO->updateGoalResult(
        $goal_id,
        $ms_status,
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
