<?php
include_once "../../common/connect.php";
$datenow="2025-11-12 14:30";//date('Y-m-d H:i');
$number=$_POST['room_number'];
$name=$_POST['room_name'];
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="gamenn.css">
<title>画面表示</title>
</head>
<body>
<?php
try{
    $dbh=$conn;
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $sql="SELECT * FROM reservation_table
            WHERE room_number='$number'
            AND start_time_of_use <'$datenow'
            AND end_time_of_use >'$datenow'";
    $stmt=$dbh->prepare($sql);
    $stmt->execute();
    $rec=$stmt->fetch(PDO::FETCH_ASSOC);
//var_dump($rec);
    if($rec==false){
        $use="not";
        print '<div class="'.$use.'">';
        print '<br><p>現在時刻 '.$datenow.'</p>';
        print '<p>空室</p><br>';
    }else{
        $use="use";
        //var_dump($rec);
        $kaisi=$rec['start_time_of_use'];
        $owari=$rec['end_time_of_use'];
        $bikou=$rec['remark'];
        $newDateTime=new Datetime($rec['start_time_of_use']);
        $kaisi=$newDateTime->format("H:i");
        $newDateTime=new Datetime($rec['end_time_of_use']);
        $owari=$newDateTime->format("H:i");
        print '<div class="'.$use.'">';
        print '<br><p>現在時刻 '.$datenow.'</p>';
        print '<p>使用中</p>';
        print '<p>'.$name.'</p>';
        print '<p>利用時間:';
        print $kaisi."～".$owari.'</p><br />';
        print '<p>'.$bikou.'</p>';

    }
}catch(Exception $e){
    print "error";
    print $e;
}
?>
</div>
<?php
print'<form method=POST action="gamennseni.php">';
print '<input type="button" onclick=location.href="gamennseni.php" value="戻る">';        
?>
<script>/*
//ページ更新
setTimeout(function () {
    location.reload();
}, 10000);//数字は更新の時間間隔(ミリ秒)
</script>
</body>
</html>