<?php
 include_once "../common/session.php";
 include_once "../common/connect.php";
/*$_SESSION=array();
 if(isset($_COOKIE[session_name()])==true)
 {
    setcookie(session_name(),'',time()-42000,'/');
 }
*/
//$post=sanitize($_POST);
//session_start();
$user_Id=$_SESSION['user_Id'];


$_SESSION=array();
 if(isset($_COOKIE[session_name()])==true)
 {
    setcookie(session_name(),'',time()-42000,'/');
 }
session_destroy();
$dbh=null;
//$user_Id=$_SESSION['login'];
//var_dump($user_Id);
?>

<!DOCTYPE html>
<html>
<meat charset="UTF-8">
<link rel="stylesheet" href="user.css">
<title>ログアウト画面</title>

</head>
<body>
        <p class="sucsess">ログアウトしました。</p><br>
        <br/>
        <a href="user_login.php">ログイン画面へ</a>
        <?php

?>
</body>
