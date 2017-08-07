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
  <title>员工照片</title>
  <style type="text/css">
    .container{ width: 500px; height: 650px; margin: 0 auto; }
    .container form{ border: 1px solid #999; text-align: center; padding: 20px 0; }
    a{ text-decoration: none; }
  </style>
</head>

<body>
  <div class="container">
    <?php

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

    //图片上传及服务器端保存
    if( isset( $_GET['photono'] ) ){
      $photoNo = $_GET["photono"];
    }else{
      $photoNo = $_POST["photono"];
    }
    if( isset( $_FILES['file'] ) ){
      if ($_FILES["file"]["error"] > 0){
        echo "Error " . $_FILES["file"]["error"] . " : 无效操作" . "<br />";
      }else{
        $type = $_FILES["file"]["type"];
        if( stripos( $type, 'image' ) >= 0 ){
          $path = 'imgs/' . time() . $photoNo . '.jpg';
          move_uploaded_file( $_FILES["file"]["tmp_name"], $path );
          $database->update( 'emp', array( 'IMG'=>$path ), array( 'EMPNO'=>$photoNo ) );
          echo "<script>alert('Success : 修改成功！');</script>";
        }else{
          echo "<script>alert('Error : 修改无效！');</script>";
        }
      }
    }
    
    //返回这行数据
    $emp = $database->get( 'emp', '*', array( 'empno' => $photoNo ) );
  ?>

    <!--照片大图-->
    <img src="<?php echo $emp['IMG'] ?>" alt="员工照片" style="width:500px;height:500px"/>

    <!--功能菜单-->
    <form method="post" action="emp_photo.php" enctype="multipart/form-data">
      <input type="file" name="file" placeholder="请选择照片" />
      &nbsp;&nbsp;
      <input type="hidden" name="photono" value="<?php echo $photoNo; ?>"/>
      &nbsp;&nbsp;
      <input type="submit" value="更新照片">
      &nbsp;&nbsp;
      <a href="emp_manage.php"><input type="button" value="返回" /></a>
    </form>
  </div>
</body>
</html>




