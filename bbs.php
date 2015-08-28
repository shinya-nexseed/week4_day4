<?php
    session_start();
    $db = mysqli_connect('localhost', 'root', 'mysql', 'online_bbs_ver2') or die(mysqli_connect_error());
    mysqli_set_charset($db, 'utf8');
?>

<?php
    $_SESSION['name'] = 'shinyahirai';

    if (isset($_SESSION['name'])) {
        echo $_SESSION['name'];
    } else {
        echo '$_SESSION["name"]は未定義';
    }

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
  <link rel="stylesheet" type="text/css" href="assets/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="assets/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" type="text/css" href="assets/css/custom.css">
  <link rel="stylesheet" type="text/css" href="assets/css/form.css">
  <link rel="stylesheet" type="text/css" href="assets/css/timeline.css">
</head>
<body>
  <!-- ググって適当なテンプレから引っ張ってくる -->
  <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-slide-dropdown">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#">ひとこと掲示版</a>
      </div>

      <!-- Collect the nav links, forms, and other content for toggling -->
      <div class="collapse navbar-collapse" id="bs-slide-dropdown">
        <ul class="nav navbar-nav navbar-right">
          <li><a href="#">Link</a></li>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
              <li><a href="#">Action</a></li>
              <li><a href="#">Another action</a></li>
              <li><a href="#">Something else here</a></li>
              <li class="divider"></li>
              <li><a href="#">Separated link</a></li>
            </ul>
          </li>
        </ul>
      </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
  </nav>

  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h1>ひとこと掲示版</h1>
      </div>
    </div>
    <!-- http://bootsnipp.com/snippets/featured/input-validation-colorful-input-groups -->
    <div class="row">
      <div class="col-md-4">
        <form action="bbs.php" method="post">

          <div class="form-group">
            <label for="validate-text">名前</label>
            <div class="input-group">
              <input type="text" class="form-control" name="name" id="validate-text" placeholder="つぶやき..." required>
              <span class="input-group-addon danger"><span class="glyphicon glyphicon-remove"></span></span>
            </div>
          </div>

          <div class="form-group">
            <label for="validate-length">ひとこと</label>
            <div class="input-group" data-validate="length" data-length="1">
              <textarea type="text" class="form-control" name="comment" id="validate-length" placeholder="Validate Length" required></textarea>
              <span class="input-group-addon danger"><span class="glyphicon glyphicon-remove"></span></span>
            </div>
          </div>
          
          <div class="row">
            <button type="submit" class="btn btn-primary col-xs-3 col-xs-offset-8" disabled>つぶやく</button>  
          </div>
          
        </form>
      </div>
      <!-- http://bootsnipp.com/snippets/featured/single-column-timeline-dotted -->
      <div class="col-md-8">
        <?php
            $sql = 'SELECT * FROM `messages` ORDER BY `created_at` DESC';
            $results = mysqli_query($db, $sql) or die(mysqli_error($db));
        ?>
        <div class="timeline-centered">
          <?php while ($message = mysqli_fetch_assoc($results)): ?>
          <article class="timeline-entry">
            <div class="timeline-entry-inner">
              <div class="timeline-icon bg-info">
                <i class="entypo-feather"></i>
              </div>
              <div class="timeline-label">
                <h2><a href="#"><?php echo $message['name'] ?></a> <span><?php echo $message['created_at']; ?></span></h2>
                <p><?php echo $message['comment'] ?></p>
              </div>
            </div>
          </article>
          <?php endwhile; ?>

          <article class="timeline-entry begin">
            <div class="timeline-entry-inner">
              <div class="timeline-icon" style="-webkit-transform: rotate(-90deg); -moz-transform: rotate(-90deg);">
                <i class="entypo-flight"></i> +
              </div>
            </div>
          </article>
        </div>
      </div>
    </div>
  </div>

  <!-- jQuery (JavaScript plugin) https://jquery.com/download/ -->
  <script type="text/javascript" src="assets/js/jquery-1.11.3.js"></script>
  <!-- Included other js files -->
  <script type="text/javascript" src="assets/bootstrap/js/bootstrap.js"></script>
  <script type="text/javascript" src="assets/js/form.js"></script>
  <script type="text/javascript">
    $(document).ready(function(){
        $(".dropdown").hover(            
            function() {
                $('.dropdown-menu', this).not('.in .dropdown-menu').stop( true, true ).slideDown("fast");
                $(this).toggleClass('open');        
            },
            function() {
                $('.dropdown-menu', this).not('.in .dropdown-menu').stop( true, true ).slideUp("fast");
                $(this).toggleClass('open');       
            }
        );
    });
  </script>

</body>
</html>
