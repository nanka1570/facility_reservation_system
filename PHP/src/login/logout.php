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
$dbh=$conn;
$dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
$sql = "UPDATE user_table 
        SET login_status='O'
        WHERE user_id=:user_Id";
$stmt=$dbh->prepare($sql);
$stmt->bindParam(':user_Id', $user_Id, PDO::PARAM_STR); 
//$data[]=$user_Id;
//$stmt->execute($data);
$stmt->execute();

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
