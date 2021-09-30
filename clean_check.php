<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>勤怠管理</title>
</head>
<body id="clean_check">
  <?php

  // 担当ペア取得
  $basis_day = "2021-09-12";
  $today = date("Y-m-d");
  $differ_time = strtotime($today) - strtotime($basis_day);
  $differ_day = $differ_time / (60 * 60 * 24);

  // 今週の担当
  if ($differ_day % 35 < 7) {
    $cleaner_pair = "A";
  } elseif (7 <= $differ_day % 35 && $differ_day % 35 < 14) {
    $cleaner_pair = "B";
  } elseif (14 <= $differ_day % 35 && $differ_day % 35 < 21) {
    $cleaner_pair = "C";
  } elseif (21 <= $differ_day % 35 && $differ_day % 35 < 28) {
    $cleaner_pair = "D";
  } elseif (28 <= $differ_day % 35 && $differ_day % 35 < 35) {
    $cleaner_pair = "E";
  }

  // 先週の担当
  if ($differ_day % 35 < 7) {
    $lastcleaner_pair = "E";
  } elseif (7 <= $differ_day % 35 && $differ_day % 35 < 14) {
    $lastcleaner_pair = "A";
  } elseif (14 <= $differ_day % 35 && $differ_day % 35 < 21) {
    $lastcleaner_pair = "B";
  } elseif (21 <= $differ_day % 35 && $differ_day % 35 < 28) {
    $lastcleaner_pair = "C";
  } elseif (28 <= $differ_day % 35 && $differ_day % 35 < 35) {
    $lastcleaner_pair = "D";
  }

  // 今週の日付取得
  if (date('w') == '0') {
    $first_day = date("Y-m-d",strtotime("+1 day"));
  } elseif (date('w') == '1') {
    $first_day = date("Y-m-d");
  } elseif (date('w') == '2') {
    $first_day = date("Y-m-d",strtotime("-1 day"));
  } elseif (date('w') == '3') {
    $first_day = date("Y-m-d",strtotime("-2 day"));
  } elseif (date('w') == '4') {
    $first_day = date("Y-m-d",strtotime("-3 day"));
  } elseif (date('w') == '5') {
    $first_day = date("Y-m-d",strtotime("-4 day"));
  } elseif (date('w') == '6') {
    $first_day = date("Y-m-d",strtotime("-5 day"));
  }

  $Mon = substr($first_day, 5, 5);
  $Tue = substr(date("Y-m-d",strtotime("+1 day", strtotime($first_day))), 5, 5);
  $Wed = substr(date("Y-m-d",strtotime("+2 day", strtotime($first_day))), 5, 5);
  $Thu = substr(date("Y-m-d",strtotime("+3 day", strtotime($first_day))), 5, 5);
  $Fri = substr(date("Y-m-d",strtotime("+4 day", strtotime($first_day))), 5, 5);

  // 先週の日付
  $lastMon = substr(date("Y-m-d",strtotime("-7 day", strtotime($first_day))), 5, 5);
  $lastTue = substr(date("Y-m-d",strtotime("-6 day", strtotime($first_day))), 5, 5);
  $lastWed = substr(date("Y-m-d",strtotime("-5 day", strtotime($first_day))), 5, 5);
  $lastThu = substr(date("Y-m-d",strtotime("-4 day", strtotime($first_day))), 5, 5);
  $lastFri = substr(date("Y-m-d",strtotime("-3 day", strtotime($first_day))), 5, 5);

  // データベース接続
  $dsn = 'mysql:dbname=test;host=localhost;charset=utf8';
  $user = 'root';
  $password = '';
  $dbh = new PDO($dsn,$user,$password);
  $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

  // 今週の担当
  $sql = 'SELECT name FROM clean WHERE clean="'.$cleaner_pair . "1".'" OR clean="'.$cleaner_pair . "2".'"';
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $trackfarm_kintai_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // 先週の担当
  $sql2 = 'SELECT name FROM clean WHERE clean="'.$lastcleaner_pair . "1".'" OR clean="'.$lastcleaner_pair . "2".'"';
  $stmt2 = $dbh->prepare($sql2);
  $stmt2->execute();
  $trackfarm_kintai_list2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);

  $value = "";
  if (isset($_POST["value"])) {
    $value = $_POST["value"];
  }

  for ($i = 1; $i <= 174; $i++) {
    if ($value == $i) {

      if (isset($_POST['date_place' . $i])) {

        // チェックされているか確認
        $sql = 'SELECT * from clean_check where date="'.explode("_", $_POST["date_place$i"])[0].'" AND place="'.explode("_", $_POST["date_place$i"])[1].'"';
        $stmt = $dbh->prepare($sql);
        $stmt->execute();

        if ($stmt->fetch() === false) {
          // チェックされていなかったらデータ挿入
          $sql2 = 'INSERT INTO clean_check (date, place) VALUES("'.explode("_", $_POST["date_place$i"])[0].'", "'.explode("_", $_POST["date_place$i"])[1].'")';
          $stmt2 = $dbh->prepare($sql2);
          $stmt2->execute();
        } else {
          // チェックされていたらデータ削除
          // $sql2 = 'DELETE from clean_check where date="'.explode("_", $_POST["date_place$i"])[0].'" AND place="'.explode("_", $_POST["date_place$i"])[1].'"';
          // $stmt2 = $dbh->prepare($sql2);
          // $stmt2->execute();
        }

      }
    }
  }
  ?>

  <table id="check_table">
    <tr>
      <th class="border-none"></th>
      <th class="border-none"></th>
      <th colspan="5" class="border-right color-change"><span>先週</span> 担当 : <?php echo $trackfarm_kintai_list2[0]["name"]; ?>・<?php echo $trackfarm_kintai_list2[1]["name"]; ?></th>
      <th colspan="5" class="color-change"><span>今週</span> 担当 : <?php echo $trackfarm_kintai_list[0]["name"]; ?>・<?php echo $trackfarm_kintai_list[1]["name"]; ?></th>
    </tr>
    <tr>
      <td class="border-none"></td>
      <td class="border-none"></td>
      <td><?php echo $lastMon ?></td>
      <td><?php echo $lastTue ?></td>
      <td><?php echo $lastWed ?></td>
      <td><?php echo $lastThu ?></td>
      <td><?php echo $lastFri ?></td>
      <td><?php echo $Mon ?></td>
      <td><?php echo $Tue ?></td>
      <td><?php echo $Wed ?></td>
      <td><?php echo $Thu ?></td>
      <td><?php echo $Fri ?></td>
    </tr>
    <tr>
      <td class="border-none">4F</td>
      <td class="border-none"></td>
      <td class="border-none"></td>
      <td class="border-none"></td>
      <td class="border-none"></td>
      <td class="border-none"></td>
      <td class="border-none"></td>
      <td class="border-none"></td>
      <td class="border-none"></td>
      <td class="border-none"></td>
      <td class="border-none"></td>
      <td class="border-none"></td>
    </tr>
    <tr>
      <td rowspan="5" class="border-bottom color-change room">メイン</td>
      <td class="place">机</td>
      <td id="check_table1">
        <form id="check_form1" action="./clean_check.php" method="post">
          <input type="checkbox" id="form1" name="date_place1" value="<?php echo date("Y-m-d",strtotime("-7 day", strtotime($first_day))); ?>_1"
            <?php
              // データがあればチェック
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-7 day", strtotime($first_day))).'" AND place="1"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form1">
          <input type="hidden" name="value" value="1">
        </form>
      </td>
      <td id="check_table2">
        <form id="check_form2" action="./clean_check.php" method="post">
          <input type="checkbox" id="form2" name="date_place2" value="<?php echo date("Y-m-d",strtotime("-6 day", strtotime($first_day))); ?>_1"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-6 day", strtotime($first_day))).'" AND place="1"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form2">
          <input type="hidden" name="value" value="2">
        </form>
      </td>
      <td id="check_table3">
        <form id="check_form3" action="./clean_check.php" method="post">
          <input type="checkbox" id="form3" name="date_place3" value="<?php echo date("Y-m-d",strtotime("-5 day", strtotime($first_day))); ?>_1"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-5 day", strtotime($first_day))).'" AND place="1"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form3">
          <input type="hidden" name="value" value="3">
        </form>
      </td>
      <td id="check_table4">
        <form id="check_form4" action="./clean_check.php" method="post">
          <input type="checkbox" id="form4" name="date_place4" value="<?php echo date("Y-m-d",strtotime("-4 day", strtotime($first_day))); ?>_1"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-4 day", strtotime($first_day))).'" AND place="1"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form4">
          <input type="hidden" name="value" value="4">
        </form>
      </td>
      <td id="check_table5">
        <form id="check_form5" action="./clean_check.php" method="post">
          <input type="checkbox" id="form5" name="date_place5" value="<?php echo date("Y-m-d",strtotime("-3 day", strtotime($first_day))); ?>_1"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-3 day", strtotime($first_day))).'" AND place="1"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form5">
          <input type="hidden" name="value" value="5">
        </form>
      </td>
      <td id="check_table6">
        <form id="check_form6" action="./clean_check.php" method="post">
          <input type="checkbox" id="form6" name="date_place6" value="<?php echo $first_day; ?>_1"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.$first_day.'" AND place="1"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form6">
          <input type="hidden" name="value" value="6">
        </form>
      </td>
      <td id="check_table7">
        <form id="check_form7" action="./clean_check.php" method="post">
          <input type="checkbox" id="form7" name="date_place7" value="<?php echo date("Y-m-d",strtotime("+1 day", strtotime($first_day))); ?>_1"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+1 day", strtotime($first_day))).'" AND place="1"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form7">
          <input type="hidden" name="value" value="7">
        </form>
      </td>
      <td id="check_table8">
        <form id="check_form8" action="./clean_check.php" method="post">
          <input type="checkbox" id="form8" name="date_place8" value="<?php echo date("Y-m-d",strtotime("+2 day", strtotime($first_day))); ?>_1"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+2 day", strtotime($first_day))).'" AND place="1"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form8">
          <input type="hidden" name="value" value="8">
        </form>
      </td>
      <td id="check_table9">
        <form id="check_form9" action="./clean_check.php" method="post">
          <input type="checkbox" id="form9" name="date_place9" value="<?php echo date("Y-m-d",strtotime("+3 day", strtotime($first_day))); ?>_1"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+3 day", strtotime($first_day))).'" AND place="1"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form9">
          <input type="hidden" name="value" value="9">
        </form>
      </td>
      <td id="check_table10">
        <form id="check_form10" action="./clean_check.php" method="post">
          <input type="checkbox" id="form10" name="date_place10" value="<?php echo date("Y-m-d",strtotime("+4 day", strtotime($first_day))); ?>_1"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+4 day", strtotime($first_day))).'" AND place="1"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form10">
          <input type="hidden" name="value" value="10">
        </form>
      </td>
    </tr>
    <tr>
      <td class="place">床</td>
      <td id="check_table11">
        <form id="check_form11" action="./clean_check.php" method="post">
          <input type="checkbox" id="form11" name="date_place11" value="<?php echo date("Y-m-d",strtotime("-7 day", strtotime($first_day))); ?>_2"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-7 day", strtotime($first_day))).'" AND place="2"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form11">
          <input type="hidden" name="value" value="11">
        </form>
      </td>
      <td id="check_table12">
        <form id="check_form12" action="./clean_check.php" method="post">
          <input type="checkbox" id="form12" name="date_place12" value="<?php echo date("Y-m-d",strtotime("-6 day", strtotime($first_day))); ?>_2"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-6 day", strtotime($first_day))).'" AND place="2"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form12">
          <input type="hidden" name="value" value="12">
        </form>
      </td>
      <td id="check_table13">
        <form id="check_form13" action="./clean_check.php" method="post">
          <input type="checkbox" id="form13" name="date_place13" value="<?php echo date("Y-m-d",strtotime("-5 day", strtotime($first_day))); ?>_2"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-5 day", strtotime($first_day))).'" AND place="2"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form13">
          <input type="hidden" name="value" value="13">
        </form>
      </td>
      <td id="check_table14">
        <form id="check_form14" action="./clean_check.php" method="post">
          <input type="checkbox" id="form14" name="date_place14" value="<?php echo date("Y-m-d",strtotime("-4 day", strtotime($first_day))); ?>_2"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-4 day", strtotime($first_day))).'" AND place="2"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form14">
          <input type="hidden" name="value" value="14">
        </form>
      </td>
      <td id="check_table15">
        <form id="check_form15" action="./clean_check.php" method="post">
          <input type="checkbox" id="form15" name="date_place15" value="<?php echo date("Y-m-d",strtotime("-3 day", strtotime($first_day))); ?>_2"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-3 day", strtotime($first_day))).'" AND place="2"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form15">
          <input type="hidden" name="value" value="15">
        </form>
      </td>
      <td id="check_table16">
        <form id="check_form16" action="./clean_check.php" method="post">
          <input type="checkbox" id="form16" name="date_place16" value="<?php echo $first_day; ?>_2"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.$first_day.'" AND place="2"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form16">
          <input type="hidden" name="value" value="16">
        </form>
      </td>
      <td id="check_table17">
        <form id="check_form17" action="./clean_check.php" method="post">
          <input type="checkbox" id="form17" name="date_place17" value="<?php echo date("Y-m-d",strtotime("+1 day", strtotime($first_day))); ?>_2"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+1 day", strtotime($first_day))).'" AND place="2"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form17">
          <input type="hidden" name="value" value="17">
        </form>
      </td>
      <td id="check_table18">
        <form id="check_form18" action="./clean_check.php" method="post">
          <input type="checkbox" id="form18" name="date_place18" value="<?php echo date("Y-m-d",strtotime("+2 day", strtotime($first_day))); ?>_2"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+2 day", strtotime($first_day))).'" AND place="2"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form18">
          <input type="hidden" name="value" value="18">
        </form>
      </td>
      <td id="check_table19">
        <form id="check_form19" action="./clean_check.php" method="post">
          <input type="checkbox" id="form19" name="date_place19" value="<?php echo date("Y-m-d",strtotime("+3 day", strtotime($first_day))); ?>_2"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+3 day", strtotime($first_day))).'" AND place="2"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form19">
          <input type="hidden" name="value" value="19">
        </form>
      </td>
      <td id="check_table20">
        <form id="check_form20" action="./clean_check.php" method="post">
          <input type="checkbox" id="form20" name="date_place20" value="<?php echo date("Y-m-d",strtotime("+4 day", strtotime($first_day))); ?>_2"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+4 day", strtotime($first_day))).'" AND place="2"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form20">
          <input type="hidden" name="value" value="20">
        </form>
      </td>
    </tr>
    <tr>
      <td class="place">ラック</td>
      <td id="check_table21">
        <form id="check_form21" action="./clean_check.php" method="post">
          <input type="checkbox" id="form21" name="date_place21" value="<?php echo date("Y-m-d",strtotime("-7 day", strtotime($first_day))); ?>_3"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-7 day", strtotime($first_day))).'" AND place="3"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form21">
          <input type="hidden" name="value" value="21">
        </form>
      </td>
      <td id="check_table22">
        <form id="check_form22" action="./clean_check.php" method="post">
          <input type="checkbox" id="form22" name="date_place22" value="<?php echo date("Y-m-d",strtotime("-6 day", strtotime($first_day))); ?>_3"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-6 day", strtotime($first_day))).'" AND place="3"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form22">
          <input type="hidden" name="value" value="22">
        </form>
      </td>
      <td id="check_table23">
        <form id="check_form23" action="./clean_check.php" method="post">
          <input type="checkbox" id="form23" name="date_place23" value="<?php echo date("Y-m-d",strtotime("-5 day", strtotime($first_day))); ?>_3"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-5 day", strtotime($first_day))).'" AND place="3"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form23">
          <input type="hidden" name="value" value="23">
        </form>
      </td>
      <td id="check_table24">
        <form id="check_form24" action="./clean_check.php" method="post">
          <input type="checkbox" id="form24" name="date_place24" value="<?php echo date("Y-m-d",strtotime("-4 day", strtotime($first_day))); ?>_3"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-4 day", strtotime($first_day))).'" AND place="3"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form24">
          <input type="hidden" name="value" value="24">
        </form>
      </td>
      <td id="check_table25">
        <form id="check_form25" action="./clean_check.php" method="post">
          <input type="checkbox" id="form25" name="date_place25" value="<?php echo date("Y-m-d",strtotime("-3 day", strtotime($first_day))); ?>_3"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-3 day", strtotime($first_day))).'" AND place="3"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form25">
          <input type="hidden" name="value" value="25">
        </form>
      </td>
      <td id="check_table26">
        <form id="check_form26" action="./clean_check.php" method="post">
          <input type="checkbox" id="form26" name="date_place26" value="<?php echo $first_day; ?>_3"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.$first_day.'" AND place="3"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form26">
          <input type="hidden" name="value" value="26">
        </form>
      </td>
      <td id="check_table27">
        <form id="check_form27" action="./clean_check.php" method="post">
          <input type="checkbox" id="form27" name="date_place27" value="<?php echo date("Y-m-d",strtotime("+1 day", strtotime($first_day))); ?>_3"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+1 day", strtotime($first_day))).'" AND place="3"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form27">
          <input type="hidden" name="value" value="27">
        </form>
      </td>
      <td id="check_table28">
        <form id="check_form28" action="./clean_check.php" method="post">
          <input type="checkbox" id="form28" name="date_place28" value="<?php echo date("Y-m-d",strtotime("+2 day", strtotime($first_day))); ?>_3"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+2 day", strtotime($first_day))).'" AND place="3"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form28">
          <input type="hidden" name="value" value="28">
        </form>
      </td>
      <td id="check_table29">
        <form id="check_form29" action="./clean_check.php" method="post">
          <input type="checkbox" id="form29" name="date_place29" value="<?php echo date("Y-m-d",strtotime("+3 day", strtotime($first_day))); ?>_3"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+3 day", strtotime($first_day))).'" AND place="3"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form29">
          <input type="hidden" name="value" value="29">
        </form>
      </td>
      <td id="check_table30">
        <form id="check_form30" action="./clean_check.php" method="post">
          <input type="checkbox" id="form30" name="date_place30" value="<?php echo date("Y-m-d",strtotime("+4 day", strtotime($first_day))); ?>_3"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+4 day", strtotime($first_day))).'" AND place="3"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form30">
          <input type="hidden" name="value" value="30">
        </form>
      </td>
    </tr>
    <tr>
      <td class="place">プリンター</td>
      <td id="check_table31">
        <form id="check_form31" action="./clean_check.php" method="post">
          <input type="checkbox" id="form31" name="date_place31" value="<?php echo date("Y-m-d",strtotime("-7 day", strtotime($first_day))); ?>_4"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-7 day", strtotime($first_day))).'" AND place="4"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form31">
          <input type="hidden" name="value" value="31">
        </form>
      </td>
      <td id="check_table32">
        <form id="check_form32" action="./clean_check.php" method="post">
          <input type="checkbox" id="form32" name="date_place32" value="<?php echo date("Y-m-d",strtotime("-6 day", strtotime($first_day))); ?>_4"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-6 day", strtotime($first_day))).'" AND place="4"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form32">
          <input type="hidden" name="value" value="32">
        </form>
      </td>
      <td id="check_table33">
        <form id="check_form33" action="./clean_check.php" method="post">
          <input type="checkbox" id="form33" name="date_place33" value="<?php echo date("Y-m-d",strtotime("-5 day", strtotime($first_day))); ?>_4"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-5 day", strtotime($first_day))).'" AND place="4"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form33">
          <input type="hidden" name="value" value="33">
        </form>
      </td>
      <td id="check_table34">
        <form id="check_form34" action="./clean_check.php" method="post">
          <input type="checkbox" id="form34" name="date_place34" value="<?php echo date("Y-m-d",strtotime("-4 day", strtotime($first_day))); ?>_4"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-4 day", strtotime($first_day))).'" AND place="4"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form34">
          <input type="hidden" name="value" value="34">
        </form>
      </td>
      <td id="check_table35">
        <form id="check_form35" action="./clean_check.php" method="post">
          <input type="checkbox" id="form35" name="date_place35" value="<?php echo date("Y-m-d",strtotime("-3 day", strtotime($first_day))); ?>_4"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-3 day", strtotime($first_day))).'" AND place="4"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form35">
          <input type="hidden" name="value" value="35">
        </form>
      </td>
      <td id="check_table36">
        <form id="check_form36" action="./clean_check.php" method="post">
          <input type="checkbox" id="form36" name="date_place36" value="<?php echo $first_day; ?>_4"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.$first_day.'" AND place="4"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form36">
          <input type="hidden" name="value" value="36">
        </form>
      </td>
      <td id="check_table37">
        <form id="check_form37" action="./clean_check.php" method="post">
          <input type="checkbox" id="form37" name="date_place37" value="<?php echo date("Y-m-d",strtotime("+1 day", strtotime($first_day))); ?>_4"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+1 day", strtotime($first_day))).'" AND place="4"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form37">
          <input type="hidden" name="value" value="37">
        </form>
      </td>
      <td id="check_table38">
        <form id="check_form38" action="./clean_check.php" method="post">
          <input type="checkbox" id="form38" name="date_place38" value="<?php echo date("Y-m-d",strtotime("+2 day", strtotime($first_day))); ?>_4"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+2 day", strtotime($first_day))).'" AND place="4"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form38">
          <input type="hidden" name="value" value="38">
        </form>
      </td>
      <td id="check_table39">
        <form id="check_form39" action="./clean_check.php" method="post">
          <input type="checkbox" id="form39" name="date_place39" value="<?php echo date("Y-m-d",strtotime("+3 day", strtotime($first_day))); ?>_4"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+3 day", strtotime($first_day))).'" AND place="4"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form39">
          <input type="hidden" name="value" value="39">
        </form>
      </td>
      <td id="check_table40">
        <form id="check_form40" action="./clean_check.php" method="post">
          <input type="checkbox" id="form40" name="date_place40" value="<?php echo date("Y-m-d",strtotime("+4 day", strtotime($first_day))); ?>_4"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+4 day", strtotime($first_day))).'" AND place="4"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form40">
          <input type="hidden" name="value" value="40">
        </form>
      </td>
    </tr>
    <tr>
      <td class="place">ベランダ</td>
      <td id="check_table41">
        <form id="check_form41" action="./clean_check.php" method="post">
          <input type="checkbox" id="form41" name="date_place41" value="<?php echo date("Y-m-d",strtotime("-7 day", strtotime($first_day))); ?>_5"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-7 day", strtotime($first_day))).'" AND place="5"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form41">
          <input type="hidden" name="value" value="41">
        </form>
      </td>
      <td id="check_table42">
        <form id="check_form42" action="./clean_check.php" method="post">
          <input type="checkbox" id="form42" name="date_place42" value="<?php echo date("Y-m-d",strtotime("-6 day", strtotime($first_day))); ?>_5"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-6 day", strtotime($first_day))).'" AND place="5"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form42">
          <input type="hidden" name="value" value="42">
        </form>
      </td>
      <td id="check_table43">
        <form id="check_form43" action="./clean_check.php" method="post">
          <input type="checkbox" id="form43" name="date_place43" value="<?php echo date("Y-m-d",strtotime("-5 day", strtotime($first_day))); ?>_5"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-5 day", strtotime($first_day))).'" AND place="5"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form43">
          <input type="hidden" name="value" value="43">
        </form>
      </td>
      <td id="check_table44">
        <form id="check_form44" action="./clean_check.php" method="post">
          <input type="checkbox" id="form44" name="date_place44" value="<?php echo date("Y-m-d",strtotime("-4 day", strtotime($first_day))); ?>_5"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-4 day", strtotime($first_day))).'" AND place="5"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form44">
          <input type="hidden" name="value" value="44">
        </form>
      </td>
      <td id="check_table45">
        <form id="check_form45" action="./clean_check.php" method="post">
          <input type="checkbox" id="form45" name="date_place45" value="<?php echo date("Y-m-d",strtotime("-3 day", strtotime($first_day))); ?>_5"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-3 day", strtotime($first_day))).'" AND place="5"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form45">
          <input type="hidden" name="value" value="45">
        </form>
      </td>
      <td id="check_table46">
        <form id="check_form46" action="./clean_check.php" method="post">
          <input type="checkbox" id="form46" name="date_place46" value="<?php echo $first_day; ?>_5"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.$first_day.'" AND place="5"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form46">
          <input type="hidden" name="value" value="46">
        </form>
      </td>
      <td id="check_table47">
        <form id="check_form47" action="./clean_check.php" method="post">
          <input type="checkbox" id="form47" name="date_place47" value="<?php echo date("Y-m-d",strtotime("+1 day", strtotime($first_day))); ?>_5"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+1 day", strtotime($first_day))).'" AND place="5"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form47">
          <input type="hidden" name="value" value="47">
        </form>
      </td>
      <td id="check_table48">
        <form id="check_form48" action="./clean_check.php" method="post">
          <input type="checkbox" id="form48" name="date_place48" value="<?php echo date("Y-m-d",strtotime("+2 day", strtotime($first_day))); ?>_5"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+2 day", strtotime($first_day))).'" AND place="5"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form48">
          <input type="hidden" name="value" value="48">
        </form>
      </td>
      <td id="check_table49">
        <form id="check_form49" action="./clean_check.php" method="post">
          <input type="checkbox" id="form49" name="date_place49" value="<?php echo date("Y-m-d",strtotime("+3 day", strtotime($first_day))); ?>_5"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+3 day", strtotime($first_day))).'" AND place="5"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form49">
          <input type="hidden" name="value" value="49">
        </form>
      </td>
      <td id="check_table50">
        <form id="check_form50" action="./clean_check.php" method="post">
          <input type="checkbox" id="form50" name="date_place50" value="<?php echo date("Y-m-d",strtotime("+4 day", strtotime($first_day))); ?>_5"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+4 day", strtotime($first_day))).'" AND place="5"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form50">
          <input type="hidden" name="value" value="50">
        </form>
      </td>
    </tr>
    <tr>
      <td rowspan="4" class="border-bottom color-change room">サブ</td>
      <td class="place">机</td>
      <td id="check_table51">
        <form id="check_form51" action="./clean_check.php" method="post">
          <input type="checkbox" id="form51" name="date_place51" value="<?php echo date("Y-m-d",strtotime("-7 day", strtotime($first_day))); ?>_6"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-7 day", strtotime($first_day))).'" AND place="6"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form51">
          <input type="hidden" name="value" value="51">
        </form>
      </td>
      <td id="check_table52">
        <form id="check_form52" action="./clean_check.php" method="post">
          <input type="checkbox" id="form52" name="date_place52" value="<?php echo date("Y-m-d",strtotime("-6 day", strtotime($first_day))); ?>_6"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-6 day", strtotime($first_day))).'" AND place="6"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form52">
          <input type="hidden" name="value" value="52">
        </form>
      </td>
      <td id="check_table53">
        <form id="check_form53" action="./clean_check.php" method="post">
          <input type="checkbox" id="form53" name="date_place53" value="<?php echo date("Y-m-d",strtotime("-5 day", strtotime($first_day))); ?>_6"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-5 day", strtotime($first_day))).'" AND place="6"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form53">
          <input type="hidden" name="value" value="53">
        </form>
      </td>
      <td id="check_table54">
        <form id="check_form54" action="./clean_check.php" method="post">
          <input type="checkbox" id="form54" name="date_place54" value="<?php echo date("Y-m-d",strtotime("-4 day", strtotime($first_day))); ?>_6"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-4 day", strtotime($first_day))).'" AND place="6"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form54">
          <input type="hidden" name="value" value="54">
        </form>
      </td>
      <td id="check_table55">
        <form id="check_form55" action="./clean_check.php" method="post">
          <input type="checkbox" id="form55" name="date_place55" value="<?php echo date("Y-m-d",strtotime("-3 day", strtotime($first_day))); ?>_6"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-3 day", strtotime($first_day))).'" AND place="6"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form55">
          <input type="hidden" name="value" value="55">
        </form>
      </td>
      <td id="check_table56">
        <form id="check_form56" action="./clean_check.php" method="post">
          <input type="checkbox" id="form56" name="date_place56" value="<?php echo $first_day; ?>_6"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.$first_day.'" AND place="6"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form56">
          <input type="hidden" name="value" value="56">
        </form>
      </td>
      <td id="check_table57">
        <form id="check_form57" action="./clean_check.php" method="post">
          <input type="checkbox" id="form57" name="date_place57" value="<?php echo date("Y-m-d",strtotime("+1 day", strtotime($first_day))); ?>_6"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+1 day", strtotime($first_day))).'" AND place="6"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form57">
          <input type="hidden" name="value" value="57">
        </form>
      </td>
      <td id="check_table58">
        <form id="check_form58" action="./clean_check.php" method="post">
          <input type="checkbox" id="form58" name="date_place58" value="<?php echo date("Y-m-d",strtotime("+2 day", strtotime($first_day))); ?>_6"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+2 day", strtotime($first_day))).'" AND place="6"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form58">
          <input type="hidden" name="value" value="58">
        </form>
      </td>
      <td id="check_table59">
        <form id="check_form59" action="./clean_check.php" method="post">
          <input type="checkbox" id="form59" name="date_place59" value="<?php echo date("Y-m-d",strtotime("+3 day", strtotime($first_day))); ?>_6"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+3 day", strtotime($first_day))).'" AND place="6"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form59">
          <input type="hidden" name="value" value="59">
        </form>
      </td>
      <td id="check_table60">
        <form id="check_form60" action="./clean_check.php" method="post">
          <input type="checkbox" id="form60" name="date_place60" value="<?php echo date("Y-m-d",strtotime("+4 day", strtotime($first_day))); ?>_6"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+4 day", strtotime($first_day))).'" AND place="6"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form60">
          <input type="hidden" name="value" value="60">
        </form>
      </td>
    </tr>
    <tr>
      <td class="place">床</td>
      <td id="check_table61">
        <form id="check_form61" action="./clean_check.php" method="post">
          <input type="checkbox" id="form61" name="date_place61" value="<?php echo date("Y-m-d",strtotime("-7 day", strtotime($first_day))); ?>_7"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-7 day", strtotime($first_day))).'" AND place="7"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form61">
          <input type="hidden" name="value" value="61">
        </form>
      </td>
      <td id="check_table62">
        <form id="check_form62" action="./clean_check.php" method="post">
          <input type="checkbox" id="form62" name="date_place62" value="<?php echo date("Y-m-d",strtotime("-6 day", strtotime($first_day))); ?>_7"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-6 day", strtotime($first_day))).'" AND place="7"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form62">
          <input type="hidden" name="value" value="62">
        </form>
      </td>
      <td id="check_table63">
        <form id="check_form63" action="./clean_check.php" method="post">
          <input type="checkbox" id="form63" name="date_place63" value="<?php echo date("Y-m-d",strtotime("-5 day", strtotime($first_day))); ?>_7"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-5 day", strtotime($first_day))).'" AND place="7"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form63">
          <input type="hidden" name="value" value="63">
        </form>
      </td>
      <td id="check_table64">
        <form id="check_form64" action="./clean_check.php" method="post">
          <input type="checkbox" id="form64" name="date_place64" value="<?php echo date("Y-m-d",strtotime("-4 day", strtotime($first_day))); ?>_7"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-4 day", strtotime($first_day))).'" AND place="7"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form64">
          <input type="hidden" name="value" value="64">
        </form>
      </td>
      <td id="check_table65">
        <form id="check_form65" action="./clean_check.php" method="post">
          <input type="checkbox" id="form65" name="date_place65" value="<?php echo date("Y-m-d",strtotime("-3 day", strtotime($first_day))); ?>_7"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-3 day", strtotime($first_day))).'" AND place="7"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form65">
          <input type="hidden" name="value" value="65">
        </form>
      </td>
      <td id="check_table66">
        <form id="check_form66" action="./clean_check.php" method="post">
          <input type="checkbox" id="form66" name="date_place66" value="<?php echo $first_day; ?>_7"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.$first_day.'" AND place="7"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form66">
          <input type="hidden" name="value" value="66">
        </form>
      </td>
      <td id="check_table67">
        <form id="check_form67" action="./clean_check.php" method="post">
          <input type="checkbox" id="form67" name="date_place67" value="<?php echo date("Y-m-d",strtotime("+1 day", strtotime($first_day))); ?>_7"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+1 day", strtotime($first_day))).'" AND place="7"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form67">
          <input type="hidden" name="value" value="67">
        </form>
      </td>
      <td id="check_table68">
        <form id="check_form68" action="./clean_check.php" method="post">
          <input type="checkbox" id="form68" name="date_place68" value="<?php echo date("Y-m-d",strtotime("+2 day", strtotime($first_day))); ?>_7"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+2 day", strtotime($first_day))).'" AND place="7"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form68">
          <input type="hidden" name="value" value="68">
        </form>
      </td>
      <td id="check_table69">
        <form id="check_form69" action="./clean_check.php" method="post">
          <input type="checkbox" id="form69" name="date_place69" value="<?php echo date("Y-m-d",strtotime("+3 day", strtotime($first_day))); ?>_7"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+3 day", strtotime($first_day))).'" AND place="7"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form69">
          <input type="hidden" name="value" value="69">
        </form>
      </td>
      <td id="check_table70">
        <form id="check_form70" action="./clean_check.php" method="post">
          <input type="checkbox" id="form70" name="date_place70" value="<?php echo date("Y-m-d",strtotime("+4 day", strtotime($first_day))); ?>_7"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+4 day", strtotime($first_day))).'" AND place="7"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form70">
          <input type="hidden" name="value" value="70">
        </form>
      </td>
    </tr>
    <tr>
      <td class="place">本棚</td>
      <td id="check_table71">
        <form id="check_form71" action="./clean_check.php" method="post">
          <input type="checkbox" id="form71" name="date_place71" value="<?php echo date("Y-m-d",strtotime("-7 day", strtotime($first_day))); ?>_8"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-7 day", strtotime($first_day))).'" AND place="8"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form71">
          <input type="hidden" name="value" value="71">
        </form>
      </td>
      <td id="check_table72">
        <form id="check_form72" action="./clean_check.php" method="post">
          <input type="checkbox" id="form72" name="date_place72" value="<?php echo date("Y-m-d",strtotime("-6 day", strtotime($first_day))); ?>_8"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-6 day", strtotime($first_day))).'" AND place="8"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form72">
          <input type="hidden" name="value" value="72">
        </form>
      </td>
      <td id="check_table73">
        <form id="check_form73" action="./clean_check.php" method="post">
          <input type="checkbox" id="form73" name="date_place73" value="<?php echo date("Y-m-d",strtotime("-5 day", strtotime($first_day))); ?>_8"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-5 day", strtotime($first_day))).'" AND place="8"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form73">
          <input type="hidden" name="value" value="73">
        </form>
      </td>
      <td id="check_table74">
        <form id="check_form74" action="./clean_check.php" method="post">
          <input type="checkbox" id="form74" name="date_place74" value="<?php echo date("Y-m-d",strtotime("-4 day", strtotime($first_day))); ?>_8"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-4 day", strtotime($first_day))).'" AND place="8"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form74">
          <input type="hidden" name="value" value="74">
        </form>
      </td>
      <td id="check_table75">
        <form id="check_form75" action="./clean_check.php" method="post">
          <input type="checkbox" id="form75" name="date_place75" value="<?php echo date("Y-m-d",strtotime("-3 day", strtotime($first_day))); ?>_8"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-3 day", strtotime($first_day))).'" AND place="8"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form75">
          <input type="hidden" name="value" value="75">
        </form>
      </td>
      <td id="check_table76">
        <form id="check_form76" action="./clean_check.php" method="post">
          <input type="checkbox" id="form76" name="date_place76" value="<?php echo $first_day; ?>_8"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.$first_day.'" AND place="8"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form76">
          <input type="hidden" name="value" value="76">
        </form>
      </td>
      <td id="check_table77">
        <form id="check_form77" action="./clean_check.php" method="post">
          <input type="checkbox" id="form77" name="date_place77" value="<?php echo date("Y-m-d",strtotime("+1 day", strtotime($first_day))); ?>_8"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+1 day", strtotime($first_day))).'" AND place="8"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form77">
          <input type="hidden" name="value" value="77">
        </form>
      </td>
      <td id="check_table78">
        <form id="check_form78" action="./clean_check.php" method="post">
          <input type="checkbox" id="form78" name="date_place78" value="<?php echo date("Y-m-d",strtotime("+2 day", strtotime($first_day))); ?>_8"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+2 day", strtotime($first_day))).'" AND place="8"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form78">
          <input type="hidden" name="value" value="78">
        </form>
      </td>
      <td id="check_table79">
        <form id="check_form79" action="./clean_check.php" method="post">
          <input type="checkbox" id="form79" name="date_place79" value="<?php echo date("Y-m-d",strtotime("+3 day", strtotime($first_day))); ?>_8"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+3 day", strtotime($first_day))).'" AND place="8"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form79">
          <input type="hidden" name="value" value="79">
        </form>
      </td>
      <td id="check_table80">
        <form id="check_form80" action="./clean_check.php" method="post">
          <input type="checkbox" id="form80" name="date_place80" value="<?php echo date("Y-m-d",strtotime("+4 day", strtotime($first_day))); ?>_8"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+4 day", strtotime($first_day))).'" AND place="8"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form80">
          <input type="hidden" name="value" value="80">
        </form>
      </td>
    </tr>
    <tr>
      <td class="place">ゴミ置き場</td>
      <td id="check_table81">
        <form id="check_form81" action="./clean_check.php" method="post">
          <input type="checkbox" id="form81" name="date_place81" value="<?php echo date("Y-m-d",strtotime("-7 day", strtotime($first_day))); ?>_9"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-7 day", strtotime($first_day))).'" AND place="9"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form81">
          <input type="hidden" name="value" value="81">
        </form>
      </td>
      <td id="check_table82">
        <form id="check_form82" action="./clean_check.php" method="post">
          <input type="checkbox" id="form82" name="date_place82" value="<?php echo date("Y-m-d",strtotime("-6 day", strtotime($first_day))); ?>_9"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-6 day", strtotime($first_day))).'" AND place="9"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form82">
          <input type="hidden" name="value" value="82">
        </form>
      </td>
      <td id="check_table83">
        <form id="check_form83" action="./clean_check.php" method="post">
          <input type="checkbox" id="form83" name="date_place83" value="<?php echo date("Y-m-d",strtotime("-5 day", strtotime($first_day))); ?>_9"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-5 day", strtotime($first_day))).'" AND place="9"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form83">
          <input type="hidden" name="value" value="83">
        </form>
      </td>
      <td id="check_table84">
        <form id="check_form84" action="./clean_check.php" method="post">
          <input type="checkbox" id="form84" name="date_place84" value="<?php echo date("Y-m-d",strtotime("-4 day", strtotime($first_day))); ?>_9"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-4 day", strtotime($first_day))).'" AND place="9"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form84">
          <input type="hidden" name="value" value="84">
        </form>
      </td>
      <td id="check_table85">
        <form id="check_form85" action="./clean_check.php" method="post">
          <input type="checkbox" id="form85" name="date_place85" value="<?php echo date("Y-m-d",strtotime("-3 day", strtotime($first_day))); ?>_9"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-3 day", strtotime($first_day))).'" AND place="9"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form85">
          <input type="hidden" name="value" value="85">
        </form>
      </td>
      <td id="check_table86">
        <form id="check_form86" action="./clean_check.php" method="post">
          <input type="checkbox" id="form86" name="date_place86" value="<?php echo $first_day; ?>_9"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.$first_day.'" AND place="9"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form86">
          <input type="hidden" name="value" value="86">
        </form>
      </td>
      <td id="check_table87">
        <form id="check_form87" action="./clean_check.php" method="post">
          <input type="checkbox" id="form87" name="date_place87" value="<?php echo date("Y-m-d",strtotime("+1 day", strtotime($first_day))); ?>_9"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+1 day", strtotime($first_day))).'" AND place="9"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form87">
          <input type="hidden" name="value" value="87">
        </form>
      </td>
      <td id="check_table88">
        <form id="check_form88" action="./clean_check.php" method="post">
          <input type="checkbox" id="form88" name="date_place88" value="<?php echo date("Y-m-d",strtotime("+2 day", strtotime($first_day))); ?>_9"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+2 day", strtotime($first_day))).'" AND place="9"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form88">
          <input type="hidden" name="value" value="88">
        </form>
      </td>
      <td id="check_table89">
        <form id="check_form89" action="./clean_check.php" method="post">
          <input type="checkbox" id="form89" name="date_place89" value="<?php echo date("Y-m-d",strtotime("+3 day", strtotime($first_day))); ?>_9"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+3 day", strtotime($first_day))).'" AND place="9"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form89">
          <input type="hidden" name="value" value="89">
        </form>
      </td>
      <td id="check_table90">
        <form id="check_form90" action="./clean_check.php" method="post">
          <input type="checkbox" id="form90" name="date_place90" value="<?php echo date("Y-m-d",strtotime("+4 day", strtotime($first_day))); ?>_9"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+4 day", strtotime($first_day))).'" AND place="9"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form90">
          <input type="hidden" name="value" value="90">
        </form>
      </td>
    </tr>
    <tr>
      <td rowspan="2" class="color-change room">その他</td>
      <td class="place">階段</td>
      <td id="check_table91">
        <form id="check_form91" action="./clean_check.php" method="post">
          <input type="checkbox" id="form91" name="date_place91" value="<?php echo date("Y-m-d",strtotime("-7 day", strtotime($first_day))); ?>_10"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-7 day", strtotime($first_day))).'" AND place="10"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form91">
          <input type="hidden" name="value" value="91">
        </form>
      </td>
      <td id="check_table92">
        <form id="check_form92" action="./clean_check.php" method="post">
          <input type="checkbox" id="form92" name="date_place92" value="<?php echo date("Y-m-d",strtotime("-6 day", strtotime($first_day))); ?>_10"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-6 day", strtotime($first_day))).'" AND place="10"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form92">
          <input type="hidden" name="value" value="92">
        </form>
      </td>
      <td id="check_table93">
        <form id="check_form93" action="./clean_check.php" method="post">
          <input type="checkbox" id="form93" name="date_place93" value="<?php echo date("Y-m-d",strtotime("-5 day", strtotime($first_day))); ?>_10"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-5 day", strtotime($first_day))).'" AND place="10"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form93">
          <input type="hidden" name="value" value="93">
        </form>
      </td>
      <td id="check_table94">
        <form id="check_form94" action="./clean_check.php" method="post">
          <input type="checkbox" id="form94" name="date_place94" value="<?php echo date("Y-m-d",strtotime("-4 day", strtotime($first_day))); ?>_10"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-4 day", strtotime($first_day))).'" AND place="10"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form94">
          <input type="hidden" name="value" value="94">
        </form>
      </td>
      <td id="check_table95">
        <form id="check_form95" action="./clean_check.php" method="post">
          <input type="checkbox" id="form95" name="date_place95" value="<?php echo date("Y-m-d",strtotime("-3 day", strtotime($first_day))); ?>_10"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-3 day", strtotime($first_day))).'" AND place="10"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form95">
          <input type="hidden" name="value" value="95">
        </form>
      </td>
      <td id="check_table96">
        <form id="check_form96" action="./clean_check.php" method="post">
          <input type="checkbox" id="form96" name="date_place96" value="<?php echo $first_day; ?>_10"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.$first_day.'" AND place="10"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form96">
          <input type="hidden" name="value" value="96">
        </form>
      </td>
      <td id="check_table97">
        <form id="check_form97" action="./clean_check.php" method="post">
          <input type="checkbox" id="form97" name="date_place97" value="<?php echo date("Y-m-d",strtotime("+1 day", strtotime($first_day))); ?>_10"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+1 day", strtotime($first_day))).'" AND place="10"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form97">
          <input type="hidden" name="value" value="97">
        </form>
      </td>
      <td id="check_table98">
        <form id="check_form98" action="./clean_check.php" method="post">
          <input type="checkbox" id="form98" name="date_place98" value="<?php echo date("Y-m-d",strtotime("+2 day", strtotime($first_day))); ?>_10"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+2 day", strtotime($first_day))).'" AND place="10"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form98">
          <input type="hidden" name="value" value="98">
        </form>
      </td>
      <td id="check_table99">
        <form id="check_form99" action="./clean_check.php" method="post">
          <input type="checkbox" id="form99" name="date_place99" value="<?php echo date("Y-m-d",strtotime("+3 day", strtotime($first_day))); ?>_10"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+3 day", strtotime($first_day))).'" AND place="10"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form99">
          <input type="hidden" name="value" value="99">
        </form>
      </td>
      <td id="check_table100">
        <form id="check_form100" action="./clean_check.php" method="post">
          <input type="checkbox" id="form100" name="date_place100" value="<?php echo date("Y-m-d",strtotime("+4 day", strtotime($first_day))); ?>_10"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+4 day", strtotime($first_day))).'" AND place="10"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form100">
          <input type="hidden" name="value" value="100">
        </form>
      </td>
    </tr>
    <tr>
      <td class="place">下駄箱</td>
      <td id="check_table101">
        <form id="check_form101" action="./clean_check.php" method="post">
          <input type="checkbox" id="form101" name="date_place101" value="<?php echo date("Y-m-d",strtotime("-7 day", strtotime($first_day))); ?>_11"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-7 day", strtotime($first_day))).'" AND place="11"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form101">
          <input type="hidden" name="value" value="101">
        </form>
      </td>
      <td id="check_table102">
        <form id="check_form102" action="./clean_check.php" method="post">
          <input type="checkbox" id="form102" name="date_place102" value="<?php echo date("Y-m-d",strtotime("-6 day", strtotime($first_day))); ?>_11"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-6 day", strtotime($first_day))).'" AND place="11"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form102">
          <input type="hidden" name="value" value="102">
        </form>
      </td>
      <td id="check_table103">
        <form id="check_form103" action="./clean_check.php" method="post">
          <input type="checkbox" id="form103" name="date_place103" value="<?php echo date("Y-m-d",strtotime("-5 day", strtotime($first_day))); ?>_11"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-5 day", strtotime($first_day))).'" AND place="11"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form103">
          <input type="hidden" name="value" value="103">
        </form>
      </td>
      <td id="check_table104">
        <form id="check_form104" action="./clean_check.php" method="post">
          <input type="checkbox" id="form104" name="date_place104" value="<?php echo date("Y-m-d",strtotime("-4 day", strtotime($first_day))); ?>_11"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-4 day", strtotime($first_day))).'" AND place="11"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form104">
          <input type="hidden" name="value" value="104">
        </form>
      </td>
      <td id="check_table105">
        <form id="check_form105" action="./clean_check.php" method="post">
          <input type="checkbox" id="form105" name="date_place105" value="<?php echo date("Y-m-d",strtotime("-3 day", strtotime($first_day))); ?>_11"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-3 day", strtotime($first_day))).'" AND place="11"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form105">
          <input type="hidden" name="value" value="105">
        </form>
      </td>
      <td id="check_table106">
        <form id="check_form106" action="./clean_check.php" method="post">
          <input type="checkbox" id="form106" name="date_place106" value="<?php echo $first_day; ?>_11"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.$first_day.'" AND place="11"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form106">
          <input type="hidden" name="value" value="106">
        </form>
      </td>
      <td id="check_table107">
        <form id="check_form107" action="./clean_check.php" method="post">
          <input type="checkbox" id="form107" name="date_place107" value="<?php echo date("Y-m-d",strtotime("+1 day", strtotime($first_day))); ?>_11"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+1 day", strtotime($first_day))).'" AND place="11"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form107">
          <input type="hidden" name="value" value="107">
        </form>
      </td>
      <td id="check_table108">
        <form id="check_form108" action="./clean_check.php" method="post">
          <input type="checkbox" id="form108" name="date_place108" value="<?php echo date("Y-m-d",strtotime("+2 day", strtotime($first_day))); ?>_11"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+2 day", strtotime($first_day))).'" AND place="11"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form108">
          <input type="hidden" name="value" value="108">
        </form>
      </td>
      <td id="check_table109">
        <form id="check_form109" action="./clean_check.php" method="post">
          <input type="checkbox" id="form109" name="date_place109" value="<?php echo date("Y-m-d",strtotime("+3 day", strtotime($first_day))); ?>_11"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+3 day", strtotime($first_day))).'" AND place="11"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form109">
          <input type="hidden" name="value" value="109">
        </form>
      </td>
            <td id="check_table110">
        <form id="check_form110" action="./clean_check.php" method="post">
          <input type="checkbox" id="form110" name="date_place110" value="<?php echo date("Y-m-d",strtotime("+4 day", strtotime($first_day))); ?>_11"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+4 day", strtotime($first_day))).'" AND place="11"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form110">
          <input type="hidden" name="value" value="110">
        </form>
      </td>
    </tr>
    <tr>
      <td class="border-none">5F</td>
      <td class="border-none"></td>
      <td class="border-none"></td>
      <td class="border-none"></td>
      <td class="border-none"></td>
      <td class="border-none"></td>
      <td class="border-none"></td>
      <td class="border-none"></td>
      <td class="border-none"></td>
      <td class="border-none"></td>
      <td class="border-none"></td>
      <td class="border-none"></td>
    </tr>
    <tr>
      <td rowspan="2" class="border-bottom color-change room">会議室</td>
      <td class="place">机</td>
      <td id="check_table111">
        <form id="check_form111" action="./clean_check.php" method="post">
          <input type="checkbox" id="form111" name="date_place111" value="<?php echo date("Y-m-d",strtotime("-7 day", strtotime($first_day))); ?>_12"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-7 day", strtotime($first_day))).'" AND place="12"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form111">
          <input type="hidden" name="value" value="111">
        </form>
      </td>
      <td id="check_table112">
        <form id="check_form112" action="./clean_check.php" method="post">
          <input type="checkbox" id="form112" name="date_place112" value="<?php echo date("Y-m-d",strtotime("-6 day", strtotime($first_day))); ?>_12"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-6 day", strtotime($first_day))).'" AND place="12"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form112">
          <input type="hidden" name="value" value="112">
        </form>
      </td>
      <td id="check_table113">
        <form id="check_form113" action="./clean_check.php" method="post">
          <input type="checkbox" id="form113" name="date_place113" value="<?php echo date("Y-m-d",strtotime("-5 day", strtotime($first_day))); ?>_12"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-5 day", strtotime($first_day))).'" AND place="12"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form113">
          <input type="hidden" name="value" value="113">
        </form>
      </td>
      <td id="check_table114">
        <form id="check_form114" action="./clean_check.php" method="post">
          <input type="checkbox" id="form114" name="date_place114" value="<?php echo date("Y-m-d",strtotime("-4 day", strtotime($first_day))); ?>_12"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-4 day", strtotime($first_day))).'" AND place="12"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form114">
          <input type="hidden" name="value" value="114">
        </form>
      </td>
      <td id="check_table115">
        <form id="check_form115" action="./clean_check.php" method="post">
          <input type="checkbox" id="form115" name="date_place115" value="<?php echo date("Y-m-d",strtotime("-3 day", strtotime($first_day))); ?>_12"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-3 day", strtotime($first_day))).'" AND place="12"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form115">
          <input type="hidden" name="value" value="115">
        </form>
      </td>
      <td id="check_table116">
        <form id="check_form116" action="./clean_check.php" method="post">
          <input type="checkbox" id="form116" name="date_place116" value="<?php echo $first_day; ?>_12"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.$first_day.'" AND place="12"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form116">
          <input type="hidden" name="value" value="116">
        </form>
      </td>
      <td id="check_table117">
        <form id="check_form117" action="./clean_check.php" method="post">
          <input type="checkbox" id="form117" name="date_place117" value="<?php echo date("Y-m-d",strtotime("+1 day", strtotime($first_day))); ?>_12"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+1 day", strtotime($first_day))).'" AND place="12"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form117">
          <input type="hidden" name="value" value="117">
        </form>
      </td>
      <td id="check_table118">
        <form id="check_form118" action="./clean_check.php" method="post">
          <input type="checkbox" id="form118" name="date_place118" value="<?php echo date("Y-m-d",strtotime("+2 day", strtotime($first_day))); ?>_12"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+2 day", strtotime($first_day))).'" AND place="12"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form118">
          <input type="hidden" name="value" value="118">
        </form>
      </td>
      <td id="check_table119">
        <form id="check_form119" action="./clean_check.php" method="post">
          <input type="checkbox" id="form119" name="date_place119" value="<?php echo date("Y-m-d",strtotime("+3 day", strtotime($first_day))); ?>_12"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+3 day", strtotime($first_day))).'" AND place="12"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form119">
          <input type="hidden" name="value" value="119">
        </form>
      </td>
      <td id="check_table120">
        <form id="check_form120" action="./clean_check.php" method="post">
          <input type="checkbox" id="form120" name="date_place120" value="<?php echo date("Y-m-d",strtotime("+4 day", strtotime($first_day))); ?>_12"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+4 day", strtotime($first_day))).'" AND place="12"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form120">
          <input type="hidden" name="value" value="120">
        </form>
      </td>
    </tr>
    <tr>
      <td class="place">床</td>
      <td id="check_table121">
        <form id="check_form121" action="./clean_check.php" method="post">
          <input type="checkbox" id="form121" name="date_place121" value="<?php echo date("Y-m-d",strtotime("-7 day", strtotime($first_day))); ?>_13"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-7 day", strtotime($first_day))).'" AND place="13"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form121">
          <input type="hidden" name="value" value="121">
        </form>
      </td>
      <td id="check_table122">
        <form id="check_form122" action="./clean_check.php" method="post">
          <input type="checkbox" id="form122" name="date_place122" value="<?php echo date("Y-m-d",strtotime("-6 day", strtotime($first_day))); ?>_13"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-6 day", strtotime($first_day))).'" AND place="13"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form122">
          <input type="hidden" name="value" value="122">
        </form>
      </td>
      <td id="check_table123">
        <form id="check_form123" action="./clean_check.php" method="post">
          <input type="checkbox" id="form123" name="date_place123" value="<?php echo date("Y-m-d",strtotime("-5 day", strtotime($first_day))); ?>_13"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-5 day", strtotime($first_day))).'" AND place="13"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form123">
          <input type="hidden" name="value" value="123">
        </form>
      </td>
      <td id="check_table124">
        <form id="check_form124" action="./clean_check.php" method="post">
          <input type="checkbox" id="form124" name="date_place124" value="<?php echo date("Y-m-d",strtotime("-4 day", strtotime($first_day))); ?>_13"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-4 day", strtotime($first_day))).'" AND place="13"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form124">
          <input type="hidden" name="value" value="124">
        </form>
      </td>
      <td id="check_table125">
        <form id="check_form125" action="./clean_check.php" method="post">
          <input type="checkbox" id="form125" name="date_place125" value="<?php echo date("Y-m-d",strtotime("-3 day", strtotime($first_day))); ?>_13"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-3 day", strtotime($first_day))).'" AND place="13"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form125">
          <input type="hidden" name="value" value="125">
        </form>
      </td>
      <td id="check_table126">
        <form id="check_form126" action="./clean_check.php" method="post">
          <input type="checkbox" id="form126" name="date_place126" value="<?php echo $first_day; ?>_13"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.$first_day.'" AND place="13"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form126">
          <input type="hidden" name="value" value="126">
        </form>
      </td>
      <td id="check_table127">
        <form id="check_form127" action="./clean_check.php" method="post">
          <input type="checkbox" id="form127" name="date_place127" value="<?php echo date("Y-m-d",strtotime("+1 day", strtotime($first_day))); ?>_13"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+1 day", strtotime($first_day))).'" AND place="13"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form127">
          <input type="hidden" name="value" value="127">
        </form>
      </td>
      <td id="check_table128">
        <form id="check_form128" action="./clean_check.php" method="post">
          <input type="checkbox" id="form128" name="date_place128" value="<?php echo date("Y-m-d",strtotime("+2 day", strtotime($first_day))); ?>_13"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+2 day", strtotime($first_day))).'" AND place="13"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form128">
          <input type="hidden" name="value" value="128">
        </form>
      </td>
      <td id="check_table129">
        <form id="check_form129" action="./clean_check.php" method="post">
          <input type="checkbox" id="form129" name="date_place129" value="<?php echo date("Y-m-d",strtotime("+3 day", strtotime($first_day))); ?>_13"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+3 day", strtotime($first_day))).'" AND place="13"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form129">
          <input type="hidden" name="value" value="129">
        </form>
      </td>
      <td id="check_table130">
        <form id="check_form130" action="./clean_check.php" method="post">
          <input type="checkbox" id="form130" name="date_place130" value="<?php echo date("Y-m-d",strtotime("+4 day", strtotime($first_day))); ?>_13"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+4 day", strtotime($first_day))).'" AND place="13"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form130">
          <input type="hidden" name="value" value="130">
        </form>
      </td>
    </tr>
    <tr>
      <td rowspan="4" class="color-change room">その他</td>
      <td class="place">机</td>
      <td id="check_table131">
        <form id="check_form131" action="./clean_check.php" method="post">
          <input type="checkbox" id="form131" name="date_place131" value="<?php echo date("Y-m-d",strtotime("-7 day", strtotime($first_day))); ?>_14"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-7 day", strtotime($first_day))).'" AND place="14"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form131">
          <input type="hidden" name="value" value="131">
        </form>
      </td>
      <td id="check_table132">
        <form id="check_form132" action="./clean_check.php" method="post">
          <input type="checkbox" id="form132" name="date_place132" value="<?php echo date("Y-m-d",strtotime("-6 day", strtotime($first_day))); ?>_14"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-6 day", strtotime($first_day))).'" AND place="14"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form132">
          <input type="hidden" name="value" value="132">
        </form>
      </td>
      <td id="check_table133">
        <form id="check_form133" action="./clean_check.php" method="post">
          <input type="checkbox" id="form133" name="date_place133" value="<?php echo date("Y-m-d",strtotime("-5 day", strtotime($first_day))); ?>_14"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-5 day", strtotime($first_day))).'" AND place="14"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form133">
          <input type="hidden" name="value" value="133">
        </form>
      </td>
      <td id="check_table134">
        <form id="check_form134" action="./clean_check.php" method="post">
          <input type="checkbox" id="form134" name="date_place134" value="<?php echo date("Y-m-d",strtotime("-4 day", strtotime($first_day))); ?>_14"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-4 day", strtotime($first_day))).'" AND place="14"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form134">
          <input type="hidden" name="value" value="134">
        </form>
      </td>
      <td id="check_table135">
        <form id="check_form135" action="./clean_check.php" method="post">
          <input type="checkbox" id="form135" name="date_place135" value="<?php echo date("Y-m-d",strtotime("-3 day", strtotime($first_day))); ?>_14"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-3 day", strtotime($first_day))).'" AND place="14"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form135">
          <input type="hidden" name="value" value="135">
        </form>
      </td>
      <td id="check_table136">
        <form id="check_form136" action="./clean_check.php" method="post">
          <input type="checkbox" id="form136" name="date_place136" value="<?php echo $first_day; ?>_14"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.$first_day.'" AND place="14"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form136">
          <input type="hidden" name="value" value="136">
        </form>
      </td>
      <td id="check_table137">
        <form id="check_form137" action="./clean_check.php" method="post">
          <input type="checkbox" id="form137" name="date_place137" value="<?php echo date("Y-m-d",strtotime("+1 day", strtotime($first_day))); ?>_14"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+1 day", strtotime($first_day))).'" AND place="14"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form137">
          <input type="hidden" name="value" value="137">
        </form>
      </td>
      <td id="check_table138">
        <form id="check_form138" action="./clean_check.php" method="post">
          <input type="checkbox" id="form138" name="date_place138" value="<?php echo date("Y-m-d",strtotime("+2 day", strtotime($first_day))); ?>_14"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+2 day", strtotime($first_day))).'" AND place="14"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form138">
          <input type="hidden" name="value" value="138">
        </form>
      </td>
      <td id="check_table139">
        <form id="check_form139" action="./clean_check.php" method="post">
          <input type="checkbox" id="form139" name="date_place139" value="<?php echo date("Y-m-d",strtotime("+3 day", strtotime($first_day))); ?>_14"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+3 day", strtotime($first_day))).'" AND place="14"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form139">
          <input type="hidden" name="value" value="139">
        </form>
      </td>
      <td id="check_table140">
        <form id="check_form140" action="./clean_check.php" method="post">
          <input type="checkbox" id="form140" name="date_place140" value="<?php echo date("Y-m-d",strtotime("+4 day", strtotime($first_day))); ?>_14"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+4 day", strtotime($first_day))).'" AND place="14"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form140">
          <input type="hidden" name="value" value="140">
        </form>
      </td>
    </tr>
    <tr>
      <td class="place">トイレ</td>
      <td id="check_table141">
        <form id="check_form141" action="./clean_check.php" method="post">
          <input type="checkbox" id="form141" name="date_place141" value="<?php echo date("Y-m-d",strtotime("-7 day", strtotime($first_day))); ?>_15"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-7 day", strtotime($first_day))).'" AND place="15"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form141">
          <input type="hidden" name="value" value="141">
        </form>
      </td>
      <td id="check_table142">
        <form id="check_form142" action="./clean_check.php" method="post">
          <input type="checkbox" id="form142" name="date_place142" value="<?php echo date("Y-m-d",strtotime("-6 day", strtotime($first_day))); ?>_15"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-6 day", strtotime($first_day))).'" AND place="15"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form142">
          <input type="hidden" name="value" value="142">
        </form>
      </td>
      <td id="check_table143">
        <form id="check_form143" action="./clean_check.php" method="post">
          <input type="checkbox" id="form143" name="date_place143" value="<?php echo date("Y-m-d",strtotime("-5 day", strtotime($first_day))); ?>_15"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-5 day", strtotime($first_day))).'" AND place="15"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form143">
          <input type="hidden" name="value" value="143">
        </form>
      </td>
      <td id="check_table144">
        <form id="check_form144" action="./clean_check.php" method="post">
          <input type="checkbox" id="form144" name="date_place144" value="<?php echo date("Y-m-d",strtotime("-4 day", strtotime($first_day))); ?>_15"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-4 day", strtotime($first_day))).'" AND place="15"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form144">
          <input type="hidden" name="value" value="144">
        </form>
      </td>
      <td id="check_table145">
        <form id="check_form145" action="./clean_check.php" method="post">
          <input type="checkbox" id="form145" name="date_place145" value="<?php echo date("Y-m-d",strtotime("-3 day", strtotime($first_day))); ?>_15"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-3 day", strtotime($first_day))).'" AND place="15"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form145">
          <input type="hidden" name="value" value="145">
        </form>
      </td>
      <td id="check_table146">
        <form id="check_form146" action="./clean_check.php" method="post">
          <input type="checkbox" id="form146" name="date_place146" value="<?php echo $first_day; ?>_15"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.$first_day.'" AND place="15"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form146">
          <input type="hidden" name="value" value="146">
        </form>
      </td>
      <td id="check_table147">
        <form id="check_form147" action="./clean_check.php" method="post">
          <input type="checkbox" id="form147" name="date_place147" value="<?php echo date("Y-m-d",strtotime("+1 day", strtotime($first_day))); ?>_15"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+1 day", strtotime($first_day))).'" AND place="15"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form147">
          <input type="hidden" name="value" value="147">
        </form>
      </td>
      <td id="check_table148">
        <form id="check_form148" action="./clean_check.php" method="post">
          <input type="checkbox" id="form148" name="date_place148" value="<?php echo date("Y-m-d",strtotime("+2 day", strtotime($first_day))); ?>_15"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+2 day", strtotime($first_day))).'" AND place="15"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form148">
          <input type="hidden" name="value" value="148">
        </form>
      </td>
      <td id="check_table149">
        <form id="check_form149" action="./clean_check.php" method="post">
          <input type="checkbox" id="form149" name="date_place149" value="<?php echo date("Y-m-d",strtotime("+3 day", strtotime($first_day))); ?>_15"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+3 day", strtotime($first_day))).'" AND place="15"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form149">
          <input type="hidden" name="value" value="149">
        </form>
      </td>
      <td id="check_table150">
        <form id="check_form150" action="./clean_check.php" method="post">
          <input type="checkbox" id="form150" name="date_place150" value="<?php echo date("Y-m-d",strtotime("+4 day", strtotime($first_day))); ?>_15"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+4 day", strtotime($first_day))).'" AND place="15"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form150">
          <input type="hidden" name="value" value="150">
        </form>
      </td>
    </tr>
    <tr>
      <td class="place">廊下</td>
      <td id="check_table151">
        <form id="check_form151" action="./clean_check.php" method="post">
          <input type="checkbox" id="form151" name="date_place151" value="<?php echo date("Y-m-d",strtotime("-7 day", strtotime($first_day))); ?>_16"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-7 day", strtotime($first_day))).'" AND place="16"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form151">
          <input type="hidden" name="value" value="151">
        </form>
      </td>
      <td id="check_table152">
        <form id="check_form152" action="./clean_check.php" method="post">
          <input type="checkbox" id="form152" name="date_place152" value="<?php echo date("Y-m-d",strtotime("-6 day", strtotime($first_day))); ?>_16"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-6 day", strtotime($first_day))).'" AND place="16"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form152">
          <input type="hidden" name="value" value="152">
        </form>
      </td>
      <td id="check_table153">
        <form id="check_form153" action="./clean_check.php" method="post">
          <input type="checkbox" id="form153" name="date_place153" value="<?php echo date("Y-m-d",strtotime("-5 day", strtotime($first_day))); ?>_16"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-5 day", strtotime($first_day))).'" AND place="16"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form153">
          <input type="hidden" name="value" value="153">
        </form>
      </td>
      <td id="check_table154">
        <form id="check_form154" action="./clean_check.php" method="post">
          <input type="checkbox" id="form154" name="date_place154" value="<?php echo date("Y-m-d",strtotime("-4 day", strtotime($first_day))); ?>_16"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-4 day", strtotime($first_day))).'" AND place="16"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form154">
          <input type="hidden" name="value" value="154">
        </form>
      </td>
      <td id="check_table155">
        <form id="check_form155" action="./clean_check.php" method="post">
          <input type="checkbox" id="form155" name="date_place155" value="<?php echo date("Y-m-d",strtotime("-3 day", strtotime($first_day))); ?>_16"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-3 day", strtotime($first_day))).'" AND place="16"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form155">
          <input type="hidden" name="value" value="155">
        </form>
      </td>
      <td id="check_table156">
        <form id="check_form156" action="./clean_check.php" method="post">
          <input type="checkbox" id="form156" name="date_place156" value="<?php echo $first_day; ?>_16"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.$first_day.'" AND place="16"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form156">
          <input type="hidden" name="value" value="156">
        </form>
      </td>
      <td id="check_table157">
        <form id="check_form157" action="./clean_check.php" method="post">
          <input type="checkbox" id="form157" name="date_place157" value="<?php echo date("Y-m-d",strtotime("+1 day", strtotime($first_day))); ?>_16"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+1 day", strtotime($first_day))).'" AND place="16"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form157">
          <input type="hidden" name="value" value="157">
        </form>
      </td>
      <td id="check_table158">
        <form id="check_form158" action="./clean_check.php" method="post">
          <input type="checkbox" id="form158" name="date_place158" value="<?php echo date("Y-m-d",strtotime("+2 day", strtotime($first_day))); ?>_16"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+2 day", strtotime($first_day))).'" AND place="16"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form158">
          <input type="hidden" name="value" value="158">
        </form>
      </td>
      <td id="check_table159">
        <form id="check_form159" action="./clean_check.php" method="post">
          <input type="checkbox" id="form159" name="date_place159" value="<?php echo date("Y-m-d",strtotime("+3 day", strtotime($first_day))); ?>_16"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+3 day", strtotime($first_day))).'" AND place="16"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form159">
          <input type="hidden" name="value" value="159">
        </form>
      </td>
      <td id="check_table160">
        <form id="check_form160" action="./clean_check.php" method="post">
          <input type="checkbox" id="form160" name="date_place160" value="<?php echo date("Y-m-d",strtotime("+4 day", strtotime($first_day))); ?>_16"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+4 day", strtotime($first_day))).'" AND place="16"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form160">
          <input type="hidden" name="value" value="160">
        </form>
      </td>
    </tr>
    <tr>
      <td class="place">階段</td>
      <td id="check_table161">
        <form id="check_form161" action="./clean_check.php" method="post">
          <input type="checkbox" id="form161" name="date_place161" value="<?php echo date("Y-m-d",strtotime("-7 day", strtotime($first_day))); ?>_17"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-7 day", strtotime($first_day))).'" AND place="17"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form161">
          <input type="hidden" name="value" value="161">
        </form>
      </td>
      <td id="check_table162">
        <form id="check_form162" action="./clean_check.php" method="post">
          <input type="checkbox" id="form162" name="date_place162" value="<?php echo date("Y-m-d",strtotime("-6 day", strtotime($first_day))); ?>_17"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-6 day", strtotime($first_day))).'" AND place="17"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form162">
          <input type="hidden" name="value" value="162">
        </form>
      </td>
      <td id="check_table163">
        <form id="check_form163" action="./clean_check.php" method="post">
          <input type="checkbox" id="form163" name="date_place163" value="<?php echo date("Y-m-d",strtotime("-5 day", strtotime($first_day))); ?>_17"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-5 day", strtotime($first_day))).'" AND place="17"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form163">
          <input type="hidden" name="value" value="163">
        </form>
      </td>
      <td id="check_table164">
        <form id="check_form164" action="./clean_check.php" method="post">
          <input type="checkbox" id="form164" name="date_place164" value="<?php echo date("Y-m-d",strtotime("-4 day", strtotime($first_day))); ?>_17"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-4 day", strtotime($first_day))).'" AND place="17"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form164">
          <input type="hidden" name="value" value="164">
        </form>
      </td>
      <td id="check_table165">
        <form id="check_form165" action="./clean_check.php" method="post">
          <input type="checkbox" id="form165" name="date_place165" value="<?php echo date("Y-m-d",strtotime("-3 day", strtotime($first_day))); ?>_17"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-3 day", strtotime($first_day))).'" AND place="17"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form165">
          <input type="hidden" name="value" value="165">
        </form>
      </td>
      <td id="check_table166">
        <form id="check_form166" action="./clean_check.php" method="post">
          <input type="checkbox" id="form166" name="date_place166" value="<?php echo $first_day; ?>_17"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.$first_day.'" AND place="17"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form166">
          <input type="hidden" name="value" value="166">
        </form>
      </td>
      <td id="check_table167">
        <form id="check_form167" action="./clean_check.php" method="post">
          <input type="checkbox" id="form167" name="date_place167" value="<?php echo date("Y-m-d",strtotime("+1 day", strtotime($first_day))); ?>_17"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+1 day", strtotime($first_day))).'" AND place="17"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form167">
          <input type="hidden" name="value" value="167">
        </form>
      </td>
      <td id="check_table168">
        <form id="check_form168" action="./clean_check.php" method="post">
          <input type="checkbox" id="form168" name="date_place168" value="<?php echo date("Y-m-d",strtotime("+2 day", strtotime($first_day))); ?>_17"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+2 day", strtotime($first_day))).'" AND place="17"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form168">
          <input type="hidden" name="value" value="168">
        </form>
      </td>
      <td id="check_table169">
        <form id="check_form169" action="./clean_check.php" method="post">
          <input type="checkbox" id="form169" name="date_place169" value="<?php echo date("Y-m-d",strtotime("+3 day", strtotime($first_day))); ?>_17"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+3 day", strtotime($first_day))).'" AND place="17"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form169">
          <input type="hidden" name="value" value="169">
        </form>
      </td>
      <td id="check_table170">
        <form id="check_form170" action="./clean_check.php" method="post">
          <input type="checkbox" id="form170" name="date_place170" value="<?php echo date("Y-m-d",strtotime("+4 day", strtotime($first_day))); ?>_17"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+4 day", strtotime($first_day))).'" AND place="17"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form170">
          <input type="hidden" name="value" value="170">
        </form>
      </td>
    </tr>
    <tr>
      <td class="border-none">　</td>
      <td class="border-none"></td>
      <td class="border-none"></td>
      <td class="border-none"></td>
      <td class="border-none"></td>
      <td class="border-none"></td>
      <td class="border-none"></td>
      <td class="border-none"></td>
      <td class="border-none"></td>
      <td class="border-none"></td>
      <td class="border-none"></td>
      <td class="border-none"></td>
    </tr>
    <tr>
      <td class="border-none"></td>
      <td class="color-change place">ゴミ</td>
      <td class="diagonal-line"></td>
      <td id="check_table171">
        <form id="check_form171" action="./clean_check.php" method="post">
          <input type="checkbox" id="form171" name="date_place171" value="<?php echo date("Y-m-d",strtotime("-6 day", strtotime($first_day))); ?>_18"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-6 day", strtotime($first_day))).'" AND place="18"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form171">
          <input type="hidden" name="value" value="171">
        </form>
      </td>
      <td class="diagonal-line"></td>
      <td class="diagonal-line"></td>
      <td id="check_table172">
        <form id="check_form172" action="./clean_check.php" method="post">
          <input type="checkbox" id="form172" name="date_place172" value="<?php echo date("Y-m-d",strtotime("-3 day", strtotime($first_day))); ?>_18"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("-3 day", strtotime($first_day))).'" AND place="18"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form172">
          <input type="hidden" name="value" value="172">
        </form>
      </td>
      <td class="diagonal-line"></td>
      <td id="check_table173">
        <form id="check_form173" action="./clean_check.php" method="post">
          <input type="checkbox" id="form173" name="date_place173" value="<?php echo date("Y-m-d",strtotime("+1 day", strtotime($first_day))); ?>_18"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+1 day", strtotime($first_day))).'" AND place="18"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form173">
          <input type="hidden" name="value" value="173">
        </form>
      </td>
      <td class="diagonal-line"></td>
      <td class="diagonal-line"></td>
      <td id="check_table174">
        <form id="check_form174" action="./clean_check.php" method="post">
          <input type="checkbox" id="form174" name="date_place174" value="<?php echo date("Y-m-d",strtotime("+4 day", strtotime($first_day))); ?>_18"
            <?php
              $sql3 = 'SELECT * from clean_check where date="'.date("Y-m-d",strtotime("+4 day", strtotime($first_day))).'" AND place="18"';
              $stmt3 = $dbh->prepare($sql3);
              $stmt3->execute();
              $trackfarm_kintai_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
              if (count($trackfarm_kintai_list3) == 1) {echo "checked";}
            ?>
          ><label for="form174">
          <input type="hidden" name="value" value="174">
        </form>
      </td>
    </tr>
  </table>

  <a href="home.php" class="return" role="button" aria-pressed="true"><img src="./img/return2.png"></a>
  <a href="clean_admin.php" class="admin" role="button" aria-pressed="true"><img src="./img/admin.png" alt="担当管理"></a>

  <script>

  // for (let step = 1; step <= 10; step++) {
  //   let str = "check_table".concat("", step);
  //   let str2 = "check_form".concat("", step);

  //   if(document.getElementById(str).change== true) {
  //     document.getElementById(str).addEventListener("change", function(e){
  //       document.forms.str2.submit();
  //     });
  //   }
  // }

  document.getElementById("check_table1").addEventListener("change", function(e){
    document.forms.check_form1.submit();
  });
  document.getElementById("check_table2").addEventListener("change", function(e){
    document.forms.check_form2.submit();
  });
  document.getElementById("check_table3").addEventListener("change", function(e){
    document.forms.check_form3.submit();
  });
  document.getElementById("check_table4").addEventListener("change", function(e){
    document.forms.check_form4.submit();
  });
  document.getElementById("check_table5").addEventListener("change", function(e){
    document.forms.check_form5.submit();
  });
  document.getElementById("check_table6").addEventListener("change", function(e){
    document.forms.check_form6.submit();
  });
  document.getElementById("check_table7").addEventListener("change", function(e){
    document.forms.check_form7.submit();
  });
  document.getElementById("check_table8").addEventListener("change", function(e){
    document.forms.check_form8.submit();
  });
  document.getElementById("check_table9").addEventListener("change", function(e){
    document.forms.check_form9.submit();
  });
  document.getElementById("check_table10").addEventListener("change", function(e){
    document.forms.check_form10.submit();
  });
  document.getElementById("check_table11").addEventListener("change", function(e){
    document.forms.check_form11.submit();
  });
  document.getElementById("check_table12").addEventListener("change", function(e){
    document.forms.check_form12.submit();
  });
  document.getElementById("check_table13").addEventListener("change", function(e){
    document.forms.check_form13.submit();
  });
  document.getElementById("check_table14").addEventListener("change", function(e){
    document.forms.check_form14.submit();
  });
  document.getElementById("check_table15").addEventListener("change", function(e){
    document.forms.check_form15.submit();
  });
  document.getElementById("check_table16").addEventListener("change", function(e){
    document.forms.check_form16.submit();
  });
  document.getElementById("check_table17").addEventListener("change", function(e){
    document.forms.check_form17.submit();
  });
  document.getElementById("check_table18").addEventListener("change", function(e){
    document.forms.check_form18.submit();
  });
  document.getElementById("check_table19").addEventListener("change", function(e){
    document.forms.check_form19.submit();
  });
  document.getElementById("check_table20").addEventListener("change", function(e){
    document.forms.check_form20.submit();
  });
  document.getElementById("check_table21").addEventListener("change", function(e){
    document.forms.check_form21.submit();
  });
  document.getElementById("check_table22").addEventListener("change", function(e){
    document.forms.check_form22.submit();
  });
  document.getElementById("check_table23").addEventListener("change", function(e){
    document.forms.check_form23.submit();
  });
  document.getElementById("check_table24").addEventListener("change", function(e){
    document.forms.check_form24.submit();
  });
  document.getElementById("check_table25").addEventListener("change", function(e){
    document.forms.check_form25.submit();
  });
  document.getElementById("check_table26").addEventListener("change", function(e){
    document.forms.check_form26.submit();
  });
  document.getElementById("check_table27").addEventListener("change", function(e){
    document.forms.check_form27.submit();
  });
  document.getElementById("check_table28").addEventListener("change", function(e){
    document.forms.check_form28.submit();
  });
  document.getElementById("check_table29").addEventListener("change", function(e){
    document.forms.check_form29.submit();
  });
  document.getElementById("check_table30").addEventListener("change", function(e){
    document.forms.check_form30.submit();
  });
  document.getElementById("check_table31").addEventListener("change", function(e){
    document.forms.check_form31.submit();
  });
  document.getElementById("check_table32").addEventListener("change", function(e){
    document.forms.check_form32.submit();
  });
  document.getElementById("check_table33").addEventListener("change", function(e){
    document.forms.check_form33.submit();
  });
  document.getElementById("check_table34").addEventListener("change", function(e){
    document.forms.check_form34.submit();
  });
  document.getElementById("check_table35").addEventListener("change", function(e){
    document.forms.check_form35.submit();
  });
  document.getElementById("check_table36").addEventListener("change", function(e){
    document.forms.check_form36.submit();
  });
  document.getElementById("check_table37").addEventListener("change", function(e){
    document.forms.check_form37.submit();
  });
  document.getElementById("check_table38").addEventListener("change", function(e){
    document.forms.check_form38.submit();
  });
  document.getElementById("check_table39").addEventListener("change", function(e){
    document.forms.check_form39.submit();
  });
  document.getElementById("check_table40").addEventListener("change", function(e){
    document.forms.check_form40.submit();
  });
  document.getElementById("check_table41").addEventListener("change", function(e){
    document.forms.check_form41.submit();
  });
  document.getElementById("check_table42").addEventListener("change", function(e){
    document.forms.check_form42.submit();
  });
  document.getElementById("check_table43").addEventListener("change", function(e){
    document.forms.check_form43.submit();
  });
  document.getElementById("check_table44").addEventListener("change", function(e){
    document.forms.check_form44.submit();
  });
  document.getElementById("check_table45").addEventListener("change", function(e){
    document.forms.check_form45.submit();
  });
  document.getElementById("check_table46").addEventListener("change", function(e){
    document.forms.check_form46.submit();
  });
  document.getElementById("check_table47").addEventListener("change", function(e){
    document.forms.check_form47.submit();
  });
  document.getElementById("check_table48").addEventListener("change", function(e){
    document.forms.check_form48.submit();
  });
  document.getElementById("check_table49").addEventListener("change", function(e){
    document.forms.check_form49.submit();
  });
  document.getElementById("check_table50").addEventListener("change", function(e){
    document.forms.check_form50.submit();
  });
  document.getElementById("check_table51").addEventListener("change", function(e){
    document.forms.check_form51.submit();
  });
  document.getElementById("check_table52").addEventListener("change", function(e){
    document.forms.check_form52.submit();
  });
  document.getElementById("check_table53").addEventListener("change", function(e){
    document.forms.check_form53.submit();
  });
  document.getElementById("check_table54").addEventListener("change", function(e){
    document.forms.check_form54.submit();
  });
  document.getElementById("check_table55").addEventListener("change", function(e){
    document.forms.check_form55.submit();
  });
  document.getElementById("check_table56").addEventListener("change", function(e){
    document.forms.check_form56.submit();
  });
  document.getElementById("check_table57").addEventListener("change", function(e){
    document.forms.check_form57.submit();
  });
  document.getElementById("check_table58").addEventListener("change", function(e){
    document.forms.check_form58.submit();
  });
  document.getElementById("check_table59").addEventListener("change", function(e){
    document.forms.check_form59.submit();
  });
  document.getElementById("check_table60").addEventListener("change", function(e){
    document.forms.check_form60.submit();
  });
  document.getElementById("check_table61").addEventListener("change", function(e){
    document.forms.check_form61.submit();
  });
  document.getElementById("check_table62").addEventListener("change", function(e){
    document.forms.check_form62.submit();
  });
  document.getElementById("check_table63").addEventListener("change", function(e){
    document.forms.check_form63.submit();
  });
  document.getElementById("check_table64").addEventListener("change", function(e){
    document.forms.check_form64.submit();
  });
  document.getElementById("check_table65").addEventListener("change", function(e){
    document.forms.check_form65.submit();
  });
  document.getElementById("check_table66").addEventListener("change", function(e){
    document.forms.check_form66.submit();
  });
  document.getElementById("check_table67").addEventListener("change", function(e){
    document.forms.check_form67.submit();
  });
  document.getElementById("check_table68").addEventListener("change", function(e){
    document.forms.check_form68.submit();
  });
  document.getElementById("check_table69").addEventListener("change", function(e){
    document.forms.check_form69.submit();
  });
  document.getElementById("check_table70").addEventListener("change", function(e){
    document.forms.check_form70.submit();
  });
  document.getElementById("check_table71").addEventListener("change", function(e){
    document.forms.check_form71.submit();
  });
  document.getElementById("check_table72").addEventListener("change", function(e){
    document.forms.check_form72.submit();
  });
  document.getElementById("check_table73").addEventListener("change", function(e){
    document.forms.check_form73.submit();
  });
  document.getElementById("check_table74").addEventListener("change", function(e){
    document.forms.check_form74.submit();
  });
  document.getElementById("check_table75").addEventListener("change", function(e){
    document.forms.check_form75.submit();
  });
  document.getElementById("check_table76").addEventListener("change", function(e){
    document.forms.check_form76.submit();
  });
  document.getElementById("check_table77").addEventListener("change", function(e){
    document.forms.check_form77.submit();
  });
  document.getElementById("check_table78").addEventListener("change", function(e){
    document.forms.check_form78.submit();
  });
  document.getElementById("check_table79").addEventListener("change", function(e){
    document.forms.check_form79.submit();
  });
  document.getElementById("check_table80").addEventListener("change", function(e){
    document.forms.check_form80.submit();
  });
  document.getElementById("check_table81").addEventListener("change", function(e){
    document.forms.check_form81.submit();
  });
  document.getElementById("check_table82").addEventListener("change", function(e){
    document.forms.check_form82.submit();
  });
  document.getElementById("check_table83").addEventListener("change", function(e){
    document.forms.check_form83.submit();
  });
  document.getElementById("check_table84").addEventListener("change", function(e){
    document.forms.check_form84.submit();
  });
  document.getElementById("check_table85").addEventListener("change", function(e){
    document.forms.check_form85.submit();
  });
  document.getElementById("check_table86").addEventListener("change", function(e){
    document.forms.check_form86.submit();
  });
  document.getElementById("check_table87").addEventListener("change", function(e){
    document.forms.check_form87.submit();
  });
  document.getElementById("check_table88").addEventListener("change", function(e){
    document.forms.check_form88.submit();
  });
  document.getElementById("check_table89").addEventListener("change", function(e){
    document.forms.check_form89.submit();
  });
  document.getElementById("check_table90").addEventListener("change", function(e){
    document.forms.check_form90.submit();
  });
  document.getElementById("check_table91").addEventListener("change", function(e){
    document.forms.check_form91.submit();
  });
  document.getElementById("check_table92").addEventListener("change", function(e){
    document.forms.check_form92.submit();
  });
  document.getElementById("check_table93").addEventListener("change", function(e){
    document.forms.check_form93.submit();
  });
  document.getElementById("check_table94").addEventListener("change", function(e){
    document.forms.check_form94.submit();
  });
  document.getElementById("check_table95").addEventListener("change", function(e){
    document.forms.check_form95.submit();
  });
  document.getElementById("check_table96").addEventListener("change", function(e){
    document.forms.check_form96.submit();
  });
  document.getElementById("check_table97").addEventListener("change", function(e){
    document.forms.check_form97.submit();
  });
  document.getElementById("check_table98").addEventListener("change", function(e){
    document.forms.check_form98.submit();
  });
  document.getElementById("check_table99").addEventListener("change", function(e){
    document.forms.check_form99.submit();
  });
  document.getElementById("check_table100").addEventListener("change", function(e){
    document.forms.check_form100.submit();
  });
  document.getElementById("check_table101").addEventListener("change", function(e){
    document.forms.check_form101.submit();
  });
  document.getElementById("check_table102").addEventListener("change", function(e){
    document.forms.check_form102.submit();
  });
  document.getElementById("check_table103").addEventListener("change", function(e){
    document.forms.check_form103.submit();
  });
  document.getElementById("check_table104").addEventListener("change", function(e){
    document.forms.check_form104.submit();
  });
  document.getElementById("check_table105").addEventListener("change", function(e){
    document.forms.check_form105.submit();
  });
  document.getElementById("check_table106").addEventListener("change", function(e){
    document.forms.check_form106.submit();
  });
  document.getElementById("check_table107").addEventListener("change", function(e){
    document.forms.check_form107.submit();
  });
  document.getElementById("check_table108").addEventListener("change", function(e){
    document.forms.check_form108.submit();
  });
  document.getElementById("check_table109").addEventListener("change", function(e){
    document.forms.check_form109.submit();
  });
  document.getElementById("check_table110").addEventListener("change", function(e){
    document.forms.check_form110.submit();
  });
  document.getElementById("check_table111").addEventListener("change", function(e){
    document.forms.check_form111.submit();
  });
  document.getElementById("check_table112").addEventListener("change", function(e){
    document.forms.check_form112.submit();
  });
  document.getElementById("check_table113").addEventListener("change", function(e){
    document.forms.check_form113.submit();
  });
  document.getElementById("check_table114").addEventListener("change", function(e){
    document.forms.check_form114.submit();
  });
  document.getElementById("check_table115").addEventListener("change", function(e){
    document.forms.check_form115.submit();
  });
  document.getElementById("check_table116").addEventListener("change", function(e){
    document.forms.check_form116.submit();
  });
  document.getElementById("check_table117").addEventListener("change", function(e){
    document.forms.check_form117.submit();
  });
  document.getElementById("check_table118").addEventListener("change", function(e){
    document.forms.check_form118.submit();
  });
  document.getElementById("check_table119").addEventListener("change", function(e){
    document.forms.check_form119.submit();
  });
  document.getElementById("check_table120").addEventListener("change", function(e){
    document.forms.check_form120.submit();
  });
  document.getElementById("check_table121").addEventListener("change", function(e){
    document.forms.check_form121.submit();
  });
  document.getElementById("check_table122").addEventListener("change", function(e){
    document.forms.check_form122.submit();
  });
  document.getElementById("check_table123").addEventListener("change", function(e){
    document.forms.check_form123.submit();
  });
  document.getElementById("check_table124").addEventListener("change", function(e){
    document.forms.check_form124.submit();
  });
  document.getElementById("check_table125").addEventListener("change", function(e){
    document.forms.check_form125.submit();
  });
  document.getElementById("check_table126").addEventListener("change", function(e){
    document.forms.check_form126.submit();
  });
  document.getElementById("check_table127").addEventListener("change", function(e){
    document.forms.check_form127.submit();
  });
  document.getElementById("check_table128").addEventListener("change", function(e){
    document.forms.check_form128.submit();
  });
  document.getElementById("check_table129").addEventListener("change", function(e){
    document.forms.check_form129.submit();
  });
  document.getElementById("check_table130").addEventListener("change", function(e){
    document.forms.check_form130.submit();
  });
  document.getElementById("check_table131").addEventListener("change", function(e){
    document.forms.check_form131.submit();
  });
  document.getElementById("check_table132").addEventListener("change", function(e){
    document.forms.check_form132.submit();
  });
  document.getElementById("check_table133").addEventListener("change", function(e){
    document.forms.check_form133.submit();
  });
  document.getElementById("check_table134").addEventListener("change", function(e){
    document.forms.check_form134.submit();
  });
  document.getElementById("check_table135").addEventListener("change", function(e){
    document.forms.check_form135.submit();
  });
  document.getElementById("check_table136").addEventListener("change", function(e){
    document.forms.check_form136.submit();
  });
  document.getElementById("check_table137").addEventListener("change", function(e){
    document.forms.check_form137.submit();
  });
  document.getElementById("check_table138").addEventListener("change", function(e){
    document.forms.check_form138.submit();
  });
  document.getElementById("check_table139").addEventListener("change", function(e){
    document.forms.check_form139.submit();
  });
  document.getElementById("check_table140").addEventListener("change", function(e){
    document.forms.check_form140.submit();
  });
  document.getElementById("check_table141").addEventListener("change", function(e){
    document.forms.check_form141.submit();
  });
  document.getElementById("check_table142").addEventListener("change", function(e){
    document.forms.check_form142.submit();
  });
  document.getElementById("check_table143").addEventListener("change", function(e){
    document.forms.check_form143.submit();
  });
  document.getElementById("check_table144").addEventListener("change", function(e){
    document.forms.check_form144.submit();
  });
  document.getElementById("check_table145").addEventListener("change", function(e){
    document.forms.check_form145.submit();
  });
  document.getElementById("check_table146").addEventListener("change", function(e){
    document.forms.check_form146.submit();
  });
  document.getElementById("check_table147").addEventListener("change", function(e){
    document.forms.check_form147.submit();
  });
  document.getElementById("check_table148").addEventListener("change", function(e){
    document.forms.check_form148.submit();
  });
  document.getElementById("check_table149").addEventListener("change", function(e){
    document.forms.check_form149.submit();
  });
  document.getElementById("check_table150").addEventListener("change", function(e){
    document.forms.check_form150.submit();
  });
  document.getElementById("check_table151").addEventListener("change", function(e){
    document.forms.check_form151.submit();
  });
  document.getElementById("check_table152").addEventListener("change", function(e){
    document.forms.check_form152.submit();
  });
  document.getElementById("check_table153").addEventListener("change", function(e){
    document.forms.check_form153.submit();
  });
  document.getElementById("check_table154").addEventListener("change", function(e){
    document.forms.check_form154.submit();
  });
  document.getElementById("check_table155").addEventListener("change", function(e){
    document.forms.check_form155.submit();
  });
  document.getElementById("check_table156").addEventListener("change", function(e){
    document.forms.check_form156.submit();
  });
  document.getElementById("check_table157").addEventListener("change", function(e){
    document.forms.check_form157.submit();
  });
  document.getElementById("check_table158").addEventListener("change", function(e){
    document.forms.check_form158.submit();
  });
  document.getElementById("check_table159").addEventListener("change", function(e){
    document.forms.check_form159.submit();
  });
  document.getElementById("check_table160").addEventListener("change", function(e){
    document.forms.check_form160.submit();
  });
  document.getElementById("check_table161").addEventListener("change", function(e){
    document.forms.check_form161.submit();
  });
  document.getElementById("check_table162").addEventListener("change", function(e){
    document.forms.check_form162.submit();
  });
  document.getElementById("check_table163").addEventListener("change", function(e){
    document.forms.check_form163.submit();
  });
  document.getElementById("check_table164").addEventListener("change", function(e){
    document.forms.check_form164.submit();
  });
  document.getElementById("check_table165").addEventListener("change", function(e){
    document.forms.check_form165.submit();
  });
  document.getElementById("check_table166").addEventListener("change", function(e){
    document.forms.check_form166.submit();
  });
  document.getElementById("check_table167").addEventListener("change", function(e){
    document.forms.check_form167.submit();
  });
  document.getElementById("check_table168").addEventListener("change", function(e){
    document.forms.check_form168.submit();
  });
  document.getElementById("check_table169").addEventListener("change", function(e){
    document.forms.check_form169.submit();
  });
  document.getElementById("check_table170").addEventListener("change", function(e){
    document.forms.check_form170.submit();
  });
  document.getElementById("check_table171").addEventListener("change", function(e){
    document.forms.check_form171.submit();
  });
  document.getElementById("check_table172").addEventListener("change", function(e){
    document.forms.check_form172.submit();
  });
  document.getElementById("check_table173").addEventListener("change", function(e){
    document.forms.check_form173.submit();
  });
  document.getElementById("check_table174").addEventListener("change", function(e){
    document.forms.check_form174.submit();
  });

  </script>

</body>
</html>