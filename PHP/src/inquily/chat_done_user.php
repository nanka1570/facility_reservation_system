<?php
//include_once "../connect.php";
//include_once "../session.php";
include_once "../common/connect.php";
include_once "../common/session.php";
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>チャット</title>
    </head>
    <body>
<?php
$te=$_POST['text'];
$id=$_SESSION['user_Id'];
try{
    $user_id=$id;
    $text2=$te;
    $inquily_time=date("Y/m/d H:i:s");
    $text_category="Y";
    $dbh=$conn;
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $sql='INSERT INTO inquily_table("user_id",serial_number,"text",inquily_time,"text_category")
    VALUES(?,(SELECT COALESCE(MAX(serial_number)+1,0)FROM inquily_table),?,?,?)';
    $stmt=$dbh->prepare($sql);
    $data[]=$user_id;
    $data[]=$text2;
    $data[]=$inquily_time;
    $data[]=$text_category;
    $stmt->execute($data);
    $dbh=null;
    print 'お問合せ内容を送信しました';
    print '<input type="button" onclick=location.href="controlq_user.php" name="back" value="戻る">';
    print '<br />';
    //print '<input type="button" onclick=location.href="user_home.php" name="ba" value="ホームへ戻る">';
    print '<input type="button" onclick=location.href="../login/user_home.php" name="ba" value="ホームへ戻る">';
    exit();
}
catch(Exception $e){
    print $e;
    print 'ただいま障害により大変ご迷惑をお掛けしております。';
    exit();
}
?>
</body>
</html>
