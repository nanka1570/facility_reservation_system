<?php

include_once "../../common/sanitize.php";
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="user.css">
        <title></title>
    </head>
    <body>
    <?php
    //パスワード入力内容の検閲
    $post=sanitizeinput($_POST);
    $user_Id=$_POST['user_Id'];
    $pass=$_POST['pass'];

    if($user_Id=='')
    {
        print '<p class=alart>ユーザーIDが入力されていません。</p>';
    }
    if($pass=='')
    {
        print '<p class=alart>パスワードが入力されていません。</p>';
    }
    if($user_Id == "" || $pass == "")
    {
        echo '<br />';	
        include_once "./new_password_reset.php"; 
    }
    else
    {


        print '<p class=top>この内容で登録しますか？</p>';
        print '<form method="POST" action="new_password_reset_done.php"><p class=text>';
        print '<input type="hidden" name="user_Id" value="'.$user_Id.'">';
        $p_h=password_hash($_POST['pass'],PASSWORD_DEFAULT); //0203
        print '<input type="hidden" name="pass" value="'.$p_h.'">ID　　　';
        echo $user_Id;
        echo '<br>pass　　　';
        echo $pass;
        print ' <br />';
        print ' <br />';
        
        print '<input type="button" onclick="history.back()" value="戻る">　　';
        print '<input type="submit" value="送信">';
        print '</p></form>';
    }
    ?>
    </body>
</html>
