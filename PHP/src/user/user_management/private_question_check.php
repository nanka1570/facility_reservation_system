<?php
//include_once "../../common/connect.php";
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
    //質問の入力内容の検閲
    $post=sanitizeinput($_POST);
    $user_Id=$_POST['user_Id'];
    $mail_address=$_POST['mail_address'];
    //$pass=$_POST['pass'];
    $secret_question = $_POST["question"];
    $secret_answer= $_POST["secret_answer"];

    //入力内容表示
    // echo '<p class="top">'.$secret_question.'</p>';
    // print'<br>';
    // echo '<p class="top">'.$secret_answer.'</p>';

    if($user_Id=='')
    {
        print '<p>ユーザーIDが入力されていません。</p><br />';
    }
    if($mail_address=='')
    {
        print '<p>メールアドレスが入力されていません。</p><br />';
    }
        

    if($secret_answer=='')
    {
        print '<p>質問の回答が入力されていません。</p><br />';
    }
    
    if($user_Id=='' || $mail_address==''  || $secret_answer=='')
    {
        print '<br />';	
        include_once "./private_question.php";  
    }
    else
    {
        //$pass=md5($pass);
        print '<form method="post" action="private_question_done.php">';
        print '<input type="hidden" name="user_Id" value="'.$user_Id.'">';
        print '<input type="hidden" name="mail_address" value="'.$mail_address.'">';
        //print '<input type="hidden" name="pass" value="'.$pass.'">';
        print '<input type="hidden" name="secret_question" value="'.$secret_question.'">';
        print '<input type="hidden" name="secret_answer" value="'.$secret_answer.'">';
        print '<br />';
        print '<input type="submit" value="次へ">';
        print '</form>';
        exit();
    }

    ?>
    </body>
</html>