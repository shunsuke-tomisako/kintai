<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>勤怠管理</title>
</head>
<body>
  <?php 
  $dsn = 'mysql:dbname=test;host=localhost;charset=utf8';
  $user = 'root';
  $password = '';
  $dbh = new PDO($dsn,$user,$password);
  $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

  $sql = 'SELECT * FROM users WHERE company_name="trackfarm"';
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $trackfarm_kintai_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $sql2 = 'SELECT * FROM users WHERE company_name="levelzero"';
  $stmt2 = $dbh->prepare($sql2);
  $stmt2->execute();
  $trackfarm_kintai_list2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);

  // 出勤しているか確認
  $today = date("Y-m-d");
  // 当日以外を追加したいとき
  // $today = date("Y-m-d",strtotime("-1 day"));

  foreach ($trackfarm_kintai_list as $trackfarm_kintai_rec) {
    $sql4 = 'SELECT user_id, date FROM trackfarm_kintai WHERE user_id="'.$trackfarm_kintai_rec['user_id'].'" AND date="'.$today.'"';
    $stmt4 = $dbh->prepare($sql4);
    $stmt4->execute();
    $trackfarm_kintai_list4 = $stmt4->fetchAll(PDO::FETCH_ASSOC);
    if (isset($trackfarm_kintai_list4[0]["date"]) == false) {
      $sql5 = 'INSERT INTO trackfarm_kintai (user_id, date) VALUES ("'.$trackfarm_kintai_rec['user_id'].'", "'.$today.'")';
      $stmt5 = $dbh->prepare($sql5);
      $stmt5->execute();
      // $trackfarm_kintai_list5 = $stmt5->fetchAll(PDO::FETCH_ASSOC);
    }
  }

  foreach ($trackfarm_kintai_list2 as $trackfarm_kintai_rec2) {
    $sql6 = 'SELECT user_id, date FROM trackfarm_kintai WHERE user_id="'.$trackfarm_kintai_rec2['user_id'].'" AND date="'.$today.'"';
    $stmt6 = $dbh->prepare($sql6);
    $stmt6->execute();
    $trackfarm_kintai_list6 = $stmt6->fetchAll(PDO::FETCH_ASSOC);
    if (isset($trackfarm_kintai_list6[0]["date"]) == false) {
      $sql7 = 'INSERT INTO trackfarm_kintai (user_id, date) VALUES ("'.$trackfarm_kintai_rec2['user_id'].'", "'.$today.'")';
      $stmt7 = $dbh->prepare($sql7);
      $stmt7->execute();
      // $trackfarm_kintai_list7 = $stmt7->fetchAll(PDO::FETCH_ASSOC);
    }
  }

  ?>
  <div id="home">
    <div class="memberlist">
      <div class="company_name">
        <img src="./img/levelzero.png" alt="levelzero">
      </div>
      <div class="members">
        <?php foreach ($trackfarm_kintai_list2 as $trackfarm_kintai_rec2) { ?>
        <div class="member">
        <a href="index.php?user_id=<?php echo $trackfarm_kintai_rec2['user_id']; ?>" role="button" aria-pressed="true"><?php echo $trackfarm_kintai_rec2['name']; ?></a>
        </div>
        <?php } ?>
      </div>
    </div>
    <div class="memberlist">
      <div class="company_name">
        <img src="./img/trackfarm.png" alt="trackfarm">
      </div>
      <div class="members">
        <?php foreach ($trackfarm_kintai_list as $trackfarm_kintai_rec) { ?>
        <div class="member">
        <a href="index.php?user_id=<?php echo $trackfarm_kintai_rec['user_id']; ?>" role="button" aria-pressed="true"><?php echo $trackfarm_kintai_rec['name']; ?></a>
        </div>
        <?php } ?>
      </div>
    </div>
    <a href="clean_check.php" class="clean"><img src="./img/clean.png" alt="掃除チェック"></a>
    <a href="modify.php" class="addmember"><img src="./img/addmember.png"></a>
  </div>
</body>
</html>