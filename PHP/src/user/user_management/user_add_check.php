<?php
//include_once "../common/sanitize.php";
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
        $post=sanitizeinput($_POST);
        $user_name=$_POST['name1'].$_POST['name2']."(".$_POST['name3'].$_POST['name4'].")";
        $user_Id=$_POST['userId'];
        $pass=$_POST['pass'];
        $pass2=$_POST['pass2'];
        $mail_address=$_POST['mail_address'];
        $secret_question=$_POST["question"];
        $secret_answer=$_POST['secret_answer'];

        if($user_name==""){
            print 'ユーザー名が入力されていません。<br/>';
        }else{
            print 'ユーザー名：';
            print $user_name;
            print '<br />';
        }
        if($pass == ''){
            print 'パスワードが入力されていません。<br />';
        }
        if($pass != $pass2){
            print 'パスワードが一致しません。<br />';
        }
    if($user_name=='' || $pass=='' || $pass!=$pass2){
        print '<br />';
        include_once "user_add.php";
    }else{
        print '<p class="alart">この内容で登録しますか？</p>';
        $password=md5($pass);?>
        <p class="text"><table class="table">
        <tr><th>氏名<td><?php print $_POST['name1']; ?>
        <?php print $_POST['name2']; ?></td></th> </tr>

        <tr><th> フリガナ<td>
            <?php print $_POST['name3']; ?><?php print $_POST['name4']; ?></td></th> </tr>

            <tr><th>   ユーザーID
        <td><?php print $_POST['userId']; ?></td></th> </tr>
        <tr><th>   メールアドレス
        <td><?php print $_POST['mail_address']; ?></td></th> </tr>

        <tr><th>   秘密の質問
        <td>質問<?php print $_POST['question']; ?></td></th> </tr>
        <tr><th>    回答<td><?php print $_POST['secret_answer']; ?></td></th></tr></table></p><?php
        print '<form method="POST" action="user_add_done.php">';
        print '<input type="hidden" name="name" value="'.$user_name.'">';
        //type=hidden：画面に表示せずに遷移先のページの$_POSTに渡す
        print '<input type="hidden" name="user_Id" value="'.$user_Id.'">';
        print '<input type="hidden" name="pass" value="'.$pass.'">';
        print '<input type="hidden" name="pass2" value="'.$pass2.'">';
        print '<input type="hidden" name="mail_address" value="'.$mail_address.'">';
        print '<input type="hidden" name="secret_question" value="'.$secret_question.'">';
        print '<input type="hidden" name="secret_answer" value="'.$secret_answer.'">';
        print '<br />';
        print '<input type="button" onclick="history.back()" value="戻る">';
        print '<input type="submit" value="登録">';
        print '</form>';
    }
    ?>
    </body>
</html>