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

  <a href="../index.php?user_id=<?php echo $_GET["user_id"] ?>" class="btn btn-dark btn-lg active" role="button" aria-pressed="true" >選択に戻る</a><br>

  <?php

  $user_id = $_GET["user_id"];
  if ($user_id == "") {
    header("Location: ../home.php");
    exit;
  }

  $today = $_GET["date"];
  if ($today == "") {
    header("Location: ../home.php");
    exit;
  }

  $WHERE_user_id = 'WHERE user_id='.$user_id;
  $dsn = 'mysql:dbname=test;host=localhost;charset=utf8';
  $user = 'root';
  $password = '';
  $dbh = new PDO($dsn,$user,$password);
  $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
  
  $sql = 'SELECT * FROM trackfarm_kintai WHERE user_id="'.$user_id.'" AND date="'.$today.'"';
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $trackfarm_kintai_rec = $stmt->fetch(PDO::FETCH_ASSOC);

  echo $user_id .'<p>さん</p>';

  ?>


  <?php if (isset($trackfarm_kintai_rec['begin_time']) == true) { ?>
    <a href="modify_af/shukkin.php?user_id=<?php echo $user_id; ?>&date=<?php echo $today; ?>&begin_time=<?php echo $trackfarm_kintai_rec['begin_time']; ?>&finish_time=<?php echo $trackfarm_kintai_rec['finish_time']; ?>&rest_time=<?php echo $trackfarm_kintai_rec['rest_time']; ?>&return_time=<?php echo $trackfarm_kintai_rec['return_time']; ?>" class="btn btn-danger btn-lg active w-35" role="button" aria-pressed="true">　出勤時間 　修正</a> 
    <!-- <a href="modify_af/shukkin.php?user_id=<?php echo $user_id; ?>&date=<?php echo $today; ?>&begin_time=<?php echo $trackfarm_kintai_rec['begin_time']; ?>" class="btn btn-danger btn-lg active w-35" role="button" aria-pressed="true">　出勤時間 　修正</a>  -->
    <?php echo $trackfarm_kintai_rec['begin_time'] ."<br>";?>
  <?php } ?>

  <?php if (isset($trackfarm_kintai_rec['rest_time']) == true) { ?>
    <a href="modify_af/rest.php?user_id=<?php echo $user_id; ?>&date=<?php echo $today; ?>&rest_time=<?php echo $trackfarm_kintai_rec['rest_time']; ?>" class="btn btn-success btn-lg active w-35" role="button" aria-pressed="true">休憩開始時間 修正</a>
    <?php echo $trackfarm_kintai_rec['rest_time'] ."<br>"; ?>
  <?php } ?>

  <?php if (isset($trackfarm_kintai_rec['return_time']) == true) { ?>
    <a href="modify_af/return.php?user_id=<?php echo $user_id; ?>&date=<?php echo $today; ?>&return_time=<?php echo $trackfarm_kintai_rec['return_time']; ?>" class="btn btn-info btn-lg active w-35" role="button" aria-pressed="true">休憩終了時間 修正</a>
    <?php echo $trackfarm_kintai_rec['return_time'] ."<br>"; ?>
  <?php } ?>

  <?php if (isset($trackfarm_kintai_rec['finish_time']) == true) { ?>
    <a href="modify_af/taikin.php?user_id=<?php echo $user_id; ?>&date=<?php echo $today; ?>&finish_time=<?php echo $trackfarm_kintai_rec['finish_time']; ?>" class="btn btn-secondary btn-lg active w-35" role="button" aria-pressed="true">　退勤時間 　修正</a>
    <?php echo $trackfarm_kintai_rec['finish_time'] ."<br>"; ?>
  <?php } ?>

  <a href="rireki.php?user_id=<?php echo $user_id; ?>" class="btn btn-light btn-lg active w-35" role="button" aria-pressed="true">　　　履歴　　　</a>

</body>
</html>