<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../style.css">
  <title>勤怠管理</title>
</head>
<body id="modify_be">
  <a href="../index.php?user_id=<?php echo $_GET["user_id"] ?>" class="return" role="button" aria-pressed="true"><img src="../img/return.png"></a><br>

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

  $dsn = 'mysql:dbname=test;host=localhost;charset=utf8';
  $user = 'root';
  $password = '';
  $dbh = new PDO($dsn,$user,$password);
  $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

  $sql = 'SELECT * FROM trackfarm_kintai WHERE user_id="'.$user_id.'" AND date="'.$today.'"';
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $trackfarm_kintai_rec = $stmt->fetch(PDO::FETCH_ASSOC);

  // 名前取得
  $sql2 = 'SELECT name FROM users WHERE user_id="'.$user_id.'"';
  $stmt2 = $dbh->prepare($sql2);
  $stmt2->execute();
  $trackfarm_kintai_rec2 = $stmt2->fetch(PDO::FETCH_ASSOC);

  $value = "";
  if (isset($_GET["value"])) {
    $value = $_GET["value"];
  }

  if ($value == 1) {
    $sql3 = 'UPDATE trackfarm_kintai SET comment = "'.$_GET['comment'].'" WHERE (user_id, date) = ("'.$user_id.'", "'.$today.'")';
    $stmt3 = $dbh->prepare($sql3);
    $stmt3->execute();
    $trackfarm_kintai_rec3 = $stmt2->fetch(PDO::FETCH_ASSOC);
    header("Location: modify_af/modify_af.php");
  }

  ?>
  <div class="name"><strong><?php echo $trackfarm_kintai_rec2["name"] ?></strong>さん</div>


  <div class="modify">
    <div class="wrap">
      <?php if (isset($trackfarm_kintai_rec['id']) == true && isset($trackfarm_kintai_rec['begin_time']) == true) { ?>
      <div class="index">　出勤時間　</div>
      <div class="time">
        <?php
        $split = explode("-", mb_substr($trackfarm_kintai_rec['begin_time'], 5, 11));
        $split2 = explode(" ", $split[1]);
        echo $split[0] . "月" . $split2[0] . "日 " . $split2[1];
        ?>
      </div>
      <div class="button">
        <a href="modify_af/shukkin.php?user_id=<?php echo $user_id; ?>&date=<?php echo $today; ?>&begin_time=<?php echo $trackfarm_kintai_rec['begin_time']; ?>&finish_time=<?php echo $trackfarm_kintai_rec['finish_time']; ?>&rest_time=<?php echo $trackfarm_kintai_rec['rest_time']; ?>&return_time=<?php echo $trackfarm_kintai_rec['return_time']; ?>" class="btn btn-danger btn-lg active w-35" role="button" aria-pressed="true">修正</a>
      </div>
        <?php } else { ?>
        <div class="index">　出勤時間　</div>
        <div class="button">
        <a href="modify_af/shukkin.php?user_id=<?php echo $user_id; ?>&date=<?php echo $today; ?>&begin_time=<?php echo $trackfarm_kintai_rec['begin_time']; ?>&finish_time=<?php echo $trackfarm_kintai_rec['finish_time']; ?>&rest_time=<?php echo $trackfarm_kintai_rec['rest_time']; ?>&return_time=<?php echo $trackfarm_kintai_rec['return_time']; ?>" class="btn btn-secondary btn-lg active w-35" role="button" aria-pressed="true">追加</a>
      </div>
        <?php } ?>

    </div>

    <div class="wrap">
      <?php if (isset($trackfarm_kintai_rec['finish_time']) == true) { ?>
      <div class="index">　退勤時間　</div>
      <div class="time">
        <?php
          $split = explode("-", mb_substr($trackfarm_kintai_rec['finish_time'], 5, 11));
          $split2 = explode(" ", $split[1]);
          echo $split[0] . "月" . $split2[0] . "日 " . $split2[1];
          ?>
      </div>
      <div class="button">
        <a href="modify_af/taikin.php?user_id=<?php echo $user_id; ?>&date=<?php echo $today; ?>&begin_time=<?php echo $trackfarm_kintai_rec['begin_time']; ?>&finish_time=<?php echo $trackfarm_kintai_rec['finish_time']; ?>&rest_time=<?php echo $trackfarm_kintai_rec['rest_time']; ?>&return_time=<?php echo $trackfarm_kintai_rec['return_time']; ?>" class="btn btn-secondary btn-lg active w-35" role="button" aria-pressed="true">修正</a>
      </div>
      <?php } else if (isset($trackfarm_kintai_rec['begin_time'])) { ?>
      <div class="index">　退勤時間　</div>
      <div class="button">
        <a href="modify_af/taikin.php?user_id=<?php echo $user_id; ?>&date=<?php echo $today; ?>&begin_time=<?php echo $trackfarm_kintai_rec['begin_time']; ?>&finish_time=<?php echo $trackfarm_kintai_rec['finish_time']; ?>&rest_time=<?php echo $trackfarm_kintai_rec['rest_time']; ?>&return_time=<?php echo $trackfarm_kintai_rec['return_time']; ?>" class="btn btn-secondary btn-lg active w-35" role="button" aria-pressed="true">追加</a>
      </div>
    <?php } ?>
    </div>

    <div class="wrap">

      <?php if (isset($trackfarm_kintai_rec['rest_time']) == true) { ?>
      <div class="index">休憩開始時間</div>
      <div class="time">
        <?php
          $split = explode("-", mb_substr($trackfarm_kintai_rec['rest_time'], 5, 11));
          $split2 = explode(" ", $split[1]);
          echo $split[0] . "月" . $split2[0] . "日 " . $split2[1];
          ?>
      </div>
      <div class="button">
        <a href="modify_af/rest.php?user_id=<?php echo $user_id; ?>&date=<?php echo $today; ?>&begin_time=<?php echo $trackfarm_kintai_rec['begin_time']; ?>&finish_time=<?php echo $trackfarm_kintai_rec['finish_time']; ?>&rest_time=<?php echo $trackfarm_kintai_rec['rest_time']; ?>&return_time=<?php echo $trackfarm_kintai_rec['return_time']; ?>" class="btn btn-success btn-lg active w-35" role="button" aria-pressed="true">修正</a>
      </div>
      <?php } else if (isset($trackfarm_kintai_rec['begin_time'])) {?>
      <div class="index">休憩開始時間</div>
      <div class="button">
        <a href="modify_af/rest.php?user_id=<?php echo $user_id; ?>&date=<?php echo $today; ?>&begin_time=<?php echo $trackfarm_kintai_rec['begin_time']; ?>&finish_time=<?php echo $trackfarm_kintai_rec['finish_time']; ?>&rest_time=<?php echo $trackfarm_kintai_rec['rest_time']; ?>&return_time=<?php echo $trackfarm_kintai_rec['return_time']; ?>" class="btn btn-success btn-lg active w-35" role="button" aria-pressed="true">追加</a>
      </div>
      <?php } ?>
    </div>

    <div class="wrap">
      <?php if (isset($trackfarm_kintai_rec['return_time']) == true) { ?>
      <div class="index">休憩終了時間</div>
      <div class="time">
        <?php
          $split = explode("-", mb_substr($trackfarm_kintai_rec['return_time'], 5, 11));
          $split2 = explode(" ", $split[1]);
          echo $split[0] . "月" . $split2[0] . "日 " . $split2[1];
          ?>
      </div>
      <div class="button">
        <a href="modify_af/return.php?user_id=<?php echo $user_id; ?>&date=<?php echo $today; ?>&begin_time=<?php echo $trackfarm_kintai_rec['begin_time']; ?>&finish_time=<?php echo $trackfarm_kintai_rec['finish_time']; ?>&rest_time=<?php echo $trackfarm_kintai_rec['rest_time']; ?>&return_time=<?php echo $trackfarm_kintai_rec['return_time']; ?>" class="btn btn-info btn-lg active w-35" role="button" aria-pressed="true">修正</a>
      </div>
      <?php } else if (isset($trackfarm_kintai_rec['rest_time']) == true) { ?>
      <div class="index">休憩終了時間</div>
      <div class="button">
        <a href="modify_af/return.php?user_id=<?php echo $user_id; ?>&date=<?php echo $today; ?>&begin_time=<?php echo $trackfarm_kintai_rec['begin_time']; ?>&finish_time=<?php echo $trackfarm_kintai_rec['finish_time']; ?>&rest_time=<?php echo $trackfarm_kintai_rec['rest_time']; ?>&return_time=<?php echo $trackfarm_kintai_rec['return_time']; ?>" class="btn btn-info btn-lg active w-35" role="button" aria-pressed="true">追加</a>
      </div>
      <?php } ?>
    </div>

    <div class="wrap flex-comment">
      <?php if (isset($trackfarm_kintai_rec['comment']) == true) { ?>
      <div class="index">　コメント　</div>
      <form action="./modify_be.php" method="get" onSubmit="return checkSubmit()" class="comment">
        <input type="text" size="50" name="comment" value="<?php echo $trackfarm_kintai_rec['comment']; ?>" class="content">
        <input type="hidden" name="value" value="1">
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
        <input type="hidden" name="date" value="<?php echo $today; ?>">
        <input type="submit" value="修正" class="comment_button">
      </form>
      <?php } else { ?>
      <div class="index">　コメント　</div>
      <form action="./modify_be.php" method="get" onSubmit="return checkSubmit()" class="comment">
        <input type="text" size="50" name="comment" value="" class="content">
        <input type="hidden" name="value" value="1">
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
        <input type="hidden" name="date" value="<?php echo $today; ?>">
        <input type="submit" value="追加" class="comment_button">
      </form>
      <?php } ?>
    </div>
  </div>

  <script>
  function checkSubmit() {
    if(window.confirm('変更しますか？')) {
      return true;
    } else {
      return false;
    }
  }
  </script>

</body>
</html>