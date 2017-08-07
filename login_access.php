<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<?php
  //表单检查
  $usr = $_POST["usr"];
  $psw = $_POST["psw"];
  if( empty( $usr ) || empty( $psw ) ){
     header("Location:index.php?error=2&usr=$usr&psw=$psw");
     exit();
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

  // 身份验证
  $rows = $database->select( 'users', '*', array('username'=>$usr) );
  if( $rows[0]['pwd'] == $psw ){
    session_start();
    $_SESSION['isLogin'] = true;//权限
    $_SESSION['usr'] = $usr;    //身份
    header("Location:emp_manage.php?usr=" . $usr);
    exit();
  }else{
    header("Location:index.php?error=1&usr=$usr&psw=$psw");
    exit();
  }
?>