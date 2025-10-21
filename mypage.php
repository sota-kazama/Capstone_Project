<?php
require_once './helpers/MemberDAO.php';


//セッションを開始する
session_start();

//未ログインの場合


//ログイン中の会員データを取得
$member = $_SESSION['member'];
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
            href="https://cdn.jsdelivr.net/npm/bootstrap-icosns@1.11.3/font/bootstrap-icons.min.css"
        />
        <link href="css/BaseDesignData.css" rel="stylesheet" />
        <link href="css/side.css" rel="stylesheet" />
        <?php include 'template/header.php'; ?>
    </head>

    <head>
        <!--こっちのheadを変更する-->
        <title>マイページ</title>
    </head>

    <body>
        <div class="d-flex w-100 min-vh-100">
            <?php include 'mypage/side.php';?>

            <main class="main-content">
                <!--ここに記載する-->
                <h1>マイページ</h1>

                <!--正誤表-->
                <!--途中から回答する-->
                <!--目標日-->
            </main>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    <footer>
        <?php include 'template/footer.php'; ?>
    </footer>
</html>
