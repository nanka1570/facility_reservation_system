<?php
//session_destroy();
if(isset($_POST['name']))
{
    $user_name=$_POST['name'];
}else{
    $user_name="";
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title></title>
        <link rel="stylesheet" href="user.css">
        <h1 class="login"></br>新規登録</h1>
    </head>
    <body>
    </br>
    <form action="user_add_check.php" METHOD=POST>
            <p class="text">氏名　　　　　　　　　　　　姓<input type="text" name="name1" value>
</br>　　　　　　　　　　　　　　名<input type="text" name="name2" value></p>

            <p class="text">フリガナ　　　　　　　　　セイ<input type="text" name="name3"></br> 　　　　　　　　　　　　　メイ<input type="text" name="name4"></p>

            <p class="text">ユーザーID　　　　　　 　　　
            <input type="text" name="userId" value></p>
            <p class="text">パスワード　　　　　　 　 　　
            <input type="password" name="pass" value></p>
            <p class="text">パスワード（確認用）　 　 　　
            <input type="password" name="pass2" value></p>
            <p class="text">メールアドレス　　　 　 　　　
            <input type="text" name="mail_address" value></p>

            <p class="text">秘密の質問  　  　　　 　　  　 　　　　　　質問
            <select name='question'>
                <option value="1">質問A</option>
                <option value="2">質問B</option>
                <option value="3">質問C</option>
            </select></p>
            <?php
            if(isset($_POST["question"]))
            {
                $secret_quistion = $_POST["question"];

                //echo $secret_quistion;
            }
            ?>
            <p class="text">回答　　　　　　　 　　  　　　<input type="text" name=secret_answer value></p>

            <p class="logtext"><input type="button" onclick="history.back()" value="戻る">
            <input type="submit" name=entry  value="登録"></p>
            
        </form>
    </body>
</html>