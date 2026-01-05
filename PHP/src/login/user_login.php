<?php
if(isset($_POST['user_Id']))
{
    $user_Id=$_POST['user_Id'];
}else{
    $user_Id="";
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<link rel="stylesheet" href="login.css">
</head>
<body>
   
    <p class="login">ユーザーログイン</p><br />
    
    <div class=bg>
    </br>
    <form action="user_login_check.php" method="post" >
        <p class="logtext">ユーザーID
        <input type="text" name="user_Id"></p><br />
        <p class="logtext">パスワード
        <input type="password" name="pass"></p>
        <a href="../user/user_management/private_question.php">
    <p class="btext">パスワードを忘れた場合 </p></a>
        <br />
        <br />
        <p class="logtext"><input type="submit" value="ログイン" class="logbutton">
        </p></br>
        </div>
</form>
    
    <p class="logtext">初めての方は↓</br><a href="../user/user_management/user_add.php" >新規登録</a></p>
    
    <br />
    <br>

</form>
</body>    
</html>