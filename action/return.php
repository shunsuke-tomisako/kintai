<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../style.css">
  <title>勤怠管理</title>
</head>
<body id='action'>
  <a href="../home.php" class="return" role="button" aria-pressed="true" ><img src="../img/return.png"></a>

  <?php
  $now_datetime = date('Y/m/d H:i');
  $split = explode("/", $now_datetime);
  $split2 = explode(" ", $split[2]);
  $time = $split[0] . "年" . $split[1] . "月" . $split2[0] . "日 " . $split2[1];
  ?>

  <div id="time"><?php echo $time; ?></div>

  <div class='comment'>休憩から戻りました。<div>

  <script>
    setTimeout(function() {
      window.location.href = '../home.php';
    }, 5*1000);
  </script>
</body>
</html>