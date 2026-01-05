<?php
if(isset($_POST['name']))
{
    $user_name=$_POST['name'];
}else{
    $user_name="";
}
if(isset($_POST['code']))
{
    $user_Id=$_POST['user_Id'];
}else{
    $user_Id="";
}
?>
<html>
    <head>
        <meta charset="utf-8">
        <title></title>
        <meta name="" content="">
        <link rel="stylesheet" href="user.css">
   </head>
   <body class=small>
        <p class=top>パスワードの再設定</p><br>
        <form action="new_password_reset_check.php" method="post">
            <p class=text>ユーザーID
            <input type="text" name="user_Id"><br><br>
            パスワード
            <input type="password" name="pass"><br><br>
            
            <input type="button" onclick="location.href='private_question.php'" value="戻る">
            　　　　<input type="submit" value="変更"></p>
        </form>
    </body>
</html>
