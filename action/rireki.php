<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
  <link rel="stylesheet" href="../style_index.css">
  <title>勤怠管理</title>
</head>
<body>
  <header>
    <h1>勤怠管理</h1>
  </header>

  <a href="../index.php?user_id=<?php echo $_GET["user_id"] ?>" class="btn btn-dark btn-lg active" role="button" aria-pressed="true" >選択に戻る</a><br>

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
  $sql = 'SELECT * FROM trackfarm_kintai WHERE user_id="'.$user_id.'" AND date LIKE "'.$month.'"';
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

  echo $trackfarm_kintai_rec3["name"].'<p>さん</p>';
  ?>

  <form action="./rireki.php" method="get">
    <select class="form-select form-select-sm" aria-label=".form-select-sm example" name="month">
      <?php foreach ($trackfarm_kintai_rec_list2 as $trackfarm_kintai_rec2) {
        if ($trackfarm_kintai_rec2["MONTH(date)"] < 10) {
          $trackfarm_kintai_rec2["MONTH(date)"] = "0". $trackfarm_kintai_rec2["MONTH(date)"];
        }
      ?>
      <option selected><?php echo $trackfarm_kintai_rec2["YEAR(date)"] ."-" .$trackfarm_kintai_rec2["MONTH(date)"]; ?></option>
      <?php } ?>
    </select>
    <input type="hidden" name="user_id" value="<?php echo $_GET["user_id"]; ?>">
    <input type="submit" value="表示">
  </form>
  <br>

  <table class="table">
    <thead>
      <tr>
        <th scope="col">日付</th>
        <th scope="col"></th>
        <th scope="col">出勤時間</th>
        <th scope="col">退勤時間</th>
        <th scope="col">休憩開始時間</th>
        <th scope="col">休憩終了時間</th>
        <th scope="col">休憩時間</th>
        <th scope="col">残業時間</th>
        <th scope="col">夜勤時間</th>
        <th scope="col">勤務時間</th>
      </tr>
    </thead>
    <tbody>
      <?php
        $restTimeSum = 0;
        $workingTimeSum = 0;
        $overTimeSum = 0;
        $nightTimeSum = 0;

        foreach ($trackfarm_kintai_rec_list as $trackfarm_kintai_rec) {
          // 休憩時間計算
          $restTime = strtotime($trackfarm_kintai_rec['return_time']) - strtotime($trackfarm_kintai_rec['rest_time']);
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
          $workingTime = strtotime($trackfarm_kintai_rec['finish_time']) - strtotime($trackfarm_kintai_rec['begin_time']) - (strtotime($trackfarm_kintai_rec['return_time']) - strtotime($trackfarm_kintai_rec['rest_time']));
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
          if (strtotime($trackfarm_kintai_rec['finish_time']) - strtotime($trackfarm_kintai_rec['date']) - 3600 * 22 > 0) {
            $nightTime = strtotime($trackfarm_kintai_rec['finish_time']) - strtotime($trackfarm_kintai_rec['date']) - 3600 * 22;
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


      ?>
      <tr>
        <th scope="row"><?php echo mb_substr($trackfarm_kintai_rec['date'], 5, 6); ?></th>
        <td><a href="modify_be.php?user_id=<?php echo $user_id; ?>&date=<?php echo $trackfarm_kintai_rec['date']; ?>">修正</a></td>
        <td><?php echo mb_substr($trackfarm_kintai_rec['begin_time'], 10, 6); ?></td>
        <?php if (isset($trackfarm_kintai_rec['finish_time']) == true && (int)mb_substr($trackfarm_kintai_rec['finish_time'], 11 ,2) < 5) { ?>
          <td><?php echo (int)mb_substr($trackfarm_kintai_rec['finish_time'], 10, 3) + 24 .mb_substr($trackfarm_kintai_rec['finish_time'], 13, 3); ?></td>
        <?php } else { ?>
          <td><?php echo mb_substr($trackfarm_kintai_rec['finish_time'], 10, 6); ?></td>
        <?php } ?>
        <td><?php echo mb_substr($trackfarm_kintai_rec['rest_time'], 10, 6); ?></td>
        <td><?php echo mb_substr($trackfarm_kintai_rec['return_time'], 10, 6); ?></td>
        <td><?php echo $restTimeH .$restTimeM; ?></td>
        <td><?php echo $overTimeH .$overTimeM; ?></td>
        <td><?php echo $nightTimeH .$nightTimeM; ?></td>
        <td><?php echo $workingTimeH .$workingTimeM; ?></td>
      </tr>
      <?php } ?> 
      <tr>
        <th scope="row">合計</th>
        <td></td>
        <td>出勤日数</td>
        <td><?php echo count($trackfarm_kintai_rec_list); ?></td>
        <td></td>
        <td></td>
        <td><?php echo $restTimeSumH .$restTimeSumM; ?></td>
        <td><?php echo $overTimeSumH .$overTimeSumM; ?></td>
        <td><?php echo $nightTimeSumH .$nightTimeSumM; ?></td>
        <td><?php echo $workingTimeSumH .$workingTimeSumM; ?></td>
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
      <tr>
        <th scope="row">合計(10進法)</th>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td><?php echo $restTimeSumH . $restTimeSumM10; ?></td>
        <td><?php echo $overTimeSumH . $overTimeSumM10; ?></td>
        <td><?php echo $nightTimeSumH . $nightTimeSumM10; ?></td>
        <td><?php echo $workingTimeSumH . $workingTimeSumM10; ?></td>
      </tr>
    </tbody>
  </table>

</body>
</html>