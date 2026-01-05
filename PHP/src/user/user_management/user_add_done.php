<?php
//include_once "../common/connect.php";
//include_once "../common/sanitize.php";
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
        try{
            $post=sanitizeinput($_POST);
            $user_name=$_POST['name'];
            $user_Id=$_POST['user_Id'];
            $pass=$_POST['pass'];
            $pass2=$_POST['pass2'];
            $mail_address=$_POST['mail_address'];
            $secret_question=$_POST["secret_question"];
            $secret_answer=$_POST['secret_answer'];
            $login='O'; //ログインフラグ
            //var_dump($conn);
            $dbh=$conn;
            $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

            $sql='INSERT INTO user_table(user_Id,password,user_name,
                mail_address,secret_question,secret_answer,login_status) VALUES (?,?,?,?,?,?,?)';
            
            $stmt=$dbh->prepare($sql);
            
            $data[]=$user_Id;
            $data[]=$pass;
            $data[]=$user_name;
            $data[]=$mail_address;
            $data[]=$secret_question;
            $data[]=$secret_answer;
            $data[]=$login;
            $stmt->execute($data);

            $dbh=null;
            print $user_name;
            print '<p class="sucsess">さんを登録しました。</p><br/>';
            //print '<a href="user_login.php">ログイン画面へ';
            print '<a class="text" href="../../login/user_login.php">ログイン画面へ</a>';
            //print '<a href="user_add_result.php"></a>';
            exit();
            
        }
        catch(Exception $e){
            print $e;
            print 'ただいま障害により大変ご迷惑をお掛けしております。';
            exit();
        }
        //エラートラップ命令
        //[try]:本来行う命令
        //[catch]:エラーの場合で行う命令<a href="user_top1.php">戻る</a>
        ?>
        <a href="user_add_result.php"></a>
    </body>
</html>