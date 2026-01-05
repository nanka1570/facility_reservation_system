<?php
//include_once "common/connect.php";       //DB接続用コード
//include_once "common/session.php";
include_once "../common/DB_switch.php";
include_once "../common/session.php";
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="">
<title></title>
</head>
<body>
 <?php
    try
    {
        $dbh=$conn;
        $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

        $sql='SELECT user_id,user_name FROM user_table';     //DBの項目名注意!
        $stmt=$dbh->prepare($sql);
        $stmt->execute();

        $dbh=null;

        print 'ユーザー一覧<br/><br/>';
        print '<form method="post" action="user_branch.php">';     //画面遷移先をstaff_branch.phpにする
        while(true)
        {
            $rec=$stmt->fetch(PDO::FETCH_ASSOC);
            if($rec==false)
            {
                break;
            }
            print '<input type="radio" name="userid" value="'.$rec['user_id'].'">';
            print $rec['user_id'];
            print '　';
            print $rec['user_name'];
            print '<br />';
        }
        print '<div class=s_btn>';
        print '<input type="submit" name="disp" value="参照">';     //それぞれ参照、追加、修正、削除の画面へ遷移
        print '<input type="submit" name="add" value="追加">';
        print '<input type="submit" name="edit" value="修正">';
        print '<input type="submit" name="delete" value="削除">';
        print '</div>';
        print '</form>';
    }
    catch(Exception $e){
        print $e;
        print 'ただいま障害により大変ご迷惑をお掛けしております。';
        exit();
    }
?>
</body>
</html>