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

  $value = "";
  if (isset($_POST["value"])) {
    $value = $_POST["value"];
  }

  if ($value == 1) {
    echo $_POST["check"];
    // $sql = 'INSERT INTO users (company_name, name) VALUES("'.$_POST["company_name"].'", "'.$_POST["name"].'")';
    // $stmt = $dbh->prepare($sql);
    // $stmt->execute();
    // header("Location: home.php");
  }
  ?>
  <div id="clean_check">
    <div class="text">掃除チェック表</div>

    <table>
      
    </table>



    <!-- <div class="flex">
      <div class="memberlist">
        <div class="company_name">
          <img src="./img/levelzero.png" alt="levelzero">
        </div>
        <?php foreach ($trackfarm_kintai_list2 as $trackfarm_kintai_rec2) { ?>
        <div class="members">
          <p><?php echo $trackfarm_kintai_rec2['name']; ?></p>
          <form action="./clean.php" method="post">
            <input type="hidden" name="name" value="<?php echo $trackfarm_kintai_rec2['name']; ?>">
            <input type="checkbox" class="check">
          </form>
        </div>
        <?php } ?>
      </div>
      <div class="memberlist">
        <div class="company_name">
          <img src="./img/trackfarm.png" alt="trackfarm">
        </div>
        <?php foreach ($trackfarm_kintai_list as $trackfarm_kintai_rec) { ?>
        <div class="members">
          <p><?php echo $trackfarm_kintai_rec['name']; ?></p>
          <form action="./clean.php" method="post">
            <input type="hidden" name="check" value="<?php echo $trackfarm_kintai_rec['name']; ?>">
            <input type="hidden" name="value" value="1">
            <input type="checkbox" class="check" name="check" value="<?php echo $trackfarm_kintai_rec['name']; ?>">
          </form>
        </div>
        <?php } ?>
      </div>
    </div> -->
    <form action="./clean.php" method="post" onSubmit="return checkSubmit()">
      <input type="hidden" name="value" value="1">
      <input type="submit" value="登録する" class="submit">
    </form>
    <a href="home.php" class="return" role="button" aria-pressed="true"><img src="./img/return.png"></a>
    <a href="clean_admin.php" class="admin" role="button" aria-pressed="true"><img src="./img/admin.png" alt="担当管理"></a>
  </div>

  <script>
  function checkSubmit() {
    if(window.confirm('登録しますか？')){
      return true;
    } else {
      return false;
    }
  }
  </script>

</body>
</html>