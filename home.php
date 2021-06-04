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
    <a href="modify.php" class="addmember"><img src="./img/addmember.png"></a>
  </div>
</body>
</html>