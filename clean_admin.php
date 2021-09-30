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

  $sql = 'SELECT name FROM users';
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $trackfarm_kintai_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $sql2 = 'SELECT name FROM clean';
  $stmt2 = $dbh->prepare($sql2);
  $stmt2->execute();
  $trackfarm_kintai_list2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);

  $value = "";
  if (isset($_POST["value"])) {
    $value = $_POST["value"];
  }

  if ($value == 1) {
    $sql = 'UPDATE clean SET name = "'.$_POST["A1"].'" WHERE (clean) = ("A1")';
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $sql = 'UPDATE clean SET name = "'.$_POST["A2"].'" WHERE (clean) = ("A2")';
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $sql = 'UPDATE clean SET name = "'.$_POST["B1"].'" WHERE (clean) = ("B1")';
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $sql = 'UPDATE clean SET name = "'.$_POST["B2"].'" WHERE (clean) = ("B2")';
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $sql = 'UPDATE clean SET name = "'.$_POST["C1"].'" WHERE (clean) = ("C1")';
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $sql = 'UPDATE clean SET name = "'.$_POST["C2"].'" WHERE (clean) = ("C2")';
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $sql = 'UPDATE clean SET name = "'.$_POST["D1"].'" WHERE (clean) = ("D1")';
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $sql = 'UPDATE clean SET name = "'.$_POST["D2"].'" WHERE (clean) = ("D2")';
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $sql = 'UPDATE clean SET name = "'.$_POST["E1"].'" WHERE (clean) = ("E1")';
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $sql = 'UPDATE clean SET name = "'.$_POST["E2"].'" WHERE (clean) = ("E2")';
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    header("Location: home.php");
  }
  ?>
  <div id="clean_admin">

    <form action="./clean_admin.php" method="post" onSubmit="return checkSubmit()">

      <div class="team-member">

        <div class="flex">
  
          <div class="team">
            <div class="teamname">A チーム</div>
            <select class="select" name="A1" id="">
              <?php if (isset($trackfarm_kintai_list2[0]["name"]) == true) { ?>
                <option value="<?php echo $trackfarm_kintai_list2[0]["name"]; ?>"><?php echo $trackfarm_kintai_list2[0]["name"]; ?></option>
              <?php } ?>
              <?php foreach ($trackfarm_kintai_list as $trackfarm_kintai_rec) { ?>
                <option value="<?php echo $trackfarm_kintai_rec["name"]; ?>"><?php echo $trackfarm_kintai_rec["name"]; ?></option>
              <?php } ?>
            </select>
            <select class="select" name="A2" id="">
              <?php if (isset($trackfarm_kintai_list2[1]["name"]) == true) { ?>
                <option value="<?php echo $trackfarm_kintai_list2[1]["name"]; ?>"><?php echo $trackfarm_kintai_list2[1]["name"]; ?></option>
              <?php } ?>
              <?php foreach ($trackfarm_kintai_list as $trackfarm_kintai_rec) { ?>
                <option value="<?php echo $trackfarm_kintai_rec["name"]; ?>"><?php echo $trackfarm_kintai_rec["name"]; ?></option>
              <?php } ?>
            </select>
          </div>
    
          <div class="team">
            <div class="teamname">B チーム</div>
            <select class="select" name="B1" id="">
              <?php if (isset($trackfarm_kintai_list2[2]["name"]) == true) { ?>
                <option value="<?php echo $trackfarm_kintai_list2[2]["name"]; ?>" selected><?php echo $trackfarm_kintai_list2[2]["name"]; ?></option>
              <?php } ?>
              <?php foreach ($trackfarm_kintai_list as $trackfarm_kintai_rec) { ?>
                <option value="<?php echo $trackfarm_kintai_rec["name"]; ?>"><?php echo $trackfarm_kintai_rec["name"]; ?></option>
              <?php } ?>
            </select>
            <select class="select" name="B2" id="">
              <?php if (isset($trackfarm_kintai_list2[3]["name"]) == true) { ?>
                <option value="<?php echo $trackfarm_kintai_list2[3]["name"]; ?>"><?php echo $trackfarm_kintai_list2[3]["name"]; ?></option>
              <?php } ?>
              <?php foreach ($trackfarm_kintai_list as $trackfarm_kintai_rec) { ?>
                <option value="<?php echo $trackfarm_kintai_rec["name"]; ?>"><?php echo $trackfarm_kintai_rec["name"]; ?></option>
              <?php } ?>
            </select>
          </div>
    
          <div class="team">
            <div class="teamname">C チーム</div>
            <select class="select" name="C1" id="">
              <?php if (isset($trackfarm_kintai_list2[4]["name"]) == true) { ?>
                <option value="<?php echo $trackfarm_kintai_list2[4]["name"]; ?>"><?php echo $trackfarm_kintai_list2[4]["name"]; ?></option>
              <?php } ?>
              <?php foreach ($trackfarm_kintai_list as $trackfarm_kintai_rec) { ?>
                <option value="<?php echo $trackfarm_kintai_rec["name"]; ?>"><?php echo $trackfarm_kintai_rec["name"]; ?></option>
              <?php } ?>
            </select>
            <select class="select" name="C2" id="">
              <?php if (isset($trackfarm_kintai_list2[5]["name"]) == true) { ?>
                <option value="<?php echo $trackfarm_kintai_list2[5]["name"]; ?>"><?php echo $trackfarm_kintai_list2[5]["name"]; ?></option>
              <?php } ?>
              <?php foreach ($trackfarm_kintai_list as $trackfarm_kintai_rec) { ?>
                <option value="<?php echo $trackfarm_kintai_rec["name"]; ?>"><?php echo $trackfarm_kintai_rec["name"]; ?></option>
              <?php } ?>
            </select>
          </div>
    
          <div class="team">
            <div class="teamname">D チーム</div>
            <select class="select" name="D1" id="">
              <?php if (isset($trackfarm_kintai_list2[6]["name"]) == true) { ?>
                <option value="<?php echo $trackfarm_kintai_list2[6]["name"]; ?>"><?php echo $trackfarm_kintai_list2[6]["name"]; ?></option>
              <?php } ?>
              <?php foreach ($trackfarm_kintai_list as $trackfarm_kintai_rec) { ?>
                <option value="<?php echo $trackfarm_kintai_rec["name"]; ?>"><?php echo $trackfarm_kintai_rec["name"]; ?></option>
              <?php } ?>
            </select>
            <select class="select" name="D2" id="">
              <?php if (isset($trackfarm_kintai_list2[7]["name"]) == true) { ?>
                <option value="<?php echo $trackfarm_kintai_list2[7]["name"]; ?>"><?php echo $trackfarm_kintai_list2[7]["name"]; ?></option>
              <?php } ?>
              <?php foreach ($trackfarm_kintai_list as $trackfarm_kintai_rec) { ?>
                <option value="<?php echo $trackfarm_kintai_rec["name"]; ?>"><?php echo $trackfarm_kintai_rec["name"]; ?></option>
              <?php } ?>
            </select>
          </div>
    
          <div class="team">
            <div class="teamname">E チーム</div>
            <select class="select" name="E1" id="">
              <?php if (isset($trackfarm_kintai_list2[8]["name"]) == true) { ?>
                <option value="<?php echo $trackfarm_kintai_list2[8]["name"]; ?>"><?php echo $trackfarm_kintai_list2[8]["name"]; ?></option>
              <?php } ?>
              <?php foreach ($trackfarm_kintai_list as $trackfarm_kintai_rec) { ?>
                <option value="<?php echo $trackfarm_kintai_rec["name"]; ?>"><?php echo $trackfarm_kintai_rec["name"]; ?></option>
              <?php } ?>
            </select>
            <select class="select" name="E2" id="">
              <?php if (isset($trackfarm_kintai_list2[9]["name"]) == true) { ?>
                <option value="<?php echo $trackfarm_kintai_list2[9]["name"]; ?>"><?php echo $trackfarm_kintai_list2[9]["name"]; ?></option>
              <?php } ?>
              <?php foreach ($trackfarm_kintai_list as $trackfarm_kintai_rec) { ?>
                <option value="<?php echo $trackfarm_kintai_rec["name"]; ?>"><?php echo $trackfarm_kintai_rec["name"]; ?></option>
              <?php } ?>
            </select>
          </div>
  
        </div>
      </div>

      <input type="hidden" name="value" value="1">
      <input type="submit" value="登録する" class="submit">
    </form>
    <a href="home.php" class="return" role="button" aria-pressed="true"><img src="./img/return.png"></a>
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