<?php
//session_start();
//session_regenerate_id(true);

/*if (session_status() == PHP_SESSION_NONE)
{ 
    session_start(); 
}*/
session_start();
session_regenerate_id(true);
//print '<a href="../Top.html">テキストトップ</a><br>';
$top_1=true;
switch(basename($_SERVER['REQUEST_URI']))
{
    case "user_add.php":
        break;
    case "user_login_check.php":
        break;
    case "user_home.php":
        $top_1=false;
        //break;
    case "logout_check.php":
        //$top_1=false;
        //break;
    case "reservation_add.php":

    case "reservation_add_check.php":

    case "reservation_add_done.php":
    default:
    if(isset($_SESSION['login'])==false)
    {
        print 'ログインされていません。<br>';
        //追加
        if(file_exists("../login/user_login.php")){
            //print '<a href="user_login.php">ログイン画面へ</a>';
            print '<a href="../login/user_login.php">ログイン画面へ</a>';
            exit();
        }else{
            print '<a href="../../login/user_login.php">ログイン画面へ</a>';
            exit();
        }
    }
    else
    {
        htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8');
        //print 'さんログイン中　';
        //print '<br>';
    }
    if($top_1)
    {
        
    }
    break;
}
?>