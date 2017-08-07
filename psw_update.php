<!DOCTYPE html>
<?php 
  //权限检查
  session_start();
  if( empty( $_SESSION['isLogin'] ) ){
    header("Location:index.php");
    exit();
  }
?>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title></title>
  <style type="text/css">
    div.container{ position: relative; width: 600px; height: 180px; margin: 0 auto; border: 2px dotted #999;}
    div p{ position: relative; left: 160px; }
    input{ position: absolute; left: 90px; }
  </style>
</head>

<body>
  <div class="container">
    <form method="post" action="psw_update.php">
      <p>原密码<input type="password" name="oldpsw" /></p>
      <p>新密码<input type="password" name="newpsw1" /></p>
      <p>重复新密码<input type="password" name="newpsw2" /></p>
      <p><input type="submit" /></p>
    </form>
  </div>  
</body>
</html>

<?php
  if( !empty( $_POST['oldpsw'] ) && !empty( $_POST['newpsw1'] && !empty( $_POST['newpsw2'] ) ) ){
    //获取新旧密码
    $oldpsw = $_POST['oldpsw'];
    $newpsw1 = $_POST['newpsw1'];
    $newpsw2 = $_POST['newpsw2'];
    if( $newpsw1 != $newpsw2 ){
      echo "<script type='text/javascript'> alert('两次输入密码不一致'); </script>";
    }

    //数据库连接与配置
    include( 'medoo.php' );
    $database = new medoo([
      // 必须配置项
      'database_type' => 'mysql',
      'database_name' => 'erpdb',
      'server' => 'localhost',
      'username' => 'root',
      'password' => '',
      'charset' => 'utf8',
      // 可选参数
      'port' => 3306,
      // 可选，定义表的前缀
      'prefix' => '',
      // 连接参数扩展, 更多参考 http://www.php.net/manual/en/pdo.setattribute.php
      'option' => [
          PDO::ATTR_CASE => PDO::CASE_NATURAL
      ]
    ]);

    //密码更新
    $all = $database->select( "users", "*", null );
    foreach( $all as $row ){
      if( $row['username'] == $_SESSION['usr'] && $oldpsw == $row['pwd'] ){
        $database->update( "users", [ "pwd"=>$newpsw2 ], [ "username[=]"=>$_SESSION['usr'] ] );
        $_SESSION['isLogin'] = false;
        header("Location:index.php");
        exit();
      }
    }
  }
?>