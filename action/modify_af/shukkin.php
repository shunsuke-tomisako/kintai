<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
  <link rel="stylesheet" href="../../style_index.css">
  <title>勤怠管理</title>
</head>
<body>
  <header>
    <h1>勤怠管理</h1>
  </header>

  <a href="../modify_be.php?user_id=<?php echo $_GET["user_id"] ?>&date=<?php echo $_GET["date"] ?>" class="btn btn-dark btn-lg active" role="button" aria-pressed="true" >修正の選択に戻る</a><br>
  <h3>出勤時間の修正を行います。<h3><br>

  <?php

  $user_id = $_GET["user_id"];
  if ($user_id == "") {
    header("Location: ../../home.php");
    exit;
  }

  $today = $_GET["date"];
  if ($today == "") {
    header("Location: ../../home.php");
    exit;
  }

  $begin_time = $_GET["begin_time"];
  $finish_time = $_GET["finish_time"];
  $rest_time = $_GET["rest_time"];
  $return_time = $_GET["return_time"];
  if ($begin_time == "") {
    header("Location: ../../home.php");
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

  $value = "";
  if (isset($_GET["value"])) {
    $value = $_GET["value"];
  }

  if ($value == 1) {

    $time = $_GET["time"];
    if (strtotime($time) > strtotime($finish_time)) {
      echo "退勤時間より前の時間を入力してください。";
    } else {
      $sql = 'UPDATE trackfarm_kintai SET begin_time = "'.$time.'" WHERE (user_id, date) = ("'.$user_id.'", "'.$today.'")';
      $stmt = $dbh->prepare($sql);
      $stmt->execute();
      header("Location: modify_af.php");
    }

  }

  ?>

  <form action="./shukkin.php" method="get">
    <h3><input type="datetime-local" name="time" step="1" value="<?php echo str_replace(' ', 'T', $_GET["begin_time"]); ?>"><h3><br>
    <input type="hidden" name="value" value="1">
    <input type="hidden" name="user_id" value="<?php echo $_GET["user_id"]; ?>">
    <input type="hidden" name="date" value="<?php echo $_GET["date"]; ?>">
    <input type="hidden" name="begin_time" value="<?php echo $_GET["begin_time"]; ?>">
    <input type="hidden" name="finish_time" value="<?php echo $_GET["finish_time"]; ?>">
    <input type="hidden" name="rest_time" value="<?php echo $_GET["rest_time"]; ?>">
    <input type="hidden" name="return_time" value="<?php echo $_GET["return_time"]; ?>">
    <input type="submit" class="btn btn-light btn-lg active w-35" value="変更する">
  </form>

</body>
</html>