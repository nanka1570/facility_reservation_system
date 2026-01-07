<?php
include_once "../../common/DB_switch.php";
// include_once "../../common/connect.php";
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>予約表示</title>
<link rel="stylesheet" href="../../common/admin_basic.css">
<link rel="stylesheet" href="reservation.css">
</head>
<body>
<?php
include_once "../../common/session.php";
$compMonthDate= date("Ymd");//比較用年月日
$dateH = date("H");//現在時刻(時間)
$start_dateH = $dateH + 1;//★予約は一時間後から有効//
$dateToday = date("Y-m-d");
$datenow=date('Y-m-d H:i');//^^^^^^^^^^^^^^^^^^^^
$datei = date("i");//現在時刻(分)
$newDateTimeNow=new Datetime($datenow);//^^^^^^^^^^^^^^^
$newDateTimeTody=new Datetime($dateToday);//^^^^^^^^^^^^^^
$ndtn=$newDateTimeNow->modify("+1hour");//0129
$dT=$ndtn->format("Y-m-d H:i");//0129
$dateTodaynext=$newDateTimeTody->modify("+1day");//日付の加算0129
$dTnext=$dateTodaynext->format("Y-m-d");//0129

$tdnum = array(array(),array());//$tdnum宣言
$time_data_cnt = 0;//時間選択カウント用の変数

//部屋の情報取得
$unit_time=30;//時間単位(施設テーブルのtime_of_unit)1分=1
$_SESSION['room_ctg'] = $_POST['room_ctg'];
//var_dump($_SESSION['room_ctg']);
if($_POST['room_ctg']!=$_SESSION['room_ctg']){
    $_SESSION['room_ctg'] = $_POST['room_ctg'];
}
$reservation_number = $_POST['reservation_number'];//
print $dateToday;
print "<br>";
?>
<table>
    <tr>
        <th scope="col">予約時刻</th>
        <?php
        $room_num = $_SESSION['room_ctg'];
        include "room_count.php";//部屋名と部屋の数を取得
        //$room_cnt = 1;
        for($i = 0; $i < $room_cnt; $i++){
            print'<th scope="col">';print"{$room_name[$i]}";//部屋名表記
            //print $room_name[$i];
            print'</th>';
        }
        ?>
    </tr>
    <?php
//(60/$unit_time)は1時間のコマ数^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^0129↓
$d_u=floor($datei/$unit_time);//30分未満なら0、30分以上なら1
$k=0;//k足してない、上は非表示
for($n=$start_dateH+$d_u;$n<$dateH+(24-$start_dateH)*(60/$unit_time);$n++){//時間単位に合わせた24時間の表を生成
    $tableh_l= sprintf('%02d',$n-floor(($k+$d_u)/(60/$unit_time)));//hh:
    $tablei_l= sprintf('%02d',($d_u+1+($k%2))*$unit_time%60);//:ii~
    $tableh_r= sprintf('%02d',$n+1-floor(($k+$d_u+1)/(60/$unit_time)));//~hh:
    $tablei_r= sprintf('%02d',($d_u+($k%2))*$unit_time%60);//:ii
    //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^0129↑
    $check_date=$tableh_l.$tablei_l;///////////////////////
    print"<tr>";    
        print"<th>";
            //時間表示の左側
            print $tableh_l.":".$tablei_l."~";
            $tbleft=$tableh_l.$tablei_l;
            //時間表示の右側
            print $tableh_r.":".$tablei_r;
            $start_time = $tableh_l.":".$tablei_l;
            $end_time=$tableh_r.":".$tablei_r;
            $select_time_start[] = $start_time;
            $select_time_end[] = $end_time;
            $time_data_cnt++;
        print "</th>";
        
        for($room_reserv=0;$room_reserv<$room_cnt;$room_reserv++){
            $set_room_number = $room_number[$room_reserv];
            //予約時間を取得
            $sql="select DISTINCT start_time_of_use,end_time_of_use from reservation_table AS R,facility_table AS F
                WHERE (('$dT'< R.start_time_of_use AND R.start_time_of_use<'$dTnext') 
                OR ('$dT'< R.end_time_of_use AND R.end_time_of_use<'$dTnext')) 
                AND F.room_number=$set_room_number
                AND R.room_number=$set_room_number
                AND R.cancel_flag=''";
            $stmt=$conn->prepare($sql);
            $stmt->execute();
            $stou = array();
            $etou = array();
            $tdnum[0][$room_reserv]="not";
            while(true)
            {
                $data=$stmt->fetch(PDO::FETCH_ASSOC);
                if($data==false){
                    break;
                }
                $pickstYmd=new DateTime($data["start_time_of_use"]);//利用開始年月日
                $compstYmd=$pickstYmd->format('Ymd');
                $pickendYmd=new DateTime($data["end_time_of_use"]);//利用終了年月日
                $compendYmd=$pickendYmd->format('Ymd');

                $pickstou=new DateTime($data["start_time_of_use"]);//$stou=利用開始時間
                //$stou[]=$pickstou->format('Hi');
                //日付を跨ぐ予約があるかの判定
                if($pickstYmd>$ndtn){//予約を跨がない予約(利用開始年月日が選択した日付と異なる)
                    $tdclass="not";
                    //$tdnum[0][$room_reserv]="not";
                    $stou[]=$pickstou->format('Hi');
                }else{//予約を跨ぐ予約
                    $tdclass="reserved";
                    $tdnum[0][$room_reserv]="reserved";
                    $stou[]="";
                }
                $picketou=new DateTime($data["end_time_of_use"]);//$etou=利用終了時間
                //$etou[]=$picketou->format('Hi');//日付を跨ぐ予約があるかの判定
                if($compMonthDate==$compendYmd){//予約を跨がない予約(利用終了年月日が選択した日付と異なる)
                    $etou[]=$picketou->format('Hi');
                }else{//予約を跨ぐ予約
                    $etou[]="";
                }
            }
            //var_dump($tdnum);^^^^^^^^^^tdnum[]の中をnからkに----0129
            //予約されているこまは
            //var_dump($stou);
            if($tdnum[$k][$room_reserv]=="reserved"){//前のコマの$tbclassがreserved
                if(in_array($tbleft,$etou)==false){
                    $tdclass="reserved";//このコマが利用終了ではない
                    $tdnum[$k+1][$room_reserv]="reserved";
                    print "<td class=$tdclass>×</td>";
                }else if(in_array($tbleft,$stou)==true){
                    $tdclass="reserved";//このコマが利用開始
                    $tdnum[$k+1][$room_reserv]="reserved";
                    print "<td class=$tdclass>×</td>";
                }else{
                    $tdclass="not";//このコマが利用終了
                    $tdnum[$k+1][$room_reserv]="not";
                    //print "<td class=$tdclass>×</td>";
                    print "<td class=$tdclass>";
                        print'<form method="post" action="admin_reservation_edit_03.php">';
                            //print'<input type="hidden" name="room_num" value='."{$room_num}";print'>';
                            print'<input type="hidden" name="name" value='."{$room_name[$room_reserv]}";print'>';
                            print'<input type="hidden" name="start" value='."{$start_time}";print'>';
                            print'<input type="hidden" name="user_id" value="'.$_POST['user_id'].'">';//
                            print'<input type="hidden" name="reservation_number" value="'.$reservation_number.'">';//
                            print'<input type="submit" class="reserv-button" value="時間変更">';
                        print'</form>';
                    print "</td>";
                }
            }else{//前のコマの$tbclassがnot
                if(in_array($tbleft,$stou)){//このコマが利用開始
                    $tdclass="reserved";
                    $tdnum[$k+1][$room_reserv]="reserved";
                    print "<td class=$tdclass>×</td>";
                }else{
                    $tdclass="not";
                    $tdnum[$k+1][$room_reserv]="not";
                    print "<td class=$tdclass>";
                        print'<form method="post" action="admin_reservation_edit_03.php">';
                            //print'<input type="hidden" name="room_num" value='."{$room_num}";print'>';
                            print'<input type="hidden" name="name" value='."{$room_name[$room_reserv]}";print'>';
                            print'<input type="hidden" name="start" value='."{$start_time}";print'>';
                            print'<input type="hidden" name="user_id" value="'.$_POST['user_id'].'">';//
                            print'<input type="hidden" name="reservation_number" value="'.$reservation_number.'">';//
                            print'<input type="submit" class="reserv-button" value="時間変更">';
                        print'</form>';
                    print "</td>";
                }
            }      
                        //print "<td class=$tdclass>".$tableh_l.":".$tablei_l."</td>";
        }//for終了
        print "</tr>";
        $k++;
                //}
}
?>
</table>
<?php
 print '<section class="section_css">';

    print'<form method="post" action="admin_reservation_edit_done_check.php">';//
    print'<p>利用者名（ユーザー名）を入力してください</p>';
        print'<p><input type="text" name="one_time_user_id" value="'.$_POST['user_id'].'"></p>';//
    print'<p>使用する部屋を選択してください</p>';
        print'<select name="room_name">';
        for($name_cnt=0;$name_cnt<$room_cnt;$name_cnt++){
            print'<option>';
                print"$room_name[$name_cnt]";
            print'</option>';
        }
        print'</select>';
    print'<p>利用人数を入力してください</p>';
            print'<p><input type="text" name="number_of_user">人</p>';
    print'<p>予約時間を選択してください</p>';
            print'<input type="hidden" name="reserve_day" value="'.$dateToday;print'">';
            print'<select name="start_time">';
                for($j = 0; $j < $time_data_cnt; $j++){
                    print'<option>';
                    print"{$select_time_start[$j]}";
                    print'</option>';
                }
        print'</select>';
    print'~';
        print'<select name="end_time">';
            for($j = 0; $j < $time_data_cnt; $j++){
                print'<option>';
                if($j <$time_data_cnt)
                    print"{$select_time_end[$j]}";
                else
                    print'</option>';
            }
        print'</select>';
    print'<p>貸出備品</p>';
        $dbh = $conn; 
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
        $sql='SELECT * FROM item_table';
        $stmt = $dbh->prepare($sql);                
        $stmt->execute();
        $rec = $stmt->fetch(PDO::FETCH_ASSOC);
        $i=0;

        while($rec == true)
        {
            print '<input type="checkbox" name="item_name['.$i.']" value="'.$rec['item_name'].'">'.$rec['item_name'];//
            print '<p><input type="text" class="item_css" name="item_pieces['.$i.']" placeholder="個数">個</p><br>';//

            $rec = $stmt->fetch(PDO::FETCH_ASSOC);
            $i++;
        }
        print'<input type="hidden" name="item_cnt" value="'.$i.'">';
    print'<p>備考欄</p>';
        print'<p><input type="text" name="remark"></p>';
        print'<input type="hidden" name="user_id" value="'.$_POST['user_id'].'">';//
        print'<input type="hidden" name="reservation_number" value="'.$reservation_number.'">';//
        print'<p><input type="submit" class="button_css" value="予約変更確認画面へ"></p>';
           
    print'</form>';

    print '</section>';

    print'<p>明日以降の予約は日付を選択してこちら</p>';
    print'<form name="form1" method="post" action="admin_reservation_edit_02.php">';
        print'<input type="hidden" name="room_ctg" value='."{$room_num}";print'>';
        print'<input type="hidden" name="user_id" value="'.$_POST['user_id'].'">';//
        print'<input type="hidden" name="reservation_number" value="'.$reservation_number.'">';//
        print'<input name="date" type="date" min="'.$dateToday; print'"/>';
        print'<input type="submit" class="button_css" value="別の日付へ">';
        print'</form>';
        print'<form method="post" action="admin_reservation_edit_stop.php">';//
            print'<input type="hidden" name="reservation_number" value="'.$reservation_number.'">';//
            print'<input type="submit" class="button_css" value="予約変更を中止する">';//
        print'</form>';
?>
</body>
</html>