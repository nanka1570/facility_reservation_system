<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="gamenn_2.css">
<title>画面表示</title>
</head>
<body>
<form method="post" action="gamennseni.php">
<button>
<?php
include_once "../../common/connect.php";
include_once "../../common/session.php";

$datenow="2025-11-12 21:30";//date('Y-m-d H:i');
$compMonthDate="20251112";// date("Ymd");//比較用年月日
$dateH = 21;//date("H");//現在時刻(時間)
$datei = 30;//date("i");//現在時刻(分)
$dateToday ="2025-11-12";// date("Y-m-d");
$newDateTime=new Datetime($datenow);
$dT="2025-11-12 21:30";//$newDateTime->format("Y-m-d H:i");
$dateTodaynext=$newDateTime->modify("+6 hour");
$dTnext=$dateTodaynext->format("Y-m-d H:i");
$tdnum = array(array(),array());//$tdnum宣言
$time_data_cnt = 0;//時間選択カウント用の変数
$unit_time=30;//時間単位(施設テーブルのtime_of_unit)1分=1
/*$_SESSION['room_ctg'] = $_POST['room_ctg'];
//var_dump($_SESSION['room_ctg']);
if($_POST['room_ctg']!=$_SESSION['room_ctg']){
    $_SESSION['room_ctg'] = $_POST['room_ctg'];
}*/
if(isset($_POST['check'])){
    $room_name[]=$_POST['check'];
}else{//部屋を選択していない場合戻る
    print'<meta http-equiv="refresh" content="0;gamennseni.php">';
}
//選択した部屋の数
$room_cnt = count($_POST['check']);
//1ページの最大部屋数
$maxroom= 3;
// 総ページ数を計算
$totalPages = ceil($room_cnt / $maxroom);
if(isset($_SESSION['page'])){
    if($_SESSION['page']<$totalPages){
        $_SESSION['page']++;
    }else{
        $_SESSION['page']=1;
    }
}else{
    $_SESSION['page']=1;
}
$page=$_SESSION['page'];
print '<p class="time">現在時刻：'.$datenow.'</p>';
?>
<table>
    <tr class=title>
        <th scope="col">予約時刻</th>
        <?php
        $forstart=($page-1)*$maxroom;
        if($room_cnt<=$maxroom){//選択した部屋数がmaxroom以下のとき
            $forend=$room_cnt;
        }else{
            if($page*$maxroom<=$room_cnt){//maxroom以上でmaxroomで割り切れる
                $forend=$page*$maxroom;
            }else{//割り切れない
                $forend=($page-1)*$maxroom+($room_cnt%$maxroom);
            }
        }

        for($i = $forstart; $i < $forend; $i++){
            $name=$_POST['check'][$i];
            try{
                $dbh=$conn;
                $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        
                $sql="SELECT room_number FROM facility_table
                      WHERE room_name='".$name."'";
                $stmt=$dbh->prepare($sql);
                $stmt->execute();
        
                    while(true)
                    {
                        $rec=$stmt->fetch(PDO::FETCH_ASSOC);
                        if($rec==false)
                        {
                            break;
                        }
                        $room_number[]=$rec["room_number"];
                        print'<th scope="col">'.$name.'</th>';//部屋名表記
                    }
                }
                catch(Exception $e){
                print $e;
                print'error!';
                exit();
            }
        }
        ?>
    </tr>

<?php
$k=0;
for($n=$dateH;$n<$dateH+6*(60/$unit_time);$n++){//時間単位に合わせた6時間の表を生成
    $j=$n;
    if($j>25){
        $j=$j-24;
    }
    $tableh_l= sprintf('%02d',$j-floor($k/(60/$unit_time)));//hh:
    $tablei_l= sprintf('%02d',($j*$unit_time)%60);//:ii~
    $tableh_r= sprintf('%02d',($j+1)-floor(($k+1)/(60/$unit_time)));//~hh:
    $tablei_r= sprintf('%02d',(($j+1)*$unit_time)%60);//:ii
    print"<tr>"; 
            print"<th class=retime>";
                //時間表示の左側
                print $tableh_l.":".$tablei_l."~";
                $tbleft=$tableh_l.$tablei_l;
                //時間表示の右側
                print $tableh_r.":".$tablei_r;
                $tbright=$tableh_r.$tablei_r;
                $start_time = $tableh_l.":".$tablei_l;
                $end_time=$tableh_r.":".$tablei_r;
                $select_time_start[] = $start_time;
                $select_time_end[] = $end_time;
                $time_data_cnt++;
            print "</th>";
        
    for($room_reserv=0;$room_reserv<count($room_number);$room_reserv++){
        $set_room_number = $room_number[$room_reserv];

        //予約時間を取得
        $sql="select DISTINCT start_time_of_use,end_time_of_use,reservation_number
              from reservation_table AS R,facility_table AS F
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
            $rnum[]=$data['reservation_number'];
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
            }else{//前日に予約を跨ぐ予約
                $tdclass="reserved";
                $tdnum[0][$room_reserv]="reserved";
                $stou[]="";
            }
            $picketou=new DateTime($data["end_time_of_use"]);//$etou=利用終了時間
            $etou[]=$picketou->format('Hi');
        }
        //予約されているこまは
        
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
                print "<td class=$tdclass>〇</td>";
            }
        }else{//前のコマの$tbclassがnot
            if(in_array($tbleft,$stou)){//このコマが利用開始
                $tdclass="reserved";
                $tdnum[$k+1][$room_reserv]="reserved";
                print "<td class=$tdclass>×</td>";
            }else{
                $tdclass="not";
                $tdnum[$k+1][$room_reserv]="not";
                print "<td class=$tdclass>〇";
                print "</td>";
            }
        }
    }//for終了
    print "</tr>";
    $k=$k+1;
}

?>
</table>

</button>
 <!-- <input type="button" onclick=location.href="gamennseni.php" value="戻る"> -->
</form>

<script>
//ページ更新
setTimeout(function () {
    location.reload();
}, 5000);//数字は更新の時間間隔(ミリ秒)
</script>
</body>
</html>