<?php
include_once "../../common/connect.php";
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
    try
    {
        $post=sanitizeinput($_POST);
        $user_Id=$_POST['user_Id'];
        $pass=$_POST['pass'];
        $dbh=$conn;
        $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                
        $sql='UPDATE user_table 
              SET password=? 
              WHERE user_id=?';
        $stmt=$dbh->prepare($sql);
        /*$stmt->bindParam(':password', $pass); 
        $stmt->bindParam(':user_id', $user_Id);*/

        //var_dump($user_Id.':ID');
        echo '<br>';
        //var_dump($pass.':password');
        echo '<br>';
        
        /*$data=[$user_Id];
        $data=[$pass];*/
        //var_dump($stmt);
        echo '<br>';
        $stmt->execute([$pass,$user_Id]);

        print '再設定しました。<br>';
        //print '<a href="user_login.php">ログイン画面へ</a>';
        print '<a href="../../login/user_login.php">ログイン画面へ</a>';
        $dbh = null;
        exit();
    }
    catch(Exception $e)
    {
        print $e;
        print 'ただいま障害により大変ご迷惑をお掛けしております。';
        exit();
    }
    ?>
</body>
</html>