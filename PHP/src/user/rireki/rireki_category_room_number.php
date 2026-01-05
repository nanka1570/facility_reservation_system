<?php
include_once "../../common/connect.php";
include_once "../../common/session.php";
$_SESSION['pagename']="利用履歴";
include_once "../../login/user_home.php";
$_SESSION['pagename']="ホームページ";
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="rireki.css">
<title>履歴ソート</title>
<body>
<?php
try{
    $dbh=$conn;    
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION); 
    ?>

    <?php
    $id=$_SESSION['user_Id'];
    print '<br>';
    $sql="SELECT DISTINCT room_name FROM history_table WHERE user_id='$id'";
    $stmt=$dbh->prepare($sql);
    $stmt->execute();
}
catch(Exception $e){
    print $e;
print'error!';
exit();
}
print '<p class="title">表示する部屋を選択</p>';
    while(true){
        $rec=$stmt->fetch(PDO::FETCH_ASSOC);
        if($rec==false)
            {
                break;
            }
        
        print'<form method="POST" action="rireki.php">';
        print'<p class="right">';
        print'<input type=hidden  name="atai" value="'.$rec['room_name'].'">';
        print'<input type="hidden"  name="cate" value="room_name">';
        print'<input type="submit" class="button" name="sousin" onclick=""value="'.$rec['room_name'].'" >';
        print '<input type="hidden" class="pagename" name="pagename" value="利用履歴">';
        print '</p>';
        print '</form>';       
    }
            print'<form method="POST" action="rireki.php">';
            print'<p class="right">'; 
            print '<input type="submit" class="button" name="back" value="戻る">';
            print '<input type="hidden" class="pagename" name="pagename" value="利用履歴">';
            print '</p></form>';
?>
</body>
</html>