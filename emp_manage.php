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
  <title>员工信息管理系统</title>
  <style type="text/css">
    *{ margin: 0; padding: 0; }
    .mainContainer{ width: 1140px; margin: 0 auto; text-align: center; margin-top: 40px; }
    .mainContainer table,form,p{ margin-top: 20px; }
    .account{ position: absolute; right: 20px; top: -5px; }
    th, td{ width: 120px; padding-bottom: 20px; }
    th{ padding-top: 20px; }
    a{ text-decoration: none; display: inline-block; color: #00f; }
    a:hover{ background-color: #c63; }
    .td50{ width:70px; word-break:break-word; }
    .returnTop{ position: fixed; width: 60px; height: 60px; text-align: center; line-height: 60px; font-size: 14px; right: 20px; bottom: 20px; background-color: #ccc; }
  </style>
</head>

<body>
  <?php
    //后代遍历递归函数
    function digui( $no, $map ){
      $ans = '';
      foreach ( $map as $x ){
        if( $x['MGR'] == $no ){
          $ans = $ans.'<br />'.$x['ENAME'];
          $ans = $ans . digui( $x['EMPNO'], $map );
        }
      }
      return $ans;
    }

    //右上角导航菜单
    if( !empty( $_SESSION["usr"] ) ){
      echo "<p class='account'>欢迎登录，&nbsp;". $_SESSION['usr'] . "<br /><a href='index.php'>注销登录</a>
      &nbsp;<a href='psw_update.php'>修改密码</a></p>";
    }
  ?>

  <div class='returnTop'><a href="#top">返回顶部</a></div>

  <div class="mainContainer">
    <h1>系统主界面</h1>
    <form method="get" action="emp_manage.php">
      员工号：<input type="number" name="ask_empno" autofocus="autofocus" />&nbsp;&nbsp;
      姓名：<input type="text" name="ask_ename" />&nbsp;&nbsp;
      <input type="submit" value="查询" />
    </form>
    <p>
      <a href="emp_add.php" target="_self">增加新员工</a>
    </p>
    <table border=1>
      <tr>
        <th>员工号</th><th>姓名</th><th>照片</th><th>职位</th><th>主管</th><th>直接下属</th><th>下属</th><th>入职时间</th><th>薪资</th><th>部门号</th>
        <th>照片</th><th>编辑</th><th>删除</th>
      </tr>

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

        //增
        if( isset( $_GET["add_empno"] ) ){
          $row =  [
            "EMPNO" => $_GET["add_empno"],
            "ENAME" => $_GET["add_ename"],
            "JOB" => $_GET["add_job"],
            "MGR" => $_GET["add_mgr"],
            "HIREDATE" => $_GET["add_hiredate"],
            "SAL" => $_GET["add_sal"],
            "DEPTNO" => $_GET["add_deptno"]
          ];
          foreach( $row as $key=>$vlue ){
            if( empty( $vlue ) ){
              unset( $row[$key] );
            }
          }
          
          $new_row_id = $database->insert( "emp", $row );
        }

        //删
        if( isset( $_GET["delno"] ) ){
          $del_row_id = $database->delete( "emp", array( "EMPNO" => $_GET["delno"] ) );
        }

        //改
        if( isset( $_GET["edit_empno"] ) ){
          $update_row_id = $database->update( "emp", [
            "EMPNO" => $_GET["edit_empno"],
            "ENAME" => $_GET["edit_ename"],
            "JOB" => $_GET["edit_job"],
            "MGR" => $_GET["edit_mgr"],
            "HIREDATE" => $_GET["edit_hiredate"],
            "SAL" => $_GET["edit_sal"],
            "DEPTNO" => $_GET["edit_deptno"]
          ], [
                "EMPNO[=]" => $_GET["edit_empno"]
          ] );
        }

        //上下级关系遍历
        $all = $database->select( 'emp', '*', null );
        $map = array();
        foreach( $all as $one ){
          $map[ $one['EMPNO'] ] = $one;
        }
        foreach( $map as &$one ){
          $one['MGRNAME'] = '';
          $one['FCNAME'] = '';
          $o = $one;
          while( !empty( $o['MGR'] ) ){
            $o = $map[ $o['MGR'] ];
            $one['MGRNAME'] = $one['MGRNAME'].'<br />->'.$o['ENAME'];
          }
          foreach( $map as $tmp ){
            if( $tmp['MGR'] == $one['EMPNO'] ){
              $one['FCNAME'] = $one['FCNAME'].'<br />'.$tmp['ENAME'];
            }
          }
          $one['CNAME'] = digui( $one['EMPNO'], $map );
        }

      ?>


      <?php
        //无查询条件时执行全部打印
        if( empty( $_GET["ask_empno"] ) && empty( $_GET["ask_ename"] ) ){
          foreach( $map as $row ){ 
            echo "<tr>";
            echo "<td>" . $row['EMPNO'] . "</td>";
            echo "<td>" . $row['ENAME'] . "</td>";
            echo "<td><img style=\"width:60px;height:60px\" src=\"".$row['IMG'] ."\" /></td>";
            echo "<td>" . $row['JOB'] . "</td>";
            echo "<td>" . $row['MGRNAME'] . "</td>";
            echo "<td><div class=\"td50\">" . $row['FCNAME'] . "</div></td>";
            echo "<td><div class=\"td50\">" . $row['CNAME'] . "<div></td>";
            echo "<td>" . $row['HIREDATE'] . "</td>";
            echo "<td>" . $row['SAL'] . "</td>";
            echo "<td>" . $row['DEPTNO'] . "</td>";
      ?>
            <td><a href="emp_photo.php?photono=<?php echo $row['EMPNO']; ?>">照片</a></td>;
            <td><a href="emp_edit.php?editno=<?php echo $row['EMPNO']; ?>">编辑</a></td>;
            <td><a href="emp_manage.php?delno=<?php echo $row['EMPNO']; ?>">删除</a></td>;
      <?php
            echo "</tr>";
          }
        }else{
          //根据EMPNO、ENAME进行查询
          $sql = null;
          if( empty( $_GET['ask_empno'] ) && !empty( $_GET['ask_ename'] ) ){
            $sql = "SELECT * FROM emp WHERE ENAME LIKE '%".$_GET['ask_ename']."%'";
            $res = $database->query( $sql )->fetchAll();
          }else{
            $res = $database->select( "emp", "*",[
              "EMPNO[=]" => $_GET["ask_empno"]
            ]);
          }

          //记录筛选
          foreach( $map as $key=>$value ){
            $ok = false;
            foreach( $res as $r ){
              if( $r['EMPNO'] == $key ){
                $ok = true;
                break;
              }
            }
            if( !$ok ){
              unset( $map[$key] );
            }
          }

          //记录打印
          foreach( $map as $row ){
            echo "<tr>";
            echo "<td>" . $row['EMPNO'] . "</td>";
            echo "<td>" . $row['ENAME'] . "</td>";
            echo "<td><img style=\"width:60px;height:60px\" src=\"".$row['IMG'] ."\" /></td>";
            echo "<td>" . $row['JOB'] . "</td>";
            echo "<td>" . $row['MGRNAME'] . "</td>";
            echo "<td><div class=\"td50\">" . $row['FCNAME'] . "</div></td>";
            echo "<td><div class=\"td50\">" . $row['CNAME'] . "<div></td>";
            echo "<td>" . $row['HIREDATE'] . "</td>";
            echo "<td>" . $row['SAL'] . "</td>";
            echo "<td>" . $row['DEPTNO'] . "</td>";
      ?>
          <td><a href="emp_photo.php?photono=<?php echo $row['EMPNO']; ?>">照片</a></td>;
          <td><a href="emp_edit.php?editno=<?php echo $row['EMPNO']; ?>">编辑</a></td>;
          <td><a href="emp_manage.php?delno=<?php echo $row['EMPNO']; ?>">删除</a></td>;
      <?php
        }}
      ?>
    </table>
    <br /><br />
  </div>
</body>
</html>
