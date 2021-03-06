<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../style.css">
  <title>勤怠管理</title>
</head>
<body id='rireki'>
  <a href="../index.php?user_id=<?php echo $_GET["user_id"] ?>" class="return" role="button" aria-pressed="true"><img src="../img/return2.png"></a><br>

  <?php

  $user_id = $_GET["user_id"];
  if ($user_id == "") {
    header("Location: ../home.php");
    exit;
  }

  $dsn = 'mysql:dbname=test;host=localhost;charset=utf8';
  $user = 'root';
  $password = '';
  $dbh = new PDO($dsn,$user,$password);
  $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

  // $sql = 'SELECT * FROM trackfarm_kintai WHERE user_id="'.$user_id.'"';
  // $stmt = $dbh->prepare($sql);
  // $stmt->execute();
  // $trackfarm_kintai_rec_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // 最新出勤日取得
  $sql3 = 'SELECT MAX(date) FROM trackfarm_kintai WHERE user_id="'.$user_id.'" GROUP BY user_id="'.$user_id.'"';
  $stmt3 = $dbh->prepare($sql3);
  $stmt3->execute();
  $trackfarm_kintai_rec_list3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);

  if (isset($_GET["month"]) == true) {
    $month = $_GET["month"] ."%";

  } else {
    $month = mb_substr($trackfarm_kintai_rec_list3[0]["MAX(date)"], 0, 7) ."%";
  }
  // csvダウンロード名
  $month2 = mb_substr($month, 0, 7);
  // 表示月
  $month3 = explode("-", $month2);
  $month4 = $month3[0] . "年" . $month3[1] . "月";

  // 全日付取得
  // $date = $month2;
  // $begin = new DateTime(date('Y-m-d', strtotime('first day of '. $date)));
  // $end = new Datetime(date('Y-m-d', strtotime('first day of next month '. $date)));
  // $interval = new DateInterval('P1D');
  // $daterange = new DatePeriod($begin, $interval, $end);
  // foreach ($daterange as $date) {
  //   echo $date->format("m-d") . "\n";
  // }

  $sql = 'SELECT * FROM trackfarm_kintai WHERE user_id="'.$user_id.'" AND date LIKE "'.$month.'" ORDER BY date';
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $trackfarm_kintai_rec_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $sql2 = 'SELECT distinct YEAR(date), MONTH(date) FROM trackfarm_kintai WHERE user_id="'.$user_id.'"';
  $stmt2 = $dbh->prepare($sql2);
  $stmt2->execute();
  $trackfarm_kintai_rec_list2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);

  // 名前取得
  $sql3 = 'SELECT name FROM users WHERE user_id="'.$user_id.'"';
  $stmt3 = $dbh->prepare($sql3);
  $stmt3->execute();
  $trackfarm_kintai_rec3 = $stmt3->fetch(PDO::FETCH_ASSOC);

  // 出勤日のみ取得
  $sql4 = 'SELECT * FROM trackfarm_kintai WHERE user_id="'.$user_id.'" AND date LIKE "'.$month.'" AND begin_time LIKE "%-%" ORDER BY date';
  $stmt4 = $dbh->prepare($sql4);
  $stmt4->execute();
  $trackfarm_kintai_rec_list4 = $stmt4->fetchAll(PDO::FETCH_ASSOC);
  ?>

  <div class="wrap">

    <div class="name"><?php echo $trackfarm_kintai_rec3["name"] ?>さん</div>
    <div class="month"><?php echo $month4 ?></div>

    <form action="./rireki.php" method="get">
      <select class="" aria-label=".form-select-sm example" name="month" onchange="submit(this.form)">
        <?php foreach ($trackfarm_kintai_rec_list2 as $trackfarm_kintai_rec2) {
          if ($trackfarm_kintai_rec2["MONTH(date)"] < 10) {
            $trackfarm_kintai_rec2["MONTH(date)"] = "0". $trackfarm_kintai_rec2["MONTH(date)"];
          }
        ?>
        <option value="<?php echo $trackfarm_kintai_rec2["YEAR(date)"] ."-" .$trackfarm_kintai_rec2["MONTH(date)"]; ?>" <?php if (isset($_GET["month"]) == true && $trackfarm_kintai_rec2["YEAR(date)"] ."-" .$trackfarm_kintai_rec2["MONTH(date)"] == $_GET["month"]) echo "selected='selected'" ?> <?php if (isset($_GET["month"]) == false) echo "selected='selected'" ?>>
          <?php echo $trackfarm_kintai_rec2["YEAR(date)"] ."-" .$trackfarm_kintai_rec2["MONTH(date)"]; ?>
        </option>
        <?php } ?>
      </select>
      <input type="hidden" name="user_id" value="<?php echo $_GET["user_id"]; ?>">
      <!-- <input type="submit" value="表示"> -->
    </form>

  </div>
  <a id="download" href="#" download="<?php echo $trackfarm_kintai_rec3["name"] . "_" . $month2; ?>.csv" onclick="handleDownload()" class="download"><img src="../img/download.png" alt="csvファイルダウンロード"></a>

  <table id="table">
    <thead>
      <tr class="tr">
        <td>日付</td>
        <td>曜日</td>
        <td>出勤時間</td>
        <td>(繰り上げ)</td>
        <td>退勤時間</td>
        <td>(繰り下げ)</td>
        <td>休憩開始時間</td>
        <td>(繰り下げ)</td>
        <td>休憩終了時間</td>
        <td>(繰り上げ)</td>
        <td>休憩時間</td>
        <td>残業時間</td>
        <td>夜勤時間</td>
        <td>勤務時間</td>
        <td></td>
      </tr>
    </thead>
    <tbody>
      <?php
        $restTimeSum = 0;
        $workingTimeSum = 0;
        $overTimeSum = 0;
        $nightTimeSum = 0;

        // 繰り上げ計算
        function upDatetime($timestamp, $margin_minutes){
          $_year = date('Y', $timestamp);
          $_month = date('m', $timestamp);
          $_day = date('d', $timestamp);
          $_hour = date('H', $timestamp);
          $_minute = date('i', $timestamp);

          if($_minute % $margin_minutes) $_minute += $margin_minutes - ($_minute % $margin_minutes);

          return mktime($_hour, $_minute, 0, $_month, $_day, $_year);
        }

        // 繰り下げ計算
        function downDatetime($timestamp, $margin_minutes){
          $_year = date('Y', $timestamp);
          $_month = date('m', $timestamp);
          $_day = date('d', $timestamp);
          $_hour = date('H', $timestamp);
          $_minute = date('i', $timestamp);

          if($_minute % $margin_minutes) $_minute -= ($_minute % $margin_minutes);

          return mktime($_hour, $_minute, 0, $_month, $_day, $_year);
        }


        foreach ($trackfarm_kintai_rec_list as $trackfarm_kintai_rec) {

          // 休憩時間計算
          $restTime = strtotime(date('Y-m-d H:i:s', upDatetime(strtotime($trackfarm_kintai_rec['return_time']), 5))) - strtotime(date('Y-m-d H:i:s', downDatetime(strtotime($trackfarm_kintai_rec['rest_time']), 5)));
          if ($restTime > 0) {
            $restTimeh = floor($restTime / 3600);
            if ($restTimeh < 10) {
              $restTimeh = "0" . $restTimeh;
            }
            $restTimeH = $restTimeh .":";
            $restTimem = floor(($restTime - $restTimeh * 3600) / 60);
            if ($restTimem < 10) {
              $restTimem = "0" . $restTimem;
            }
            $restTimeM = $restTimem;
          } else {
            $restTimeH = "";
            $restTimeM = "";
          }

          // 休憩時間合計計算
          $restTimeSum += $restTime;
          if ($restTimeSum > 0) {
            $restTimeSumh = floor($restTimeSum / 3600);
            if ($restTimeSumh < 10) {
              $restTimeSumh = "0" . $restTimeSumh;
            }
            $restTimeSumH = $restTimeSumh .":";
            $restTimeSumm = floor(($restTimeSum - $restTimeSumh * 3600) / 60);
            if ($restTimeSumm < 10) {
              $restTimeSumm = "0" . $restTimeSumm;
            }
            $restTimeSumM = $restTimeSumm;
          } else {
            $restTimeSumH = "";
            $restTimeSumM = "";
          }

          // 勤務時間計算
          $workingTime = strtotime(date('Y-m-d H:i:s', downDatetime(strtotime($trackfarm_kintai_rec['finish_time']), 15))) - strtotime(date('Y-m-d H:i:s', upDatetime(strtotime($trackfarm_kintai_rec['begin_time']), 15))) - (strtotime(date('Y-m-d H:i:s', upDatetime(strtotime($trackfarm_kintai_rec['return_time']), 5))) - strtotime(date('Y-m-d H:i:s', downDatetime(strtotime($trackfarm_kintai_rec['rest_time']), 5))));
          if ($workingTime > 0) {
            $workingTimeh = floor($workingTime / 3600);
            if ($workingTimeh < 10) {
              $workingTimeh = "0" . $workingTimeh;
            }
            $workingTimeH = $workingTimeh .":";
            $workingTimem = floor(($workingTime - $workingTimeh * 3600) / 60);
            if ($workingTimem < 10) {
              $workingTimem = "0" . $workingTimem;
            }
            $workingTimeM = $workingTimem;
          } else {
            $workingTimeH = "";
            $workingTimeM = "";
          }

          // 勤務時間合計計算
          $workingTimeSum += $workingTime;
          if ($workingTimeSum > 0) {
            $workingTimeSumh = floor($workingTimeSum / 3600);
            if ($workingTimeSumh < 10) {
              $workingTimeSumh = "0" . $workingTimeSumh;
            }
            $workingTimeSumH = $workingTimeSumh .":";
            $workingTimeSumm = floor(($workingTimeSum - $workingTimeSumh * 3600) / 60);
            if ($workingTimeSumm < 10) {
              $workingTimeSumm = "0" . $workingTimeSumm;
            }
            $workingTimeSumM = $workingTimeSumm;
          } else {
            $workingTimeSumH = "";
            $workingTimeSumM = "";
          }

          // 残業時間計算
          if ($workingTime - 3600 * 8 > 0) {
            $overTime = $workingTime - 3600 * 8;
          } else {
            $overTime = 0;
          }
          if ($overTime > 0) {
            $overTimeh = floor($overTime / 3600);
            if ($overTimeh < 10) {
              $overTimeh = "0" . $overTimeh;
            }
            $overTimeH = $overTimeh .":";
            $overTimem = floor(($overTime - $overTimeh * 3600) / 60);
            if ($overTimem < 10) {
              $overTimem = "0" . $overTimem;
            }
            $overTimeM = $overTimem;
          } else {
            $overTimeH = "";
            $overTimeM = "";
          }

          // 残業時間合計計算
          $overTimeSum += $overTime;
          if ($overTimeSum > 0) {
            $overTimeSumh = floor($overTimeSum / 3600);
            if ($overTimeSumh < 10) {
              $overTimeSumh = "0" . $overTimeSumh;
            }
            $overTimeSumH = $overTimeSumh .":";
            $overTimeSumm = floor(($overTimeSum - $overTimeSumh * 3600) / 60);
            if ($overTimeSumm < 10) {
              $overTimeSumm = "0" . $overTimeSumm;
            }
            $overTimeSumM = $overTimeSumm;
          } else {
            $overTimeSumH = "";
            $overTimeSumM = "";
          }

          // 夜勤時間計算
          //22時以降に休憩に入った場合も?
          if (strtotime(date('Y-m-d H:i:s', downDatetime(strtotime($trackfarm_kintai_rec['finish_time']), 15))) - strtotime($trackfarm_kintai_rec['date']) - 3600 * 22 > 0) {
            $nightTime = strtotime(date('Y-m-d H:i:s', downDatetime(strtotime($trackfarm_kintai_rec['finish_time']), 15))) - strtotime($trackfarm_kintai_rec['date']) - 3600 * 22;
          } else {
            $nightTime = 0;
          }
          if (isset($trackfarm_kintai_rec['finish_time']) == true && ((int)mb_substr($trackfarm_kintai_rec['finish_time'], 11 ,2) >= 22 || (int)mb_substr($trackfarm_kintai_rec['finish_time'], 11 ,2) < 5)) {
            $nightTimeh = floor($nightTime / 3600);
            if ($nightTimeh < 10) {
              $nightTimeh = "0" . $nightTimeh;
            }
            $nightTimeH = $nightTimeh .":";
            $nightTimem = floor(($nightTime - $nightTimeh * 3600) / 60);
            if ($nightTimem < 10) {
              $nightTimem = "0" . $nightTimem;
            }
            $nightTimeM = $nightTimem;
          } else {
            $nightTimeH = "";
            $nightTimeM = "";
          }

          // 夜勤時間合計計算
          //22時以降に休憩に入った場合も?
          $nightTimeSum += $nightTime;
          if ($nightTimeSum > 0) {
            $nightTimeSumh = floor($nightTimeSum / 3600);
            if ($nightTimeSumh < 10) {
              $nightTimeSumh = "0" . $nightTimeSumh;
            }
            $nightTimeSumH = $nightTimeSumh .":";
            $nightTimeSumm = floor(($nightTimeSum - $nightTimeSumh * 3600) / 60);
            if ($nightTimeSumm < 10) {
              $nightTimeSumm = "0" . $nightTimeSumm;
            }
            $nightTimeSumM = $nightTimeSumm;
          } else {
            $nightTimeSumH = "";
            $nightTimeSumM = "";
          }

          // 曜日取得
          $date = date('w', strtotime(str_replace('-', '', $trackfarm_kintai_rec['date'])));
          $week = [
            '日', //0
            '月', //1
            '火', //2
            '水', //3
            '木', //4
            '金', //5
            '土', //6
          ];

      ?>
      <tr class="tr">
        <td><?php echo mb_substr($trackfarm_kintai_rec['date'], 5, 6); ?></td>
        <td><?php echo $week[$date]; ?></td>
        <td><?php echo mb_substr($trackfarm_kintai_rec['begin_time'], 10, 6); ?></td>
        <td><?php if (isset($trackfarm_kintai_rec['begin_time']) == true) {echo date('H:i', upDatetime(strtotime($trackfarm_kintai_rec['begin_time']), 15));}; ?></td>
        <?php if (isset($trackfarm_kintai_rec['finish_time']) == true && (int)mb_substr($trackfarm_kintai_rec['finish_time'], 11 ,2) < 5) { ?>
          <td><?php echo (int)mb_substr($trackfarm_kintai_rec['finish_time'], 10, 3) + 24 .mb_substr($trackfarm_kintai_rec['finish_time'], 13, 3); ?></td>
        <?php } else { ?>
          <td><?php echo mb_substr($trackfarm_kintai_rec['finish_time'], 10, 6); ?></td>
        <?php } ?>
        <?php if (isset($trackfarm_kintai_rec['finish_time']) == true && (int)mb_substr($trackfarm_kintai_rec['finish_time'], 11 ,2) < 5) { ?>
          <td><?php echo (int)mb_substr(date('Y-m-d H:i:s', downDatetime(strtotime($trackfarm_kintai_rec['finish_time']), 15)), 10, 3) + 24 .mb_substr(date('Y-m-d H:i:s', downDatetime(strtotime($trackfarm_kintai_rec['finish_time']), 15)), 13, 3); ?></td>
        <?php } else if (isset($trackfarm_kintai_rec['finish_time']) == true) { ?>
          <td><?php echo date('H:i', downDatetime(strtotime($trackfarm_kintai_rec['finish_time']), 15)); ?></td>
        <?php } else { ?>
          <td></td>
        <?php } ?>
        <td><?php echo mb_substr($trackfarm_kintai_rec['rest_time'], 10, 6); ?></td>
        <?php if (isset($trackfarm_kintai_rec['rest_time']) == true) { ?>
          <td><?php echo date('H:i', downDatetime(strtotime($trackfarm_kintai_rec['rest_time']), 5)); ?></td>
        <?php } else { ?>
          <td></td>
        <?php } ?>
        <td><?php echo mb_substr($trackfarm_kintai_rec['return_time'], 10, 6); ?></td>
        <?php if (isset($trackfarm_kintai_rec['return_time']) == true) { ?>
          <td><?php echo date('H:i', upDatetime(strtotime($trackfarm_kintai_rec['return_time']), 5)); ?></td>
        <?php } else { ?>
          <td></td>
        <?php } ?>
        <td><?php echo $restTimeH .$restTimeM; ?></td>
        <td><?php echo $overTimeH .$overTimeM; ?></td>
        <td><?php echo $nightTimeH .$nightTimeM; ?></td>
        <td><?php echo $workingTimeH .$workingTimeM; ?></td>
        <td class="rirekimodify"><a href="modify_be.php?user_id=<?php echo $user_id; ?>&date=<?php echo $trackfarm_kintai_rec['date']; ?>"><img src="../img/modify2.png" alt="修正"></a></td>
      </tr>
      <?php } ?>
      <tr>
        <td>出勤日数</td>
        <td><?php echo count($trackfarm_kintai_rec_list4) . "日"; ?></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <!-- <td><strong class="strong">合計</strong></td> -->
        <td>合計</td>
        <td><?php echo $restTimeSumH .$restTimeSumM; ?></td>
        <td><?php echo $overTimeSumH .$overTimeSumM; ?></td>
        <td><?php echo $nightTimeSumH .$nightTimeSumM; ?></td>
        <td><?php echo $workingTimeSumH .$workingTimeSumM; ?></td>
        <td></td>
      </tr>

      <!-- 10進法計算 -->
      <?php
      if ($restTimeSumM != "") {
        $restTimeSumM10 = mb_substr(sprintf('%.2f', $restTimeSumM / 60), 2);
      } else {
        $restTimeSumM10 = "";
      }
      if ($overTimeSumM != "") {
        $overTimeSumM10 = mb_substr(sprintf('%.2f', $overTimeSumM / 60), 2);
      } else {
        $overTimeSumM10 = "";
      }
      if ($nightTimeSumM != "") {
        $nightTimeSumM10 = mb_substr(sprintf('%.2f', $nightTimeSumM / 60), 2);
      } else {
        $nightTimeSumM10 = "";
      }
      if ($workingTimeSumM != "") {
        $workingTimeSumM10 = mb_substr(sprintf('%.2f', $workingTimeSumM / 60), 2);
      } else {
        $workingTimeSumM10 = "";
      }
      ?>
      <tr class="sum">
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <!-- <td><strong class="strong2">合計(10進法)</strong></td> -->
        <td>合計(10進法)</td>
        <td><?php echo $restTimeSumH . $restTimeSumM10; ?></td>
        <td><?php echo $overTimeSumH . $overTimeSumM10; ?></td>
        <td><?php echo $nightTimeSumH . $nightTimeSumM10; ?></td>
        <td><?php echo $workingTimeSumH . $workingTimeSumM10; ?></td>
        <td></td>
      </tr>
    </tbody>
  </table>

  <!-- csvダウンロード -->
  <script>
    function handleDownload() {
      let bom = new Uint8Array([0xEF, 0xBB, 0xBF]);
      let table = document.getElementById('table');
      let data_csv="<?php echo $trackfarm_kintai_rec3["name"] ?>, <?php echo $month4 ?>, \n";

      for (let i = 0; i < table.rows.length; i++) {
        for (let j = 0; j < table.rows[i].cells.length; j++) {
          data_csv += table.rows[i].cells[j].innerText;
          if (j == table.rows[i].cells.length-1) {
            data_csv += "\n";
          } else {
            data_csv += ",";
          }
        }
      }

      let blob = new Blob([bom, data_csv], {type : "text/csv"});
      if (window.navigator.msSaveBlob) {
        window.navigator.msSaveBlob(blob, "<?php echo $trackfarm_kintai_rec3["name"] . "_" . $month2; ?>.csv");
        window.navigator.msSaveBlob(blob, "test.csv");
      } else {
        document.getElementById("download").href = window.URL.createObjectURL(blob);
      }

      delete data_csv;
    }
  </script>
</body>
</html>