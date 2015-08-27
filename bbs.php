<?php
    $db = mysqli_connect('localhost', 'root', 'mysql', 'online_bbs_ver2') or die(mysqli_connect_error());
    mysqli_set_charset($db, 'utf8');
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>ひとこと掲示版</title>
</head>
<body>
  <h1>ひとこと掲示版</h1>
  <form action="bbs.php" method="post">
    名前: <input type="text" name="name"><br>
    ひとこと: <input type="text" name="comment" size="60"><br>
    <input type="submit" name="submit" value="送信">
  </form>

  <?php
      $sql = "SELECT * FROM `messages` ORDER BY `created_at` DESC";
      $result = mysqli_query($db,$sql) or die(mysqli_error($db));
  ?>

  <ul>
    <?php while ($post = mysqli_fetch_assoc($result)): ?>
    <li><?php echo $post['name']; ?>: <?php echo $post['comment'] ?></li>
    <?php endwhile; ?>
  </ul>

</body>
</html>
