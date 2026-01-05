<?php
include_once "../common/session.php";
include_once "../common/connect.php";
//$user_Id=$_SESSION['user_Id'];
//var_dump($user_Id);
$user_Id=$_SESSION['user_Id'];
?>
<!DOCTYPE html>
<html>
<meat charset="UTF-8">
<title>確認画面</title>
<link rel="stylesheet" href="user.css">

</head>
<body> 
 
<!--//$user_Id = $_POST['user_Id'];-->
<form action="logout.php" method="post">
    <p class=alart>ログアウトしますか？<br/>
    <br/>
    <input type="submit" value="はい">
<!--/*print'<form action="logout.php" method="post">
    ログアウトしますか？<br/>
    <br/>
    <input type="hidden" name="user_Id" value="'.$user_Id.'">
    <input type="submit" value="はい">*/ -->   
</form>

<?php
if($user_Id==0){
print '<form action="admin_home.php">';
print '<br/>';
print '<input type="submit" value="いいえ"><br/>';
print '</form>';
}
else{
    print '<form action="user_home.php">';
    print '<br/>';
    print '<input type="submit" value="いいえ"><br/>';
    print '<input type="hidden" name="pagename" value="トップページ">';
    print '</form>';
}
?>
</body>
</html>