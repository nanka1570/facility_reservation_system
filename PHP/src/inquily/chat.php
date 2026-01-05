<?php
//include_once "../connect.php";
//include_once "../session.php";
include_once "../../common/connect.php";
include_once "../../common/session.php";
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>チャット</title>
    </head>
    <body>
        <form action="chat_check.php" method="post" name="form">
         <input type="text" name="text" value>
         <input type="submit" onclick="location.href=chat_check.php" name="sou" value="送信"><br /> 
        </form>

        <form action="user_home.php" method="post" name="ho">
          <input type="submit" onclick="location.href=user_home.php" name="home" value="ホーム画面へ">  
        </form>
</body>
</html>
