<?php
include_once "../../common/connect.php";
$printdate=date('m-d H:i');
$number=$_POST['room_number'];
$name=$_POST['room_name'];
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="gamen.css">
<title>画面表示</title>
</head>

<?php
$sqldate=date('Y-m-d H:i');
try{
    $dbh=$conn;
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $sql="SELECT start_time_of_use,end_time_of_use,remark FROM reservation_table
            WHERE room_number=$number 
            AND (start_time_of_use <= '$sqldate' AND end_time_of_use >='$sqldate')
            AND cancel_flag=''";

    $stmt=$dbh->prepare($sql);
    $stmt->execute();
    while(true){
        $rec=$stmt->fetch(PDO::FETCH_ASSOC);
        if($rec==true){
            $class="use";
            // $kaisi=$rec['start_time_of_use'];
            // $owari=$rec['end_time_of_use'];
            $bikou=$rec['remark'];
            $newDateTimest=new DateTime($rec['start_time_of_use']);
            $kaisi=$newDateTimest->format("m/d H:i");
            $newDateTimeend=new DateTime($rec['end_time_of_use']);
            $owari=$newDateTimeend->format("m/d H:i");
            break;
        }else{
            $class="not";
        }
    }
}catch(Exception $e){
    print "error";
    print $e;
}
if($class=='not'){
    print '<body class="'.$class.'" onclick=location.href="gamennseni.php"><button class="notbutton">';
    print '<form method=POST action="gamennseni.php">';
    //print '<p> '.$printdate.'</p>';
    print '<p>空室</p></button>';
    //var_dump($rec);
}else{
    print '<body class="'.$class.'" onclick=location.href="gamennseni.php"><button class="usebutton">';
    print'<form method=POST action="gamennseni.php">';
    //print '<p>'.$printdate.'</p>';
    print '<p class=btext>'.$name.'</p>';
     print '<p class=btext>';
    print $kaisi."～".$owari.'</br>';
    print '<p class=btexth>'.$bikou.'</p>';
    
    print '</button>';
}
?>
<script>
//ページ更新
setTimeout(function () {
    location.reload();
}, 10000);数字は更新の時間間隔(ミリ秒)
</script>
</body>
</html>