<?php
include_once "../common/connect.php";
header("Refresh:600");
echo date('H:i:s Y-m-d');
$now='2025-11-12 14:30';//date('Y-m-d H:i');
print $name=$_POST['room_name'];
print $number=$_POST['room_number'];
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>画面表示</title>
    </head>
    <body>
        <?php
        try{
            $dbh=$conn;
            $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $sql="SELECT * FROM reservation_table 
                  WHERE room_number='$number'AND
                  (start_time_of_use < '$now' AND
                  end_time_of_use > '$now') AND
                  cancel_flag=''";
            $stmt=$dbh->prepare($sql);
            $stmt->execute();
        }catch(Exception $e){
            print "error";
            print $e;
        }
        $i=0;//データを一件だけ取得
        while($i<1){
            $rec=$stmt->fetch(PDO::FETCH_ASSOC);
            if($rec==false){
                $flag="C";
                break;
            }
            $flag="Y";
            $kaisi=$rec['start_time_of_use'];
            $owari=$rec['end_time_of_use'];
            $bikou=$rec['remark'];
            $i=$i+1;
        }    
        print '<br/>';
        if($flag=="C"){
            print "空室";
            print '<br/>';
        }
        else{
            print "使用中";print '<br />';
            print $name; print'<br />';
            print "利用時間:";
            print $kaisi;
            print "～";
            print $owari;print '<br />';
            print "備考:";
            print $bikou;

        }
        print'<form method=POST action="gamennseni.php">';
        print '<input type="button" onclick=location.href="gamennseni.php" value="戻る">';
        ?>
    </body>
</html>
