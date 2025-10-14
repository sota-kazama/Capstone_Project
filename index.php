<!DOCTYPE html>
<html>
    <head>
        <!--こっちのheadは変更しない-->
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
        <link href="../css/sidebar.css" rel="stylesheet" />
        <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        />
        <link href="css/BaseDesignData.css" rel="stylesheet" />
        <?php include 'template/header.php'; ?>
        <?php include 'template/hamburger.html';?>
    </head>

    <head>
        <!--こっちのheadを変更しない-->
        <title>メインページ</title>
    </head>

    <body>
        <div class="d-flex w-100 min-vh-100">
            <?php include 'template/side.php';?>

            <main class="main-content">
                <!--ここに記載する-->
                <h1>トップページ</h1>
                <p>ここに、メインとなるページの内容が生成されます。</p>

                <div style="height: 1500px; background-color: #f8f9fa">長いコンテンツの例</div>
            </main>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    <footer>
        <?php include 'template/footer.php'; ?>
    </footer>
</html>
