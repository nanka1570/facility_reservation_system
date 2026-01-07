<?php
include_once "../../common/connect.php";
?>
<!DOCTYPE html>
<link rel="stylesheet" href="gamen.css">
<html>
<head>
<title>部屋選択</title>
<body>
<table>
<?php
$cname="";
    try{
        $dbh=$conn;
        $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $sql="SELECT category_number,room_name FROM facility_table  ";
        $stmt=$dbh->prepare($sql);
        $stmt->execute();

        print'<form method="post" action="gamenn_2.php" class="room">';
        while(true)
        {
            $rec=$stmt->fetch(PDO::FETCH_ASSOC);
            if($rec==false)
            {
                break;
            }
            $cnum=$rec["category_number"];

            $sql2="SELECT category_name FROM category_table WHERE category_number=$cnum";
            $stmt2=$dbh->prepare($sql2);
            $stmt2->execute();
            $rec2=$stmt2->fetch(PDO::FETCH_ASSOC);

            $rname=$rec["room_name"];
            if($cname!=$rec2["category_name"]){
                if($cname!=""){
                    print '</th>';
                    print '</tr>';
                }
                $cname=$rec2["category_name"];
                print '<tr><th><a class="category">●'.$cname.'</a>';
                print'<br>';
                print'<label class="font">';
                print'<input type="checkbox" name="check[]" value="'.$rec['room_name'].'" class=checkbox>';
                print $rec['room_name'];
                print'</label>';
                print'<br>';
            }else{
                print'<label class="font">';
                print'<input type="checkbox" name="check[]" value="'.$rec['room_name'].'" class=checkbox>';
                print $rec['room_name'];
                print'</label>';
                print'<br>';
            }

        }
    }
    catch(Exception $e){
    print $e;
    print'error!';
    exit();
    }
?></table><?php    
    print '<p><input type="button" class="backbutton" onclick=location.href="../../login/admin_home.php" value="ホーム画面へ">';
    print '<input type="button" class="backbutton" onclick=location.href="gamennseni.php" value="戻る">';
    print '　　　　<input type="submit" class="backbutton" value="選択した部屋を表示"></p>';
    print '</form>';
?>
</body>
</html>