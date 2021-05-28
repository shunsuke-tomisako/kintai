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
    $sql = 'DELETE FROM users WHERE (name) = ("'.$_POST['name'].'")';
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    header("Location: home.php");
  }

  if ($value == 2 && $_POST["name"] != "") {
    $sql = 'INSERT INTO users (company_name, name) VALUES("'.$_POST["company_name"].'", "'.$_POST["name"].'")';
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    header("Location: home.php");
  }
  ?>
  <div class="modify">
    <div class="memberlist">
      <div class="company_name">
        <img src="./img/levelzero.png" alt="levelzero">
      </div>
      <?php foreach ($trackfarm_kintai_list2 as $trackfarm_kintai_rec2) { ?>
      <div class="members">
        <p><?php echo $trackfarm_kintai_rec2['name']; ?></p>
        <form action="./modify.php" method="post" onSubmit="return checkDelete()">
          <input type="hidden" name="value" value="1">
          <input type="hidden" name="name" value="<?php echo $trackfarm_kintai_rec2['name']; ?>">
          <input type="image" src="./img/delete.png" class="delete">
          <input type="submit" id="submit">
        </form>
      </div>
      <?php } ?>
      <form action="./modify.php" method="post" onSubmit="return checkSubmit()">
        <input type="hidden" name="value" value="2">
        <input type="hidden" name="company_name" value="levelzero">
        <input type="text" name="name" class="name"><br>
        <input type="submit" value="追加" class="add">
      </form>
    </div>
    <div class="memberlist">
      <div class="company_name">
        <img src="./img/trackfarm.png" alt="trackfarm">
      </div>
      <?php foreach ($trackfarm_kintai_list as $trackfarm_kintai_rec) { ?>
      <div class="members">
        <p><?php echo $trackfarm_kintai_rec['name']; ?></p>
        <form action="./modify.php" method="post" onSubmit="return checkDelete()">
          <input type="hidden" name="value" value="1">
          <input type="hidden" name="name" value="<?php echo $trackfarm_kintai_rec['name']; ?>">
          <input type="image" src="./img/delete.png" class="delete">
          <input type="submit" id="submit">
        </form>
      </div>
      <?php } ?>
      <form action="./modify.php" method="post" onSubmit="return checkSubmit()">
        <input type="hidden" name="value" value="2">
        <input type="hidden" name="company_name" value="trackfarm">
        <input type="text" name="name" class="name"><br>
        <input type="submit" value="追加" class="add">
      </form>
    </div>
    <a href="home.php" class="return" role="button" aria-pressed="true"><img src="./img/return.png"></a>
  </div>

  <?php
  if ($value == 2 && $_POST["name"] == "") {
    echo "名前を入力して下さい。" . "<br>" . "<br>";
  }
  ?>

  <script>
  function checkSubmit() {
    if(window.confirm('追加しますか？')){
      return true;
    } else {
      return false;
    }
  }

  function checkDelete() {
    if(window.confirm('削除しますか？')){
      return true;
    } else {
      return false;
    }
  }
  </script>

</body>
</html>