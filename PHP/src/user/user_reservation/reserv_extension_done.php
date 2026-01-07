<?php
include_once "../../common/connect.php";
include_once "../../common/session.php";
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<link rel="stylesheet" href="reservation.css">
</head>
<body>
    <?php
        //var_dump($_POST);
        //print"<br>";
        $reservation_number = $_POST['reservation_number'];
        $user_id = $_POST['user_id'];
        $room_number = $_POST['room_number'];
        $start_time_of_use = $_POST['start_time_of_use'];
        $end_time_of_use = $_POST['end_time_of_use'];

        $endtime = new DateTime($end_time_of_use);

        //switch文で延長時間によって分加算を分岐
        switch($_POST['selected_extension_minute']){
            case '15':
                $endtime->modify('+15 minutes');
                $endtime = $endtime->format('Y-m-d H:i:s');
                break;
            case '30':
                $endtime->modify('+30 minutes');
                $endtime = $endtime->format('Y-m-d H:i:s');
                break;
            case '45':
                $endtime->modify('+45 minutes');
                $endtime = $endtime->format('Y-m-d H:i:s');
                break;
            case '60':
                $endtime->modify('+60 minutes');
                $endtime = $endtime->format('Y-m-d H:i:s');
                break;
        }
        $sql="SELECT reservation_number
                    FROM reservation_table
                    WHERE ('$start_time_of_use'<start_time_of_use  and '$endtime'>start_time_of_use) and 
                    cancel_flag='' and
                    room_number='$room_number' ";           
                /*$sql="SELECT reservation_number
                    FROM reservation_table
                    WHERE(('$r_starttime'>start_time_of_use  and '$r_starttime'<end_time_of_use) or
                    ('$r_endtime'>start_time_of_use and '$r_endtime'<end_time_of_use)or
                    ('$r_starttime'<start_time_of_use and '$r_endtime'>end_time_of_use)) and 
                    cancel_flag='' and
                    room_number='$room_number' ";*/
                    $dbh = $conn; 
                    $stmt = $dbh->prepare($sql);
                $stmt->execute();
                $rec=$stmt->fetch(PDO::FETCH_ASSOC);
                if($rec==true)
                {
                    print '<p class="check">指定された時間帯では延長できません。</p>';
                    //echo $e->get_message();
                }
                else{
        $dbh = $conn; 
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
        $sql = 'UPDATE reservation_table SET end_time_of_use = :end_time_of_use WHERE reservation_number = :reservation_number AND user_id = :user_id';

        $stmt = $dbh->prepare($sql);

        $stmt->bindParam(':reservation_number', $reservation_number);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':end_time_of_use', $endtime);

        $stmt -> execute();
        $dbh = null;
                
        
        //予約履歴も更新する
        $dbh = $conn; 
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
        $sql = 'SELECT room_name FROM facility_table WHERE room_number = ?';
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$room_number]);
        $rec = $stmt->fetch(PDO::FETCH_ASSOC);
        $room_name = $rec['room_name'];

        $sql = 'UPDATE history_table SET end_time_of_use = :end_time_of_use WHERE reservation_number = :reservation_number AND user_id = :user_id';

        $stmt = $dbh->prepare($sql);

        $stmt->bindParam(':reservation_number', $reservation_number);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':end_time_of_use', $endtime);

        $stmt -> execute();
        $dbh = null;
        
        print'<p class="check">予約時間を延長しました。</p>';
                }
        print'<form action="../../login/user_home.php">';
            print'<p class="buttons"><input type="submit" class="button" value="ホーム画面に戻る"></p>';
        print'</form>';
        print'<p class="check">もう一度時間を延長する場合はこちら</p>';
        print'<form action="reserv_extension_list.php">';
            print'<p class="buttons"><input type="submit" class="button" value="予約一覧画面へ"></p>';
        print'</form>';

    ?>
</body>
</html>