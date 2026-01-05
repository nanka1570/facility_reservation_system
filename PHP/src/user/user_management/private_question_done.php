<?php
include_once "../../common/connect.php";
include_once "../../common/sanitize.php";
?>
<!-- <!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../common/form_style.css">
        <title></title>
    </head>
    <body> -->
    <?php
    try
    {
        $post=sanitizeinput($_POST);
        $user_Id=$_POST['user_Id'];
        $mail_address=$_POST['mail_address'];
        $secret_question=$_POST['question'];
        $secret_answer=$_POST['secret_answer'];
        $dbh=$conn;
        $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        //PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION:フェッチ構文        

        $sql="SELECT user_id,secret_question,secret_answer 
              FROM user_table 
              WHERE user_id=?
            　AND mail_address=? 
              AND secret_question=? 
              AND secret_answer=?";
        
        $stmt=$dbh->prepare($sql);

        $data = [$user_Id, $mail_address,$secret_question, $secret_answer];

        $stmt->execute($data);

        $rec = $stmt->fetch(PDO::FETCH_ASSOC);
         if($rec==true){
            echo '<p>認証に成功しました。</p><br>';
            echo '<form action="new_password_reset.php">';
            echo '<a href="new_password_reset.php">パスワードの再設定へ</a>';
            echo '</form>';
        }
        else
        {
            echo '<p>認証に失敗しました。</p>';
        }
        $dbh = null;
    }
    catch(Exception $e)
    {
        error_log($e->getMessage());
        print 'ただいま障害により大変ご迷惑をお掛けしております。';
        exit();
    }
    ?>
<!--     
</body>
</html> -->