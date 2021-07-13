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

  // 担当ペア取得
  $basis_day = "2021/07/01";
  $today = date("Y/m/d");
  $differ_time = strtotime($today) - strtotime($basis_day);
  $differ_day = $differ_time / (60 * 60 * 24);

  if ($differ_day % 28 < 7) {
    $cleaner_pair = "A";
  } elseif (7 <= $differ_day % 28 || $differ_day % 28 < 14) {
    $cleaner_pair = "B";
  } elseif (14 <= $differ_day % 28 || $differ_day % 28 < 21) {
    $cleaner_pair = "C";
  } elseif (21 <= $differ_day % 28 || $differ_day % 28 < 28) {
    $cleaner_pair = "D";
  }

  // 今週の日付取得
  if (date('w') == '0') {
    $first_day = date("Y/m/d",strtotime("+1 day"));
  } elseif (date('w') == '1') {
    $first_day = date("Y/m/d");
  } elseif (date('w') == '2') {
    $first_day = date("Y/m/d",strtotime("-1 day"));
  } elseif (date('w') == '3') {
    $first_day = date("Y/m/d",strtotime("-2 day"));
  } elseif (date('w') == '4') {
    $first_day = date("Y/m/d",strtotime("-3 day"));
  } elseif (date('w') == '5') {
    $first_day = date("Y/m/d",strtotime("-4 day"));
  } elseif (date('w') == '6') {
    $first_day = date("Y/m/d",strtotime("-5 day"));
  }

  $Mon = substr($first_day, 5, 5);
  $Tue = substr(date("Y/m/d",strtotime("+1 day", strtotime($first_day))), 5, 5);
  $Wed = substr(date("Y/m/d",strtotime("+2 day", strtotime($first_day))), 5, 5);
  $Thu = substr(date("Y/m/d",strtotime("+3 day", strtotime($first_day))), 5, 5);
  $Fri = substr(date("Y/m/d",strtotime("+4 day", strtotime($first_day))), 5, 5);

  // データベース接続
  $dsn = 'mysql:dbname=test;host=localhost;charset=utf8';
  $user = 'root';
  $password = '';
  $dbh = new PDO($dsn,$user,$password);
  $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

  $sql = 'SELECT name FROM clean WHERE clean="'.$cleaner_pair . "1".'" OR clean="'.$cleaner_pair . "2".'"';
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $trackfarm_kintai_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <div class="text">掃除チェック表<br><?php echo $Mon; ?>～<?php echo $Fri; ?>の担当は<?php echo $trackfarm_kintai_list[0]["name"]; ?>さんと<?php echo $trackfarm_kintai_list[1]["name"]; ?>さんです。</div>

    <form action="./clean.php" method="post" onSubmit="return checkSubmit()">

      <table>
        <tr>
          <th></th>
          <th>Date</th>
          <th><?php echo $Mon ?></th>
          <th><?php echo $Tue ?></th>
          <th><?php echo $Wed ?></th>
          <th><?php echo $Thu ?></th>
          <th><?php echo $Fri ?></th>
        </tr>
        <tr>
          <td>1F</td>
          <td>メイン・机</td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
        </tr>
        <tr>
          <td></td>
          <td>メイン・床</td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
        </tr>
        <tr>
          <td></td>
          <td>メイン・ラック</td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
        </tr>
        <tr>
          <td></td>
          <td>会議室・机</td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
        </tr>
        <tr>
          <td></td>
          <td>会議室・机/床</td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
        </tr>
        <tr>
          <td></td>
          <td>会議室・窓</td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
        </tr>
        <tr>
          <td></td>
          <td>メイン・奥の棚</td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
        </tr>
        <tr>
          <td></td>
          <td>入口～玄関</td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
        </tr>
        <tr>
          <td></td>
          <td>ゴミ捨て</td>
          <td><input type="checkbox"></td>
          <td>/</td>
          <td>/</td>
          <td><input type="checkbox"></td>
          <td>/</td>
        </tr>
        <tr>
          <td>2F</td>
          <td>トイレ</td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
        </tr>
        <tr>
          <td></td>
          <td>床(廊下・階段のみ)</td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
        </tr>
        <tr>
          <td></td>
          <td>シンク</td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
          <td><input type="checkbox"></td>
        </tr>
      </table>

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