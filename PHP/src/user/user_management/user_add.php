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
    <table>
    <form action="user_add_check.php" METHOD=POST>
        <table class="sort"><tr>
            <th>氏名</th><td>姓</td><td><input type="text" name="name1" value></td><br>
            <td>名</td><td><input type="text" name="name2" value></td></tr>

            <tr>
            <th>フリガナ<td>セイ</td><td><input type="text" name="name3"></td><td>メイ</td><td><input type="text" name="name4"></td>
            </tr>
            <tr><th>ユーザーID</th><td></td>
            <td><input type="text" name="userId" value></td></tr>
            <tr><th>パスワード</th><td></td>
            <td><input type="password" name="pass" value></td></tr>
            <tr><th>パスワード(確認用)</th><td></td>
            <td><input type="password" name="pass2" value></td></tr>
            <tr><th>メールアドレス</th><td></td>
            <td><input type="text" name="mail_address" value></td></tr>

            <tr><th>秘密の質問<td>質問</td>
            <td>
            <select name='question' class="ques">
                <option value="1">質問A</option>
                <option value="2">質問B</option>
                <option value="3">質問C</option>
            </select></td></tr>
            <?php
            if(isset($_POST["question"]))
            {
                $secret_quistion = $_POST["question"];

                //echo $secret_quistion;
            }
            ?>
            <tr><th>回答</th><td></td><td><input type="text" name=secret_answer value></td></tr></table>

            <p class="logtext"><input type="button" class="button" onclick="location.href='../../login/user_login.php'" value="戻る">
            <input type="submit" class="button" name=entry  value="登録"></p>
        </table>
        </form>
    </body>
</html>