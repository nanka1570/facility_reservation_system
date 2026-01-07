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
<meta charset="UTF-8">
<link rel="stylesheet" href="../../common/user_basic.css">
<link rel="stylesheet" href="rireki.css">
<title>履歴</title>
</head>
<body class="hisbody">
<?php
$date=$_POST['date'];
$room=$_POST['room'];
$cancel=$_POST['cancel'];
if($room!=null)
{
$room_category="Y";
}else{
   $room_category="N";
}
if($date!=null)
{
    $date_category="Y";
}else{
   $date_category="N";
}
print '<p class="title">絞り込み　</p>';
print '<form method="post" style="display:inline" action="rireki_category.php">';
print '<span class="font">部屋名</span>';
print '<input type="text" class="room_name" name="room" value=>';

print '<span class="font">利用日時</span>';
print '<input type="date" class="nitizi" name="date">';

print '<select name="cancel">';
print '<option value="Y">キャンセル済みを表示する</option>';
print '<option value="N">キャンセル済みを表示しない</option>';
print '<option value="A">キャンセル済みのみ表示</option></select>';
print '<input type="submit"  name="reload" class="button3" value="検索">';
print '</form>';
print '<form style="display:inline" action="rireki.php">';
print '<input type="submit" name="back" class="button3"  value="絞り込みリセット">';
print '</form>';

print'<table class="histable" align=left width=100% >';
print'<tr>';
print'<th class="histh">部屋名</th>';
print'<th class="histh">利用人数</th>';
print'<th class="histh">開始時間</th>';
print'<th class="histh">終了時間</th>';
print'<th class="histh">合計金額</th>';
print'<th class="histh">備考</th>';
print'<th class="histh">キャンセル状況</th>';
print"</tr>";
print '<br/>';
?>
<?php
try{

    $dbh=$conn;
 $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
 $id=$_SESSION['user_Id'];
 $sql="SELECT room_name,number_of_user,start_time_of_use,end_time_of_use,sum_of_price,remark,cancel_flag FROM history_table
  WHERE user_id ='$id' ORDER BY start_time_of_use ASC";
 $stmt=$dbh->prepare($sql);
 $stmt->execute();
 while(true)
 {
    $rec=$stmt->fetch(PDO::FETCH_ASSOC);
        if($rec==false){ 
            break;
        }
        $can=" ";

        //日時絞り込み
        if($date_category=="Y"){
            $time=(string)$rec['start_time_of_use'];
        if(strpos($time,$date)===false){
            continue;
        }
        }

        //部屋絞り込み
        if($room_category=="Y"){
            $room_name=(string)$rec['room_name'];
        if(strpos($room_name,$room)===false){
            continue;
        }
        }

        //キャンセル済み絞り込み
        if($cancel=="N"){
            if($rec['cancel_flag']=="C"){
            continue;
            }
        }elseif($cancel=="A"){
            if($rec['cancel_flag']!="C"){
                continue;
                }
        }
        

    $start=(string)$rec['start_time_of_use'];
       $start=mb_substr($start,0,16);
       $end=(string)$rec['end_time_of_use'];
       $end=mb_substr($end,0,16);
    if($rec['cancel_flag']=="C"){
        $can="キャンセル済み";
    }
    print '<tr>';
        print '<td class="histd">';print $rec['room_name'];print "</td>";
        print '<td class="histd">';print $rec['number_of_user'];print "</td>";
        print '<td class="histd">';print $start; print "</td>"; 
        print '<td class="histd">';print $end;print "</td>"; 
        print '<td class="histd">';print $rec['sum_of_price'];print "</td>";
        print '<td class="histd">';print $rec['remark'];print "</td>"; 
        print '<td class="histd">';print $can;print "</td>";   
        print "</tr>";   
    }
}catch(Exception $e){
    print $e;
    print 'error!';
}
?>
</table>
</body>
</html>