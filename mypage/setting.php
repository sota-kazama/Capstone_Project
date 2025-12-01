<?php
session_start();
require_once '../helpers/MemberDAO.php';

// セッションから取得
$member = $_SESSION['member'] ?? null;

// メッセージ類の初期化（ここが重要）
$message = '';
$error = '';

// ユーザーID取得（オブジェクトでも配列でも対応）
$user_id = null;

if ($member !== null) {
    if (is_object($member) && isset($member->user_id)) {
        $user_id = $member->user_id;
    } elseif (is_array($member) && isset($member['user_id'])) {
        $user_id = $member['user_id'];
    }
}

if ($user_id === null) {
    header('Location: ../login.php');
    exit;
}

$dao = new MemberDAO();
$userInfo = $dao->getMemberById($user_id);


try {
    // 最新データ取得
    $userData = $dao->getMemberById($user_id);
} catch (Exception $e) {
    $error = 'ユーザー情報取得中にエラーが発生しました：' . $e->getMessage();
}

// POST処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $user_name = $_POST['user_name'] ?? null;
        $mail_address = $_POST['mail_address'] ?? null;
        $pass_word = $_POST['pass_word'] ?? null;

        // パスワード入力時だけ更新
        $password_hashed = !empty($pass_word)
            ? password_hash($pass_word, PASSWORD_DEFAULT)
            : null;

        // 更新
        $success = $dao->updateMemberSelf(
            $user_id,
            $user_name,
            $mail_address,
            $password_hashed
        );

        if ($success) {
            $message = 'プロフィールを更新しました。';

            // セッション更新
            $updated = $dao->getMemberById($user_id);
            $_SESSION['member'] = $updated;

            // 表示用データ更新
            $userData = $updated;
        } else {
            $error = 'プロフィール更新に失敗しました。';
        }

    } catch (Exception $e) {
        $error = '更新中にエラーが発生しました：' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
            rel="stylesheet"
        />

        <!-- 既存CSS -->
        <link href="../css/BaseDesignData.css" rel="stylesheet" />
        <link href="../css/side.css" rel="stylesheet" />

        <?php include './header.php'; ?>

        <title>プロフィール編集</title>
    </head>

    <body>
        <div class="d-flex w-100 min-vh-100">

            <!-- サイドメニュー -->
            <?php include 'side.php'; ?>

            <!-- メイン -->
            <main class="main-content flex-grow-1 p-4">

                <h1>プロフィール編集</h1>

                <?php if ($message): ?>
                <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

                <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <div class="card p-4 mt-4">
                    <h4>登録情報</h4>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">ユーザー名</label>
                            <input
                                type="text"
                                class="form-control"
                                name="user_name"
                                value="<?= htmlspecialchars($userData['user_name']) ?>"
                                required
                            />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">メールアドレス</label>
                            <input
                                type="email"
                                class="form-control"
                                name="mail_address"
                                value="<?= htmlspecialchars($userData['mail_address']) ?>"
                                required
                            />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">パスワード（変更時のみ入力）</label>
                            <input
                                type="password"
                                class="form-control"
                                name="pass_word"
                                placeholder="変更する場合のみ入力"
                            />
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> 更新
                        </button>
                    </form>
                </div>
            </main>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
