<?php
include_once "../../common/connect.php";
?>
<!DOCTYPE html>
<link rel="stylesheet" href="gamen.css">
<html>
<head>
<title>部屋選択</title>
<body>
<?php
    try{
        $dbh=$conn;
        $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        
        $sql="SELECT room_number,room_name FROM facility_table  ";
        $stmt=$dbh->prepare($sql);
        $stmt->execute();
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    $sql2="SELECT room_name FROM facility_table";
    $stmt2=$dbh->prepare($sql2);
    $stmt2->execute();
    print'<form method="post" action="gamenn_2.php" class="room">';
        while(true)
        {
            $rec=$stmt2->fetch(PDO::FETCH_ASSOC);
            if($rec==false)
            {
                break;
            }
            $rname=$rec["room_name"];
            print '<label class="font">';
            print'<input type="checkbox" name="check[]" value="'.$rec['room_name'].'" class=checkbox>';
            print $rec['room_name'];
            print '</label>';
            print '<br>';

        }
    }
    catch(Exception $e){
    print $e;
    print'error!';
    exit();
    }
    print '<p><input type="button" class="backbutton" onclick=location.href="../../login/admin_home.php" value="ホーム画面へ">';
    print '<input type="button" class="backbutton" onclick=location.href="gamennseni.php" value="戻る">';
    print '　　　　<input type="submit" class="backbutton" value="選択した部屋を表示"></p>';
    print '</form>';
?>
</body>
</html>