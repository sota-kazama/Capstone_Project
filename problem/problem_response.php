<?php
require_once '../helpers/ProblemDAO.php'; //問題情報用
require_once '../helpers/MemberDAO.php';  //ユーザー毎の正誤率把握のため
require_once '../ProblemErrataDAO.php';   //問題正誤情報

// セッションを開始する
session_start();

//ログイン情報の取得
if (!empty($_SESSION['member'])) {
    $member = $_SESSION['member'];
}

// ログイン中の会員データを取得
$member = $_SESSION['member'];

//問題情報を取得

//正誤情報の取得

?>


<!DOCTYPE html>
<html>
    <head>
        <!--こっちのheadは変更しない-->
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
        <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        />
        <link href="css/BaseDesignData.css" rel="stylesheet" />
        <link href="../css/side.css" rel="stylesheet" />
        <?php include 'problem_header.php'; ?>
    </head>

    <head>
        <!--こっちのheadを変更しない-->
        <title></title>
    </head>

    <body>
        <div class="d-flex w-100 min-vh-100">
            <?php include 'problem_side.php';?>

            <main class="main-content">
                <!--ここに記載する-->
                <h1>問題回答</h1>
                <!--

                取得した情報から問題文を表示

                ループ文で回答ボタンを選択肢の数だけ表示
                選択肢はボタン形式で番号が問題の選択肢番号と一致するように設定
                -->




                <!--
                if(回答ボタンが押されたか){
                    if(ログインかどうか){
                        if(正解かどうか){
                            正解と表示
                            正解をカウント
                        }{else
                            不正解と表示
                            不正解をカウント
                        }
                    }else {
                        if(正解かどうか){
                            正解と表示
                        }{else
                            不正解と表示
                    }}
                正解と解説を表示
-->

                <!--次の問題を表示するボタン-->
            </main>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    <footer>
        <?php include '../template/footer.php'; ?>
    </footer>
</html>
