<?php
include_once "../../common/connect.php";
include_once "../../common/sanitize.php";
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="common/form_style.css">
        <title></title>
    </head>
    <body>
    <?php
    //質問の入力内容の検閲
    $post=sanitizeinput($_POST);
    $mail_address=$_POST['mail_address'];
    $secret_question = $_POST["question"];
    $secret_answer= $_POST["secret_answer"];

    if($mail_address=='')
    {
        print 'メールアドレスが入力されていません。<br />';
    }

    if($secret_answer=='')
    {
        print '質問の回答が入力されていません。<br />';
    }
    
    if($mail_address==''  || $secret_answer=='')
    {
        print '<br />';	
        include_once "./question_reset.php";  
    }
    else
    {
        //$pass=md5($pass);
        print '<form method="post" action="question_reset_done.php">';
        print '<input type="hidden" name="mail_address" value="'.$mail_address.'">';
        //print '<input type="hidden" name="pass" value="'.$pass.'">';
        print '<input type="hidden" name="secret_question" value="'.$secret_question.'">';
        print '<input type="hidden" name="secret_answer" value="'.$secret_answer.'">';
        print '<br />';
        var_dump($secret_question);
        echo '<br>';
        var_dump($secret_answer);
        print '<input type="button" onclick="location.href=private_question.php" value="戻る">';
        print '<input type="submit" value="登録">';
        print '</form>';
    }

    ?>
    </body>
</html>