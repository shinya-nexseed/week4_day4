<?php
    $db = mysqli_connect('localhost', 'root', 'mysql', 'online_bbs_ver2') or die(mysqli_connect_error());
    mysqli_set_charset($db, 'utf8');
?>

<?php

    // 入力されていなかった場合にエラー文をためておくための配列
    $errors = array();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = mysqli_real_escape_string($db, $_POST['name']);
        $comment = mysqli_real_escape_string($db, $_POST['comment']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 名前が入力されているかのチェック
            $name = null;
            if (!isset($_POST['name']) || !strlen($_POST['name'])) {
                $errors['name'] = '名前を入力して下さい';
            } elseif (strlen($_POST['name']) > 40) {
                $errors['name'] = '名前は40文字以内で入力して下さい';
            } else {
                $name = mysqli_real_escape_string($db, $_POST['name']);
            }

            // ひとことが入力されているかのチェック
            $comment = null;
            if (!isset($_POST['comment']) || !strlen($_POST['comment'])) {
                $errors['comment'] = 'ひとことを入力して下さい';
            } elseif (strlen($_POST['comment']) > 40) {
                $errors['comment'] = 'ひとことは200文字以内で入力して下さい';
            } else {
                $comment = mysqli_real_escape_string($db, $_POST['comment']);
            }

            // エラーがなければ保存
            if (count($errors) === 0) {
                // 保存処理
                $sql = sprintf('INSERT INTO messages SET name="%s", comment="%s", created_at="%s" ',
                    $name,
                    $comment,
                    date('Y-m-d H:i:s')
                );
                mysqli_query($db, $sql);
                $_SESSION['name'] = $name;

                header('Location: http://' .$_SERVER['HTTP_HOST'] .$_SERVER['REQUEST_URI']);
                // 'Location: http://' . '192.168.33.10' . '/online_bbs_ver2/bbs.php'
                // 'Location: http://192.168.33.10/online_bbs_ver2/bbs.php'
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>ひとこと掲示版</title>
  <!-- cssの読み込み -->
  <link rel="stylesheet" type="text/css" href="assets/css/custom.css">
  <link rel="stylesheet" type="text/css" href="assets/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="assets/font-awesome/css/font-awesome.css">
</head>
<body>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h1>ひとこと掲示版</h1>
      </div>
    </div>
    <div class="row">
      <div class="col-md-4">
        <form action="bbs.php" method="post">
          <?php if (count($errors) > 0): ?>
          <ul class="error_list">
            <?php foreach ($errors as $error): ?>
            <li>
              <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
            </li>
            <?php endforeach; ?>
          </ul>
          <?php endif; ?>
          名前: <input type="text" name="name"><br>
          ひとこと: <input type="text" name="comment"><br>
          <input type="submit" name="submit" value="送信">
        </form>
      </div>
      <div class="col-md-8">
        <?php
            $sql = "SELECT * FROM `messages` ORDER BY `created_at` DESC";
            $result = mysqli_query($db,$sql) or die(mysqli_error($db));
        ?>

        <ul>
          <?php while ($post = mysqli_fetch_assoc($result)): ?>
          <li><?php echo $post['name']; ?>: <?php echo $post['comment'] ?></li>
          <?php endwhile; ?>
        </ul>
      </div>
    </div>
  </div>
</body>
</html>
