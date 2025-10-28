
<!doctype html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cutt.jp/books/978-4-87783-522-4/css/bootstrap.min.css">
          <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
        <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        />
        <link href="css/BaseDesignData.css" rel="stylesheet" />
        <link href="css/side.css" rel="stylesheet" />
        <?php include 'template/header.php'; ?>
  <title>テーブルの書式</title>
</head>

<body>

<div class="container">        <!-- 全体を囲むコンテナ -->

  <h1>空室一覧</h1>

  <table class="table">
    <thead>
      <tr class="table-dark"><th>部屋No.</th><th>タイプ</th><th>定員</th><th>喫煙</th><th>料金</th></tr>
    </thead>
    <tbody>
      <tr><td>402</td><td>ツイン</td>  <td>2名</td><td>可</td>  <td>8,800円</td></tr>
      <tr><td>407</td><td>ダブル</td>  <td>2名</td><td class="table-warning">不可</td><td>7,800円</td></tr>
      <tr><td>501</td><td>シングル</td><td>1名</td><td class="table-warning">不可</td><td>4,800円</td></tr>
      <tr><td>605</td><td>シングル</td><td>1名</td><td>可</td>  <td>4,800円</td></tr>
      <tr><td>608</td><td>シングル</td><td>1名</td><td>可</td>  <td>5,200円</td></tr>
      <tr class="table-primary"><td>702</td><td>DXツイン</td><td>3名</td><td>不可</td><td>13,800円</td></tr>
      <tr class="table-success"><td>703</td><td>DXダブル</td><td>3名</td><td>不可</td><td>12,800円</td></tr>
    </tbody>
  </table>

</div>        <!-- 全体を囲むコンテナ -->

<script src=https://cutt.jp/books/978-4-87783-522-4/js/bootstrap.bundle.min.js></script>
</body>

</html>
