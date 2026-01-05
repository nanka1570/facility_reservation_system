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
include_once "../common/DB_switch.php";
// include_once "../common/connect.php";
include_once "../common/session.php";
include_once "../common/sanitize.php";

try{
    $post=sanitizeinput($_POST);
    $user_Id=$_POST['user_Id'];
    /*$user_Id2=$_POST['user_Id'];
    $user_Id3=$_POST['user_Id'];*/
    $pass=$_POST['pass'];
    //$pass=md5($pass);

    /*if(isset($_POST['debug_T']))
    {
        $user_Id="1";
    }
    /*if(!is_numeric($user_Id))
    {
        print 'ユーザーIDは数値で入力してください。';
        print '<br />';
        include_once "./user_login.php";
    }*/
    $dbh=$conn;
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    if(isset($_POST['debug_T']))
    {
        $sql='SELECT user_name FROM user_table';
    }
    else
    {
        $sql='SELECT *
              FROM user_table
              WHERE user_id=:user_Id AND password=:password';

    }

    $stmt=$dbh->prepare($sql);
    //$a = 
    $stmt->bindParam(':user_Id', $user_Id, PDO::PARAM_STR); 
    $stmt->bindParam(':password', $pass, PDO::PARAM_STR); 
    $stmt->execute();

    /*if(isset($_POST['debug_T']))
    {
        $stmt->execute();
    }
    else
    {
        
    }*/
    $rec = $stmt->fetch(PDO::FETCH_ASSOC);
    // var_dump($rec);
    if($rec == false)
    {
        echo 'ユーザーIDかパスワードが間違っています。<br>';
        include_once "./user_login.php"; 
    }
    else
    {
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
        
        // var_dump($stmt->execute());
        // echo '@<br>';
        // var_dump($stmt);
        // echo '{}<br>';
        $dbh = null;
        /*echo '<form method="post" action="user_home.php">';
        echo '<input type="hidden" name="user_Id" value="'.$user_Id.'">';
        echo '123456';
        echo '</form>*/
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