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
        <link href="css/side.css" rel="stylesheet" />
        <link rel="stylesheet" href="css/problemDesign.css">
        <?php include 'template/header.php'; ?>
    </head>

    <head>
        <!--こっちのheadを変更しない-->
        <title>問題</title>
    </head>

    <body>
        <div class="d-flex w-100 min-vh-100">
            <?php include 'template/side.php';?>
            

            <main class="main-content">
                <!--ここに記載する-->
                <h1>問題</h1>
                <div style="height: 1500px; background-color: #f8f9fa">
                    <form action="problem_response.php" method="post" class="problem_request">
                        <a href="problem_response.php" class="problem_start">出題開始</a>
                        <a href="problem_response.php" class="problem_restart">続きから(〇問目から再開)</a>
                    </form>
                    <div class="problem_review">
                        <div class="green">
                            <a href="problem_review.php" class="green_review">復習</a>
                            <p>〇問</p>
                        </div>
                        <div class="yellow">
                            <a href="problem_review.php" class="yellow_review">復習</a>
                            <p>〇問</p>
                        </div>
                        <div class="red">
                            <a href="problem_review.php" class="red_review">復習</a>
                            <p>〇問</p>
                        </div>
                    </div>
                </div>

                
            </main>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    <footer>
        <?php include 'template/footer.php'; ?>
    </footer>
</html>
