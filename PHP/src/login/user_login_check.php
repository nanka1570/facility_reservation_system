<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<link rel="stylesheet" href="login.css">
</head>
<body>
<?php
//include_once "connect.php";
//include_once "session.php";
include_once "../common/connect.php";
include_once "../common/session.php";
include_once "../common/sanitize.php";

try{
    $post=sanitizeinput($_POST);
    $user_Id=$_POST['user_Id'];
    $pass=$_POST['pass'];//0204

    $dbh=$conn;
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    $sql='SELECT * FROM user_table WHERE user_id=:user_Id';//0204
    $stmt=$dbh->prepare($sql);
    $stmt->bindParam(':user_Id', $user_Id, PDO::PARAM_STR); 
    $stmt->execute();

    $rec = $stmt->fetch(PDO::FETCH_ASSOC);
    if($rec == false)
    {
        echo 'ユーザーIDが登録されていません。<br>';//0204
        include_once "./user_login.php"; 
    }
    else
    {
        if(password_verify ( $pass , $rec['password'] )==false){//0204
            echo 'パスワードが一致しません。<br>';
            include_once "./user_login.php";
        }
        elseif($user_Id == null){
            echo 'ユーザーIDが一致しません。<br>';
            include_once "./user_login.php";
        }
        else{
            //session_start();
            $_SESSION['login']=1;
            $_SESSION['user_Id']=$user_Id;
            $_SESSION['user_name']=$rec['user_name'];

            $sql="UPDATE user_table 
                SET login_status= 'I'
                WHERE user_id=:user_Id AND password=:password";
            $stmt=$dbh->prepare($sql);
            $stmt->bindParam(':user_Id', $user_Id, PDO::PARAM_STR); 
            $stmt->bindParam(':password', $pass, PDO::PARAM_STR); 

            $dbh = null;
            if($user_Id==0){
                print'<p class="sucsess">ログインに成功しました。</p>';
                print'<form action="admin_home.php">';
                    print'<p class="logtext"><input type="submit" value="管理者ホーム画面へ"></p>';
                print'</form>';
            }
            else{
                ?>
                <p class="sucsess">ログインに成功しました。</p></br>
                <p class="addtext"><a href="user_home.php">ホーム画面へ</a></p>
            <?php
            $_SESSION['pagename']="ホームページ";
            exit();
            }
        }
        exit();
    }
}
catch(Exception $e)
{
    print $e;
    print 
    exit();
}
?>
</body>    
</html>