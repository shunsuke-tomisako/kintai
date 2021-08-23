<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../../style.css">
  <title>勤怠管理</title>
</head>
<body id="modify_af">
  <a href="../modify_be.php?user_id=<?php echo $_GET["user_id"] ?>&date=<?php echo $_GET["date"] ?>" class="modify_afreturn" role="button" aria-pressed="true" ><img src="../../img/return.png"></a>
  <div class="comment">休憩終了時間の変更を行います。<div>

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
    if (strtotime($time) < strtotime($rest_time)) {
      echo "休憩開始時間より後の時間を入力してください。";
    } else if ($finish_time != "" && strtotime($time) > strtotime($finish_time)) {
      echo "退勤時間より前の時間を入力してください。";
    } else {
      $sql = 'UPDATE trackfarm_kintai SET return_time = "'.$time.'" WHERE (user_id, date) = ("'.$user_id.'", "'.$today.'")';
      $stmt = $dbh->prepare($sql);
      $stmt->execute();
      header("Location: modify_af.php");
    }
  }

  ?>

  <form action="./return.php" method="get" onSubmit="return checkSubmit()">
    <?php if ($_GET["return_time"] == "") { ?>
      <input type="datetime-local" name="time" step="60" value="<?php echo ($_GET["date"] . 'T' . 13 . ":" . 0 . 0); ?>" class="form"><br>
    <?php } else { ?>
      <input type="datetime-local" name="time" step="60" value="<?php echo str_replace(' ', 'T', $_GET["return_time"]); ?>" class="form"><br>
    <?php } ?>
    <input type="hidden" name="value" value="1">
    <input type="hidden" name="user_id" value="<?php echo $_GET["user_id"]; ?>">
    <input type="hidden" name="date" value="<?php echo $_GET["date"]; ?>">
    <input type="hidden" name="begin_time" value="<?php echo $_GET["begin_time"]; ?>">
    <input type="hidden" name="finish_time" value="<?php echo $_GET["finish_time"]; ?>">
    <input type="hidden" name="rest_time" value="<?php echo $_GET["rest_time"]; ?>">
    <input type="hidden" name="return_time" value="<?php echo $_GET["return_time"]; ?>">
    <input type="submit" class="button" value="変更する">
  </form>

  <script>
  function checkSubmit() {
    if(window.confirm('変更しますか？')){
      return true;
    } else {
      return false;

    }
  }
  </script>

</body>
</html>