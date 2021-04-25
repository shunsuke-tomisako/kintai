<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
  <link rel="stylesheet" href="../style_index.css">
  <title>勤怠管理</title>
</head>
<body>
  <header>
    <h1>勤怠管理</h1>
  </header>

  <a href="../home.php" class="btn btn-dark btn-lg active" role="button" aria-pressed="true" >名前の選択に戻る</a>

  <?php
  // $now_datetime = date('Y/m/d H:i');
  $now_datetime = date('Y/m/d H:i:s');
  echo '<h3>出勤しました。本日も一日がんばりましょう！<h3><br>';
  echo $now_datetime;

  ?>
</body>
</html>