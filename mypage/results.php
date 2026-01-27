<?php
require_once '../helpers/MemberDAO.php';
require_once '../helpers/u_goalsDAO.php';


//セッションを開始する
session_start();

// 未ログインの場合
if (!isset($_SESSION['member'])) {
    header('Location: ../auth/login.php');
    exit;
}

//ログイン中の会員データを取得
$member = $_SESSION['member'];

// テーマ
$theme = $_COOKIE['theme'] ?? 'light';
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />

        <!-- Bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
            rel="stylesheet"
        />

        <!-- CSS -->
        <link href="../css/BaseDesignData.css" rel="stylesheet" />
        <link href="../css/side.css" rel="stylesheet" />
        <link id="theme-css" href="../css_theme/<?= htmlspecialchars($theme) ?>.css" rel="stylesheet" />
        <link href="../css_theme/toggle-button.css" rel="stylesheet" />

        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <title></title>
    </head>

    <body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">
    <?php include '../template/header.php'; ?>

    <div class="d-flex w-100 min-vh-100">
        <div class="d-none d-md-block">
            <?php include 'side.php'; ?>
        </div> <main class="main-content flex-grow-1 p-4">
            <h1 class="mb-4">成果登録</h1>
            </head>
<body class="<?= $theme === 'dark' ? 'dark-mode' : 'light-mode' ?>">

<?php include '../template/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="mb-4 text-center">成果を記録する</h1>

            <form action="results_post.php" method="POST">
                
                <div class="card shadow-sm mb-4 border-primary">
                    <div class="card-header bg-primary text-white fw-bold">
                        現在の目標
                    </div>
                    <div class="card-body">
                        <?php if ($goal_data): ?>
                            <p class="fs-5 mb-1"><?= htmlspecialchars($goal_data->goal) ?></p>
                            <small class="text-muted">目標日: <?= htmlspecialchars($goal_data->goal_date) ?></small>
                        <?php else: ?>
                            <p class="text-muted">目標が設定されていません。</p>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($milestones)): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header fw-bold">マイルストーン達成状況</div>
                    <div class="card-body">
                        <p class="small text-muted mb-3">達成した項目にチェックを入れてください。</p>
                        <?php foreach ($milestones as $m): ?>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="milestones[]" value="<?= $m['id'] ?>" id="ms<?= $m['id'] ?>">
                                <label class="form-check-label" for="ms<?= $m['id'] ?>">
                                    <?= htmlspecialchars($m['text']) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="card shadow-sm mb-4">
                    <div class="card-header fw-bold">目標の最終結果</div>
                    <div class="card-body text-center">
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="is_achieved" id="achieved" value="1" autocomplete="off" required>
                            <label class="btn btn-outline-success py-3" for="achieved">
                                <i class="bi bi-trophy-fill me-2"></i>達成！
                            </label>

                            <input type="radio" class="btn-check" name="is_achieved" id="not_achieved" value="0" autocomplete="off">
                            <label class="btn btn-outline-secondary py-3" for="not_achieved">
                                <i class="bi bi-flag-fill me-2"></i>未達成
                            </label>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header fw-bold">振り返り</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-success">良かった点・継続したいこと</label>
                            <textarea name="good_points" class="form-control" rows="3" placeholder="例：毎日30分机に向かうことができた"></textarea>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold text-danger">反省点・改善したいこと</label>
                            <textarea name="bad_points" class="form-control" rows="3" placeholder="例：週末にまとめてやろうとして計画が崩れた"></textarea>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header fw-bold">メモ（使用教材など）</div>
                    <div class="card-body">
                        <textarea name="memo" class="form-control" rows="2" placeholder="例：〇〇参考書の第3章まで完了"></textarea>
                    </div>
                </div>

                <div class="d-grid gap-2 mb-5">
                    <button type="submit" class="btn btn-primary btn-lg">この内容で登録する</button>
                    <a href="mypage.php" class="btn btn-link text-secondary">マイページに戻る</a>
                </div>

            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
            </main>
    </div>

        <!-- テーマ切替 -->
        <button id="theme-toggle-btn" class="btn btn-primary theme-toggle-btn">
            <i id="theme-icon" class="bi <?= $theme === 'dark' ? 'bi-sun' : 'bi-moon' ?>"></i>
        </button>

        <!-- JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="../js/theme-toggle.js"></script>

        <!-- フッター -->
        <footer>
            <?php include '../template/footer.php'; ?>
        </footer>
    </body>
</html>
