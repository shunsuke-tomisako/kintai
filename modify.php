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

  <h3>編集を行います。<h3><br>

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


  $value = "";
  if (isset($_POST["value"])) {
    $value = $_POST["value"];
  }

  if ($value == 1) {
    // echo $_POST['name'];
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

  <?php foreach ($trackfarm_kintai_list as $trackfarm_kintai_rec) { ?>
    <a href="" class="btn btn-secondary btn-lg active" role="button" aria-pressed="true"><?php echo $trackfarm_kintai_rec['name']; ?></a>
    <form action="./modify.php" method="post" onSubmit="return checkDelete()">
      <input type="hidden" name="value" value="1">
      <input type="hidden" name="name" value="<?php echo $trackfarm_kintai_rec['name']; ?>">
      <input type="submit" value="削除" class="btn btn-danger">
    </form>
    <!-- <button class="btn btn-danger" name="value" value="1" onClick="return checkDelete()">削除</button> -->
  <?php } ?>

  <form action="./modify.php" method="post" onSubmit="return checkSubmit()">
    <input type="hidden" name="value" value="2">
    <select name="company_name">
      <option value="トラックファーム">トラックファーム</option>
      <option value="レベルゼロ">レベルゼロ</option>
    </select>
    　名前 <input type="text" name="name">
    　<input type="submit" value="追加する">
  </form>
  <br>

  <?php
  if ($value == 2 && $_POST["name"] == "") {
    echo "名前を入力して下さい。" . "<br>" . "<br>";
  }
  ?>

  <a href="home.php" class="btn btn-dark btn-lg active" role="button" aria-pressed="true">名前の選択に戻る</a>

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