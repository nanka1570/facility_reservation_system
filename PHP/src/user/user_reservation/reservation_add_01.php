<?php
include_once "../../common/connect.php";
//include "session.php";
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>予約表示</title>
<link rel="stylesheet" href="../../common/user_basic.css">
<link rel="stylesheet" href="reservation.css">
</head>
<body>
<?php
include_once "../../common/session.php";
$compMonthDate= date("Ymd");//比較用年月日
$dateH = date("H");//現在時刻(時間)
$start_dateH = $dateH + 1;//★予約は一時間後から有効//
$dateToday = date("Y-m-d");
$dateNownext=$dateH+1;//日付の加算/////////////////
$datei = date("i");//現在時刻(分)
$dateNow_check = $dateNownext.$datei;///////////////
$newDateTime=new Datetime($dateToday);//
$dT=$newDateTime->format("Y-m-d H:i");//
$dateTodaynext=$newDateTime->modify("+1day");//日付の加算
$dTnext=$dateTodaynext->format("Y-m-d H:i");//
$tdnum = array(array(),array());//$tdnum宣言
$time_data_cnt = 0;//時間選択カウント用の変数

//部屋の情報取得
$unit_time=30;//時間単位(施設テーブルのtime_of_unit)1分=1
$_SESSION['room_ctg'] = $_POST['room_ctg'];
//var_dump($_SESSION['room_ctg']);
if($_POST['room_ctg']!=$_SESSION['room_ctg']){
    $_SESSION['room_ctg'] = $_POST['room_ctg'];
}

print $dateToday;
print "<br>";
?>
<table>
    <tr>
        <th scope="col">予約時刻</th>
        <?php
        $room_num = $_SESSION['room_ctg'];
        include_once "room_count.php";//部屋名と部屋の数を取得
        //$room_cnt = 1;
        for($i = 0; $i < $room_cnt; $i++){
            print'<th scope="col">';print"{$room_name[$i]}";//部屋名表記
            //print $room_name[$i];
            print'</th>';
        }
        ?>
    </tr>
<?php
for($n=0;$n<24*(60/$unit_time);$n++){//時間単位に合わせた24時間の表を生成
    //if($h+$n<=24)
    $tableh_l= sprintf('%02d',floor($n/(60/$unit_time)));//hh:
    $tablei_l= sprintf('%02d',($n*$unit_time)%60);//:ii~
    $tableh_r= sprintf('%02d',floor(($n+1)/(60/$unit_time)));//~hh:
    $tablei_r= sprintf('%02d',(($n+1)*$unit_time)%60);//:ii
    $check_date=$tableh_l.$tablei_l;///////////////////////
    print"<tr>";
        //if($tableh_l<=$start_dateH){
        if($check_date<$dateNow_check){
            $tbleft=$tableh_l.$tablei_l;
            //$tbleft = $tableh_l.":".$tablei_l;//
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
                    //var_dump($data);
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
                    if($compMonthDate==$compstYmd){//予約を跨がない予約(利用開始年月日が選択した日付と異なる)
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
        //var_dump($tdnum);
                    //予約されているこまは
                    //var_dump($stou);
                if($tdnum[$n][$room_reserv]=="reserved"){//前のコマの$tbclassがreserved
                    if(in_array($tbleft,$etou)==false){
                        $tdclass="reserved";//このコマが利用終了ではない
                        $tdnum[$n+1][$room_reserv]="reserved";
                        print "<td class=$tdclass>×</td>";
                    }else if(in_array($tbleft,$stou)==true){
                        $tdclass="reserved";//このコマが利用開始
                        $tdnum[$n+1][$room_reserv]="reserved";
                        print "<td class=$tdclass>×</td>";
                    }else{
                        $tdclass="not";//このコマが利用終了
                        $tdnum[$n+1][$room_reserv]="not";
                            //print "<td class=$tdclass>×</td>";
                            //print "<td class=$tdclass>";
                            //print'<form method="post" action="user_reservation_add_03.php">';
                            //print'<input type="hidden" name="room_num" value='."{$room_num}";print'>';
                            //print'<input type="hidden" name="name" value='."{$room_name[$room_reserv]}";print'>';
                            //print'<input type="hidden" name="start" value='."{$start_time}";print'>';
                            //print'<input type="submit" value="〇">';
                            //print'</form>';
                            //print "</td>";
                        }
                }else{//前のコマの$tbclassがnot
                    if(in_array($tbleft,$stou)){//このコマが利用開始
                        $tdclass="reserved";
                        $tdnum[$n+1][$room_reserv]="reserved";
                        //print "<td class=$tdclass>×</td>";
                    }else{
                        $tdclass="not";
                        $tdnum[$n+1][$room_reserv]="not";
                        //print "<td class=$tdclass>";
                            //print'<form method="post" action="admin_reservation_add_03.php">';
                            //print'<input type="hidden" name="room_num" value='."{$room_num}";print'>';
                            //print'<input type="hidden" name="name" value='."{$room_name[$room_reserv]}";print'>';
                            //print'<input type="hidden" name="start" value='."{$tbleft}";print'>';
                            //print'<input type="submit" value="〇">';
                        //print'</form>';
                        //print "</td>";
                    }
                }
            }
        }
        else{    
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
                $tdnum[0][$room_reserv]="not";/////////////////////修正
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
                    if($compMonthDate==$compstYmd){//予約を跨がない予約(利用開始年月日が選択した日付と異なる)
                        $tdclass="not";
                        //$tdnum[0][$room_reserv]="not";/////////////修正
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
                //var_dump($tdnum);
                //予約されているこまは
                //var_dump($stou);
                if($tdnum[$n][$room_reserv]=="reserved"){//前のコマの$tbclassがreserved
                    if(in_array($tbleft,$etou)==false){
                        $tdclass="reserved";//このコマが利用終了ではない
                        $tdnum[$n+1][$room_reserv]="reserved";
                        print "<td class=$tdclass>×</td>";
                    }else if(in_array($tbleft,$stou)==true){
                        $tdclass="reserved";//このコマが利用開始
                        $tdnum[$n+1][$room_reserv]="reserved";
                        print "<td class=$tdclass>×</td>";
                    }else{
                        $tdclass="not";//このコマが利用終了
                        $tdnum[$n+1][$room_reserv]="not";
                        //print "<td class=$tdclass>×</td>";
                        print "<td class=$tdclass>";
                        print'<form method="post" action="reservation_add_03.php">';
                        //print'<input type="hidden" name="room_num" value='."{$room_num}";print'>';
                        print'<input type="hidden" name="name" value='."{$room_name[$room_reserv]}";print'>';
                        print'<input type="hidden" name="start" value='."{$start_time}";print'>';
                        print '<class="buttoncolor">';
                        print'<input type="submit" class="buttoncolor" value="〇">';
                        print '</buttoncolor>';
                        print'</form>';
                        print "</td>";
                    }
                }else{//前のコマの$tbclassがnot
                    if(in_array($tbleft,$stou)){//このコマが利用開始
                        $tdclass="reserved";
                        $tdnum[$n+1][$room_reserv]="reserved";
                        print "<td class=$tdclass>×</td>";
                    }else{
                        $tdclass="not";
                        $tdnum[$n+1][$room_reserv]="not";
                        print "<td class=$tdclass>";
                            print'<form method="post" action="reservation_add_03.php">';
                            //print'<input type="hidden" name="room_num" value='."{$room_num}";print'>';
                            print'<input type="hidden" name="name" value='."{$room_name[$room_reserv]}";print'>';
                            print'<input type="hidden" name="start" value='."{$start_time}";print'>';
                            print '<class="buttoncolor">';
                            print'<input type="submit" class="buttoncolor" value="〇">';
                            print '</buttoncolor>';
                        print'</form>';
                        print "</td>";
                    }
                }        
                //print "<td class=$tdclass>".$tableh_l.":".$tablei_l."</td>";
            }//for終了
            print "</tr>";
        }
}
?>
</table>
<?php
    print'<p class="font">使用する部屋を選択してください</p>';
    print'<form method="post" action="reservation_check.php">';
        print'<select name="room_name">';
            for($name_cnt=0;$name_cnt<$room_cnt;$name_cnt++){
                print'<option>';
                    print"$room_name[$name_cnt]";
                print'</option>';
            }
        print'</select>';
    print'<p class="font">利用人数を入力してください</p>';
        print'<p class="font"><input type="text" name="number_of_user">人</p>';
    print'<p class="font">予約時間を選択してください</p>';
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
    print'<p class="font">貸出備品</p>';
        $dbh = $conn; 
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
        $sql='SELECT * FROM item_table';
        $stmt = $dbh->prepare($sql); 
                
        $stmt->execute();
        $rec = $stmt->fetch(PDO::FETCH_ASSOC);
        $i=0;
        print '<table class="bihin">';
        while($rec == true)
        {
            print '<td><input type="checkbox" name="item_name['.$i.']" value="'.$rec['item_name'].'"></td>';
            print'<td>"'.$rec['item_name'].'"</td>';//
            print '<td><input type="text" name="item_pieces['.$i.']" placeholder="個数"></td>';
            print '<td>個</td>';//
            print '</tr>';

            $rec = $stmt->fetch(PDO::FETCH_ASSOC);
            $i++;
        }
        print '</table>';
        print'<input type="hidden" name="item_cnt" value="'.$i.'">';
    print'<p class="font">備考欄</p>';
        print'<p><input type="text" class="bikou" name="remark"></p>';
        print'<p><input type="submit" class="button" value="予約確認画面へ"></p>';
           
    print'</form>';

    print'<p class="font">明日以降の予約は日付を選択してこちら</p>';
    print'<form name="form1" method="post" action="reservation_add_02.php">';
        print'<input type="hidden" name="room_ctg" value='."{$room_num}";print'>';
        print'<input name="date" class="date" type="date" min="'.$dateToday; print'"/>';
        print'<input type="submit" class="button" value="別の日付へ">';
    print'</form>';

    print'<p class="font">部屋選択画面に戻る</p>';
    print'<form action="room.php">';
        print'<input type="submit" class="button" value="部屋選択画面に戻る">';
    print'</form>';

    print'<p class="font">ホーム画面に戻る</p>';
    print'<form action="../../login/user_home.php">';
        print'<input type="submit" class="button" value="ホーム画面に戻る">';
    print'</form>';
?>
</body>
</html>