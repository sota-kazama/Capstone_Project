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
        <title>メインページ</title>
    </head>

    <body>
        <div class="d-flex w-100 min-vh-100">
            <?php include 'problem_side.php';?>

            <main class="main-content">
                <!--ここに記載する-->
                <h1>問題解説</h1>
                <h2>第何問</h2>
                <h3>問題文</h3>

                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 10%">選択肢</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><a class="btn btn-primary" role="button">A</a></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><a class="btn btn-primary" role="button">B</a></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><a class="btn btn-primary" role="button">C</a></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><a class="btn btn-primary" role="button">D</a></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <div class="d-flex flex-wrap justify-content-center">
                    <div style="width: 13rem">
                        <a href="problem_response.php" class="btn btn-outline-primary w-100">次の問題</a>
                    </div>
                </div>
            </main>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    <footer>
        <?php include 'problem_footer.php'; ?>
    </footer>
</html>
