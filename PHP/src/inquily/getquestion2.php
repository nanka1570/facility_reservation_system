<?php
//include_once "../connect.php";
//include_once "../session.php";
include_once "../common/connect.php";
include_once "../common/session.php";
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF=8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../common/admin_basic.css">
<link rel="stylesheet" href="textclass.css">
<title>???</title>
</head>
<body>
    <header class="admin-header">
        <h1>お問い合わせ一覧</h1>
    </header>
<?php
    try{
    $dbh=$conn;
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    // print'<p class="midasi"> お問い合わせ一覧<br /></p>';
    //print'<form method="post"action="controlq_kanri.php">';
    $dbh=$conn;
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    $sql='SELECT user_id,MAX(serial_number)AS serialnum FROM inquily_table GROUP BY user_id';
    //MAX(serial_number)に新しく名前をつけないとエラー
    $stmt=$dbh->prepare($sql);
    $stmt->execute();
    while(true)
    {
        $serial=$stmt->fetch(PDO::FETCH_ASSOC);
        if($serial==false)
        {
            break;
        }
        $serialnum=$serial["serialnum"];
        $sql2="SELECT user_id,text,inquily_time FROM inquily_table WHERE serial_number=$serialnum";
        $stmt2=$dbh->prepare($sql2);
        $stmt2->execute();
        
        while(true)
        {
            $text=$stmt2->fetch(PDO::FETCH_ASSOC);
            if($text==false)
            {
                break;
            }
            // print'ID        ';
            // print$text["user_id"];
            $user_id=$text["user_id"];
            // print'</br>';
            $newDateTime=new Datetime($text['inquily_time']);
            $inquily_time=$newDateTime->format("Y/m/d H:i");
            // print'<br />';
             print'<form method=POST action="controlq_kanri.php">';
            print'<input type=hidden  name="user_id" value="'.$user_id.'">';
            $_SESSION['user']=$user_id; 
             ?><p class="buttont"> <button class="button"> <?php print$user_id; ?>  　 <?php print$inquily_time; ?> </br> <?php print$text['text']; ?> </button></p> <?php
            //  print'<input type="submit" name="selectQ" onclick=""value="'.$text['text'].'">';
            print '</form>';
        }
    }
    }
    catch(Exception $e){
        print $e;
        print'ないよ';
        exit();
    }
    //追加
    print '<p class="buttont"><input type="submit"  onclick=location.href="../login/admin_home.php" name="ba" value="ホームへ戻る" ></p>';
    $dbh=null;
?>
</body>
</html>