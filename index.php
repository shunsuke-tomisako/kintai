<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
  <link rel="stylesheet" href="style.css">
  <title>勤怠管理</title>
</head>
<body>
  <div class="index">

    <a href="home.php" class="return" role="button" aria-pressed="true"><img src="./img/return.png"></a>

    <!-- データベース接続 -->
    <?php
  
    $user_id = $_GET["user_id"];
    if ($user_id == "") {
      header("Location: home.php");
      exit;
    }
  
    $today = date("Y/m/d");
    $yesterday = date("Y/m/d",strtotime("-1 day"));
    $now_datetime = date('Y/m/d H:i');
    $now_datetime_str = strtotime($now_datetime);
    $now_hour = date("H");
    // 1時間後
    $one_hour = date('Y/m/d H:i',strtotime("+1 hour"));
  
    $dsn = 'mysql:dbname=test;host=localhost;charset=utf8';
    $user = 'root';
    $password = '';
    $dbh = new PDO($dsn,$user,$password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
  
    $sql = 'SELECT * FROM trackfarm_kintai WHERE user_id="'.$user_id.'" AND date="'.$today.'"';
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $trackfarm_kintai_rec = $stmt->fetch(PDO::FETCH_ASSOC);
  
    // 前日のデータ
    $sql2 = 'SELECT * FROM trackfarm_kintai WHERE user_id="'.$user_id.'" AND date="'.$yesterday.'"';
    $stmt2 = $dbh->prepare($sql2);
    $stmt2->execute();
    $trackfarm_kintai_rec2 = $stmt2->fetch(PDO::FETCH_ASSOC);
  
    $status = "";
    if (isset($_GET["status"])) {
      $status = $_GET["status"];
    }

    // 名前取得
    $sql3 = 'SELECT name FROM users WHERE user_id="'.$user_id.'"';
    $stmt3 = $dbh->prepare($sql3);
    $stmt3->execute();
    $trackfarm_kintai_rec3 = $stmt3->fetch(PDO::FETCH_ASSOC);

    if ($trackfarm_kintai_rec == false && $status == 1) {
  
      $sql = 'INSERT INTO trackfarm_kintai (user_id, date, begin_time) VALUES("'.$user_id.'", "'.$today.'", "'.$now_datetime.'")';
      $stmt = $dbh->prepare($sql);
      $stmt->execute();
  
      header("Location: action/shukkin.php");
    }
  
    if ($status == 2) {
      // 日付越えた場合の条件分岐
      if ($now_hour > 5) {
  
        $sql = 'UPDATE trackfarm_kintai SET finish_time = "'.$now_datetime.'" WHERE (user_id, date) = ("'.$user_id.'", "'.$today.'")';
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
      } else {
        $sql = 'UPDATE trackfarm_kintai SET finish_time = "'.$now_datetime.'" WHERE (user_id, date) = ("'.$user_id.'", "'.$yesterday.'")';
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
      }
  
      header("Location: action/taikin.php");
    }
  
    if ($status == 3) {
  
      $sql = 'UPDATE trackfarm_kintai SET rest_time = "'.$now_datetime.'" WHERE (user_id, date) = ("'.$user_id.'", "'.$today.'")';
      $stmt = $dbh->prepare($sql);
      $stmt->execute();
  
      header("Location: action/rest.php");
    }
  
    if ($status == 4) {
  
      $sql = 'UPDATE trackfarm_kintai SET return_time = "'.$now_datetime.'" WHERE (user_id, date) = ("'.$user_id.'", "'.$today.'")';
      $stmt = $dbh->prepare($sql);
      $stmt->execute();
  
      header("Location: action/return.php");
    }
  
    if ($status == 5) {
  
      $sql = 'UPDATE trackfarm_kintai SET rest_time = "'.$now_datetime.'" WHERE (user_id, date) = ("'.$user_id.'", "'.$today.'")';
      $stmt = $dbh->prepare($sql);
      $stmt->execute();
      $sql = 'UPDATE trackfarm_kintai SET return_time = "'.$one_hour.'" WHERE (user_id, date) = ("'.$user_id.'", "'.$today.'")';
      $stmt = $dbh->prepare($sql);
      $stmt->execute();
  
      header("Location: action/rest.php");
    }
    ?>

    <div class="name"><strong><?php echo $trackfarm_kintai_rec3["name"] ?></strong>さん</div>

    <!-- 時計 -->
    <div id="time"></div>
    <script>
      time();
      function time() {
        let now = new Date();
        let str = now.toLocaleString();
        let result = str.split('/');
        let time = result[0] + '年' + result[1] + '月' + result[2];
        let result2 = time.split(' ');
        let time2 = result2[0] + '日 ' + result2[1];
        document.getElementById("time").innerHTML = time2;
        // document.getElementById("time").innerHTML = now.toLocaleString();
      }
      setInterval('time()',1000);
    </script>

    <div class="action">

      <?php if (isset($trackfarm_kintai_rec['id']) == false && $now_hour > 5) { ?>
      <a href="index.php?status=1&user_id=<?php echo $user_id; ?>" class="" role="button" aria-pressed="true" onClick="return checkShukkin()"><span><img src="./img/begin.png" alt="出勤"></span></a>
      <?php } ?>

      <?php if ((isset($trackfarm_kintai_rec['begin_time']) == true && isset($trackfarm_kintai_rec['finish_time']) == false && (((isset($trackfarm_kintai_rec['rest_time']) == false && isset($trackfarm_kintai_rec['return_time']) == false)) || (isset($trackfarm_kintai_rec['rest_time']) == true && isset($trackfarm_kintai_rec['return_time']) == true))) || ($now_hour <= 5 && isset($trackfarm_kintai_rec2['begin_time']) == true && isset($trackfarm_kintai_rec2['finish_time']) == false)) { ?>
      <a href="index.php?status=2&user_id=<?php echo $user_id; ?>" class="" role="button" aria-pressed="true" onClick="return checkTaikin()"><span><img src="./img/finish.png" alt="退勤"></span></a>
      <?php } ?>

      <?php if (isset($trackfarm_kintai_rec['begin_time']) == true && isset($trackfarm_kintai_rec['finish_time']) == false && isset($trackfarm_kintai_rec['rest_time']) == false && $now_hour < 21) { ?>
      <a href="index.php?status=3&user_id=<?php echo $user_id; ?>" class="" role="button" aria-pressed="true" onClick="return checkRest()"><span><img src="./img/rest.png" alt="休憩"></span></a>
      <?php } ?>

      <?php if (isset($trackfarm_kintai_rec['begin_time']) == true && isset($trackfarm_kintai_rec['finish_time']) == false && isset($trackfarm_kintai_rec['rest_time']) == false && $now_hour < 21) { ?>
      <a href="index.php?status=5&user_id=<?php echo $user_id; ?>" class="" role="button" aria-pressed="true" onClick="return checkRest()"><span><img src="./img/rest_1hour.png" alt="休憩(1時間)"></span></a>
      <?php } ?>

      <?php if (isset($trackfarm_kintai_rec['rest_time']) == true && isset($trackfarm_kintai_rec['return_time']) == false) { ?>
      <a href="index.php?status=4&user_id=<?php echo $user_id; ?>" class="" role="button" aria-pressed="true" onClick="return checkReturn()"><span><img src="./img/retur.png" alt="戻り"></span></a>
      <?php } ?>

      <?php if (isset($trackfarm_kintai_rec['id']) == true && $now_hour > 5) { ?>
      <a href="action/modify_be.php?user_id=<?php echo $user_id; ?>&date=<?php echo $today; ?>" class="" role="button" aria-pressed="true"><span><img src="./img/modify.png" alt="修正"></span></a>
      <?php } ?>

      <?php if (isset($trackfarm_kintai_rec2['id']) == true && $now_hour <= 5) { ?>
      <a href="action/modify_be.php?user_id=<?php echo $user_id; ?>&date=<?php echo $yesterday; ?>" class="" role="button" aria-pressed="true"><span><img src="./img/modify.png" alt="修正"></span></a>
      <?php } ?>

      <a href="action/rireki.php?user_id=<?php echo $user_id; ?>" class="" role="button" aria-pressed="true"><span><img src="./img/rireki.png" alt="履歴"></span></a>
    </div>
  </div>

  <script>
  function checkShukkin() {
    if(window.confirm('出勤しますか？')){
      return true;
    } else {
      return false;

    }
  }
  function checkTaikin() {
    if(window.confirm('退勤しますか？')){
      return true;
    } else {
      return false;

    }
  }
  function checkRest() {
    if(window.confirm('休憩を開始しますか？')){
      return true;
    } else {
      return false;

    }
  }
  function checkReturn() {
    if(window.confirm('休憩から戻りますか？')){
      return true;
    } else {
      return false;

    }
  }
  </script>

</body>
</html>