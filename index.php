<?php
require_once './helpers/updateDAO.php';
require_once './helpers/BugDAO.php';  // BugDAOを読み込む

// PHPでテーマを取得（Cookie が無ければ light）
$theme = $_COOKIE['theme'] ?? 'light';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />

    <!-- カスタムCSS -->
    <link href="./css/BaseDesignData.css" rel="stylesheet" />
    <link href="./css/side.css" rel="stylesheet" />
    <link id="theme-css" rel="stylesheet" href="./css_theme/<?= htmlspecialchars($theme) ?>.css" />
    <link href="./css_theme/toggle-button.css" rel="stylesheet" />

    <title>トップページ</title>
</head>
<body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">

    <!-- ヘッダー -->
    <?php include './template/header.php'; ?>

    <div class="d-flex w-100 min-vh-100">
        <!-- サイドバー（中サイズ以上のみ表示） -->
        <div class="d-none d-md-block">
            <?php include './template/side.php'; ?>
        </div>

        <!-- メインコンテンツ -->
        <main class="main-content flex-grow-1 p-4">
            <div class="mb-4">
                <h1 class="mb-2">トップページ</h1>
                <!-- <h2>概要</h2> -->
                <p>このWebシステムは資格取得など目標に向けた学習マイルストーンです。</p>
                <p>日記のように成果を記録してモチベーションを上げたり、他のユーザに質問して疑問を解消したりすることができます。</p>
                <p>左のサイドバー、もしくは右上のハンバーガーメニューから機能を選択してください。</p>
            </div>

            <!-- 更新情報セクション -->
            <div class="card p-4 mb-4">
                <h4 class="mb-3">更新情報</h4>

                <?php
                    $updateDAO = new UpdateDAO();
                    $updates = $updateDAO->getInfo(); // 最新3件取得

                    if (!empty($updates)) {
                        foreach ($updates as $update) {
                            $date = date('Y-m-d', strtotime($update->created_ad));
                            $content = htmlspecialchars($update->up_info);
                            echo <<<HTML
                            <div class="card mb-2 p-2 shadow-sm">
                                <div class="d-flex align-items-center gap-3">
                                    <small class="text-muted">{$date}</small>
                                    <span>{$content}</span>
                                </div>
                            </div>
                            HTML;
                        }
                    } else {
                        echo '<div class="alert alert-info">更新情報はありません。</div>';
                    }
                ?>
                
            </div>

            <!-- 不具合報告セクション -->
            <div class="card p-4">
                <h4 class="mb-3">今確認されている不具合</h4>

                <?php
                    $bugDAO = new BugDAO();
                    $bugs = $bugDAO->getRecent(); // 最新3件取得

                    if (!empty($bugs)) {
                        foreach ($bugs as $bug) {
                            $date = date('Y-m-d', strtotime($bug->created_at));
                            $content = htmlspecialchars($bug->bug_info);
                            echo <<<HTML
                            <div class="card mb-2 p-2 shadow-sm">
                                <div class="d-flex align-items-center gap-3">
                                    <small class="text-muted">{$date}</small>
                                    <span>{$content}</span>
                                </div>
                            </div>
                            HTML;
                        }
                    } else {
                        echo '<div class="alert alert-info">確認されている不具合はありません。</div>';
                    }
                ?>
            </div>
            <div class="d-flex flex-wrap justify-content-center p-2 g-col-6 gap-4">
                <div class="card shadow-sm" style="width: 18rem;">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">
                            <a href="problem/problem_top.php" class="btn btn-outline-primary w-100">問題演習</a>
                        </h5>
                        <p>基本情報技術者試験やITパスポートなどの問題を解くことができます。</p>
                    </div>
                </div>
                <div class="card shadow-sm" style="width: 18rem;">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">
                            <a href="book.php" class="btn btn-outline-primary w-100">書籍検索</a>
                        </h5>
                        <p>参考書などの検索ができます。Amazonへ遷移して購入することも可能です。</p> 
                    </div>
                </div>
               <div class="card shadow-sm" style="width: 18rem;">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">
                            <a href="board/thread_list.php" class="btn btn-outline-primary w-100">掲示板</a>
                        </h5>
                        <p>掲示板で利用者同士が意見交換や情報共有を行えます。</p>
                        <p>※ログインが必要です。</p>
                        
                    </div>
                </div>
                <div class="card shadow-sm" style="width: 18rem;">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">
                            <a href="mypage/mypage.php" class="btn btn-outline-primary w-100">マイページ</a>
                        </h5>
                        <p>マイルストーンで目標や成果を簡単記録。アカウント設定などもこちらから。</p>
                        <p>※ログインが必要です。</p> 
                    </div>
                    
                </div>
                <div class="card shadow-sm" style="width: 18rem;">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">
                            <a href="Shitsumonbako/question_list.php" class="btn btn-outline-primary w-100">質問箱</a>
                        </h5>
                        <p>匿名で気軽に質問や回答ができます。</p>
                        <p>※ログインが必要です。</p>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- テーマ切替ボタン -->
    <button id="theme-toggle-btn" class="btn btn-primary theme-toggle-btn">
        <i id="theme-icon" class="bi <?= $theme === 'dark' ? 'bi-sun' : 'bi-moon' ?>"></i>
    </button>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./js/theme-toggle_top.js"></script>

    <!-- フッター -->
    <footer>
        <?php include './template/footer.php'; ?>
    </footer>

</body>
</html>
