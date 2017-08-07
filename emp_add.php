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
    div.container{ position: relative; width: 600px; height: 320px; margin: 0 auto; border: 2px dotted #999;}
    div p{ position: relative; left: 160px; }
    input{ position: absolute; left: 90px; }
  </style>
</head>

<body>
  <div class="container">
    <form method="get" action="emp_manage.php">
      <p>员工号<input type="number" name="add_empno" /></p>
      <p>姓名<input type="text" name="add_ename" /></p>
      <p>职位<input type="text" name="add_job" /></p>
      <p>主管工号<input type="number" name="add_mgr" /></p>
      <p>入职时间<input type="date" name="add_hiredate" /></p>
      <p>薪资<input type="number" name="add_sal" /></p>
      <p>部门号<input type="number" name="add_deptno" /></p>
      <p><input type="submit" /></p>
    </form>
  </div>

  
</body>
</html>
