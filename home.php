<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
  <link rel="stylesheet" href="style_home.css">
  <title>勤怠管理</title>
</head>
<body>
  <header>
    <h1>勤怠管理</h1>
  </header>

  <h3>名前を選択して下さい。<h3><br>

  <?php 
  $dsn = 'mysql:dbname=test;host=localhost;charset=utf8';
  $user = 'root';
  $password = '';
  $dbh = new PDO($dsn,$user,$password);
  $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

  $sql = 'SELECT * FROM users';
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $trackfarm_kintai_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
  ?>

  <?php foreach ($trackfarm_kintai_list as $trackfarm_kintai_rec) { ?>
    <a href="index.php?user_id=<?php echo $trackfarm_kintai_rec['user_id']; ?>" class="btn btn-secondary btn-lg active" role="button" aria-pressed="true"><?php echo $trackfarm_kintai_rec['name']; ?></a>
  <?php } ?>
  <br>
  <a href="modify.php" class="btn btn-light">編集</a>
</body>
</html>