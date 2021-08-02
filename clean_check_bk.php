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
  } elseif (7 <= $differ_day % 28 && $differ_day % 28 < 14) {
    $cleaner_pair = "B";
  } elseif (14 <= $differ_day % 28 && $differ_day % 28 < 21) {
    $cleaner_pair = "C";
  } elseif (21 <= $differ_day % 28 && $differ_day % 28 < 28) {
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
    // echo $_POST["check"];
    // $sql = 'INSERT INTO users (company_name, name) VALUES("'.$_POST["company_name"].'", "'.$_POST["name"].'")';
    // $stmt = $dbh->prepare($sql);
    // $stmt->execute();
    // header("Location: home.php");
  }
  ?>
  <div id="clean_check">
    <div class="text">掃除チェック表<br><?php echo $Mon; ?>～<?php echo $Fri; ?>の担当は<?php echo $trackfarm_kintai_list[0]["name"]; ?>さんと<?php echo $trackfarm_kintai_list[1]["name"]; ?>さんです。</div>

    <?php
      for ($i = 1; $i <= 57; $i++) {
        $checked["name"][$i]="";
      }
      if (isset($_POST["name"])) {
        foreach ((array) $_POST["name"] as $val) {
          $checked["name"][$val] = "checked";
        }
      }

      print <<< eof
      <form id="check_form" action="./clean_check.php" method="post" onSubmit="return checkSubmit()">

        <table id="check_table">
          <tr>
            <th></th>
            <th>Date</th>
            <th>$Mon</th>
            <th>$Tue</th>
            <th>$Wed</th>
            <th>$Thu</th>
            <th>$Fri</th>
          </tr>
          <tr>
            <td>1F</td>
            <td>メイン・机</td>
            <td><input type="checkbox" name="name[]" value="1"{$checked["name"][1]}></td>
            <td><input type="checkbox" name="name[]" value="2"{$checked["name"][2]}></td>
            <td><input type="checkbox" name="name[]" value="3"{$checked["name"][3]}></td>
            <td><input type="checkbox" name="name[]" value="4"{$checked["name"][4]}></td>
            <td><input type="checkbox" name="name[]" value="5"{$checked["name"][5]}></td>
          </tr>
          <tr>
            <td></td>
            <td>メイン・床</td>
            <td><input type="checkbox" name="name[]" value="6"{$checked["name"][6]}></td>
            <td><input type="checkbox" name="name[]" value="7"{$checked["name"][7]}></td>
            <td><input type="checkbox" name="name[]" value="8"{$checked["name"][8]}></td>
            <td><input type="checkbox" name="name[]" value="9"{$checked["name"][9]}></td>
            <td><input type="checkbox" name="name[]" value="10"{$checked["name"][10]}></td>
          </tr>
          <tr>
            <td></td>
            <td>メイン・ラック</td>
            <td><input type="checkbox" name="name[]" value="11"{$checked["name"][11]}></td>
            <td><input type="checkbox" name="name[]" value="12"{$checked["name"][12]}></td>
            <td><input type="checkbox" name="name[]" value="13"{$checked["name"][13]}></td>
            <td><input type="checkbox" name="name[]" value="14"{$checked["name"][14]}></td>
            <td><input type="checkbox" name="name[]" value="15"{$checked["name"][15]}></td>
          </tr>
          <tr>
            <td></td>
            <td>会議室・机</td>
            <td><input type="checkbox" name="name[]" value="16"{$checked["name"][16]}></td>
            <td><input type="checkbox" name="name[]" value="17"{$checked["name"][17]}></td>
            <td><input type="checkbox" name="name[]" value="18"{$checked["name"][18]}></td>
            <td><input type="checkbox" name="name[]" value="19"{$checked["name"][19]}></td>
            <td><input type="checkbox" name="name[]" value="20"{$checked["name"][20]}></td>
          </tr>
          <tr>
            <td></td>
            <td>会議室・机/床</td>
            <td><input type="checkbox" name="name[]" value="21"{$checked["name"][21]}></td>
            <td><input type="checkbox" name="name[]" value="22"{$checked["name"][22]}></td>
            <td><input type="checkbox" name="name[]" value="23"{$checked["name"][23]}></td>
            <td><input type="checkbox" name="name[]" value="24"{$checked["name"][24]}></td>
            <td><input type="checkbox" name="name[]" value="25"{$checked["name"][25]}></td>
          </tr>
          <tr>
            <td></td>
            <td>会議室・窓</td>
            <td><input type="checkbox" name="name[]" value="26"{$checked["name"][26]}></td>
            <td><input type="checkbox" name="name[]" value="27"{$checked["name"][27]}></td>
            <td><input type="checkbox" name="name[]" value="28"{$checked["name"][28]}></td>
            <td><input type="checkbox" name="name[]" value="29"{$checked["name"][29]}></td>
            <td><input type="checkbox" name="name[]" value="30"{$checked["name"][30]}></td>
          </tr>
          <tr>
            <td></td>
            <td>メイン・奥の棚</td>
            <td><input type="checkbox" name="name[]" value="31"{$checked["name"][31]}></td>
            <td><input type="checkbox" name="name[]" value="32"{$checked["name"][32]}></td>
            <td><input type="checkbox" name="name[]" value="33"{$checked["name"][33]}></td>
            <td><input type="checkbox" name="name[]" value="34"{$checked["name"][34]}></td>
            <td><input type="checkbox" name="name[]" value="35"{$checked["name"][35]}></td>
          </tr>
          <tr>
            <td></td>
            <td>入口～玄関</td>
            <td><input type="checkbox" name="name[]" value="36"{$checked["name"][36]}></td>
            <td><input type="checkbox" name="name[]" value="37"{$checked["name"][37]}></td>
            <td><input type="checkbox" name="name[]" value="38"{$checked["name"][38]}></td>
            <td><input type="checkbox" name="name[]" value="39"{$checked["name"][39]}></td>
            <td><input type="checkbox" name="name[]" value="40"{$checked["name"][40]}></td>
          </tr>
          <tr>
            <td></td>
            <td>ゴミ捨て</td>
            <td><input type="checkbox" name="name[]" value="41"{$checked["name"][41]}></td>
            <td>/</td>
            <td>/</td>
            <td><input type="checkbox" name="name[]" value="42"{$checked["name"][42]}></td>
            <td>/</td>
          </tr>
          <tr>
            <td>2F</td>
            <td>トイレ</td>
            <td><input type="checkbox" name="name[]" value="43"{$checked["name"][43]}></td>
            <td><input type="checkbox" name="name[]" value="44"{$checked["name"][44]}></td>
            <td><input type="checkbox" name="name[]" value="45"{$checked["name"][45]}></td>
            <td><input type="checkbox" name="name[]" value="46"{$checked["name"][46]}></td>
            <td><input type="checkbox" name="name[]" value="47"{$checked["name"][47]}></td>
          </tr>
          <tr>
            <td></td>
            <td>床(廊下・階段のみ)</td>
            <td><input type="checkbox" name="name[]" value="48"{$checked["name"][48]}></td>
            <td><input type="checkbox" name="name[]" value="49"{$checked["name"][49]}></td>
            <td><input type="checkbox" name="name[]" value="50"{$checked["name"][50]}></td>
            <td><input type="checkbox" name="name[]" value="51"{$checked["name"][51]}></td>
            <td><input type="checkbox" name="name[]" value="52"{$checked["name"][52]}></td>
          </tr>
          <tr>
            <td></td>
            <td>シンク</td>
            <td><input type="checkbox" name="name[]" value="53"{$checked["name"][53]}></td>
            <td><input type="checkbox" name="name[]" value="54"{$checked["name"][54]}></td>
            <td><input type="checkbox" name="name[]" value="55"{$checked["name"][55]}></td>
            <td><input type="checkbox" name="name[]" value="56"{$checked["name"][56]}></td>
            <td><input type="checkbox" name="name[]" value="57"{$checked["name"][57]}></td>
          </tr>
        </table>

        <input type="hidden" name="value" value="1">
      </form>
      eof;
    ?>
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

  document.getElementById("check_table").addEventListener("change", function(e){
  document.forms.check_form.submit();
  });
  </script>

</body>
</html>