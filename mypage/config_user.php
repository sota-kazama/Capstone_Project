<<?php
require_once '../helpers/MemberDAO.php';

// セッションを開始する
session_start();

// 未ログインの場合はログインページへリダイレクト
if (!isset($_SESSION['member'])) {
    header('Location: login.php');
    exit;
}

// ログイン中の会員データを取得
$member = $_SESSION['member'];

// メッセージ類の初期化（成功・エラー用）
$message = '';
$error = '';

// ユーザーIDの取得（オブジェクトと配列両方に対応）
$user_id = null;
if ($member !== null) {
    if (is_object($member) && isset($member->user_id)) {
        $user_id = $member->user_id;
    } elseif (is_array($member) && isset($member['user_id'])) {
        $user_id = $member['user_id'];
    }
}

// ユーザーIDが取得できない場合、ログインページにリダイレクト
if ($user_id === null) {
    header('Location: ../login.php');
    exit;
}

// MemberDAOインスタンスを作成
$dao = new MemberDAO();

// ユーザー情報を取得
try {
    $userData = $dao->getMemberById($user_id);
} catch (Exception $e) {
    $error = 'ユーザー情報取得中にエラーが発生しました：' . $e->getMessage();
}

// POST処理（プロフィール更新）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // POSTデータの取得
        $user_name = $_POST['user_name'] ?? null;
        $mail_address = $_POST['mail_address'] ?? null;
        $pass_word = $_POST['pass_word'] ?? null;

        // パスワードが入力されている場合、ハッシュ化
        $password_hashed = !empty($pass_word) ? password_hash($pass_word, PASSWORD_DEFAULT) : null;

        // プロフィール更新処理
        $success = $dao->updateMemberSelf(
            $user_id,
            $user_name,
            $mail_address,
            $password_hashed
        );

        // 更新が成功した場合
        if ($success) {
            $message = 'プロフィールを更新しました。';

            // セッションの会員データを更新
            $updated = $dao->getMemberById($user_id);
            $_SESSION['member'] = $updated;

            // 表示用データも更新
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

                        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> 更新</button>
                    </form>
                </div>
            </main>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
