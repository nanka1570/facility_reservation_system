<?php
include_once "../../common/DB_switch.php";
// include_once "../../common/connect.php";
include_once "../../common/session.php";
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title></title>
        <meta name="" content="">
    </head>
    <body>
        <?php
        try{
            //var_dump($_POST);
            $reservation_number = $_POST['reservation_number'];//
            $one_time_user_id = $_POST['one_time_user_id'];//
            $room_ctg = $_SESSION['room_ctg'];
            $room_name = $_POST['room_name'];//
            $number_of_user = $_POST['number_of_user'];//
            $reserve_day = $_POST['reserve_day'];
            $start_time = $_POST['start_time'];
            $end_time = $_POST['end_time'];
            $remark = $_POST['remark'];//
            //$item_cnt = $_POST['item_cnt'];//
            $item_cnt_sort = $_POST['item_cnt'];//備品


            $item_names = isset($_POST['item_name']) ? $_POST['item_name'] : [];
            $item_pieces = isset($_POST['item_pieces']) ? $_POST['item_pieces'] : [];
            //var_dump($item_names);

            $item_cnt = count($item_names);//備品

            //備品の配列を整形する
            $item_name_data = array_merge($item_names);
            //var_dump($item_name_data);
            //print"<br>";

            if($item_pieces!=null){
                $item_pieces_data = [];
                for($data_sort = 0; $data_sort < $item_cnt_sort; $data_sort++){
                    if($item_pieces[$data_sort] == null){
                        continue;
                    }
                    $item_pieces_data[] = $item_pieces[$data_sort]; 
                }
                //var_dump($item_pieces_data);
                //print"<br>";
            }


            //貸し出し備品がある場合、備品名から番号を取得
            if($item_names!=null){
                $db_item_number=[];
                $dbh = $conn;
                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                for($dbcontinue = 0; $dbcontinue < $item_cnt; $dbcontinue++){
                    $sql='SELECT item_number FROM item_table WHERE item_name = :item_name';
                    $stmt=$dbh->prepare($sql);
                    $stmt->bindParam(':item_name', $item_name_data[$dbcontinue]);
                    $stmt->execute();
                    $rec = $stmt->fetch(PDO::FETCH_ASSOC);
                    //var_dump($rec);
                    //print"<br>";
                    $db_item_number[] = $rec['item_number'];
                }
                $dbh=null;
                //var_dump($db_item_number);
                //print"<br>";
            }            

            $filleditem_pieces = array_filter($item_pieces,function($value)
            {
                return $value !== '' && $value !== null && $value !== false;
            });
            //$filleditem_pieces = array_diff($item_pieces,"",null,false,0);

            $dbh = $conn; 
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
            
            $sql='SELECT item_number,item_name,MAX(total_of_item) AS max_total_of_item 
                  FROM item_table 
                  GROUP BY item_number ,item_name 
                  ORDER BY max_total_of_item DESC';
            $stmt=$dbh->prepare($sql);
            $stmt->execute();

            $item_max = [];
            
            while($rec = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $item_max[$rec['item_name']] = $rec['max_total_of_item'];
                //var_dump($rec);
                //echo '<br><br><br>';
                $rec = $stmt->fetch(PDO::FETCH_ASSOC);
                break;
            }

            /*print $_SESSION['user_Id'];//
            print'<br/>';
            print $one_time_user_id;//
            print'<br/>';
            print $room_ctg;
            print'<br/>';
            print $room_name;
            print'<br/>';
            print $number_of_user;
            print'<br/>';
            print $reserve_day.$start_time;*/
            //$r_starttime = "2025-01-02 12:00:00.000";
            $r_starttime = $reserve_day." ".$start_time;//
            /*print'<br/>';
            print $reserve_day.$end_time;
            print'<br/>';*/
            //$r_endtime = "2025-01-02 16:00:00.000";
            if($end_time=="24:00"){
                $end_time = "00:00";
                $newDateTime=new Datetime($reserve_day);//
                $next_reserve_day = $newDateTime->modify("+1 day");//
                $dTnext=$next_reserve_day->format("Y-m-d");//
                $r_endtime = $dTnext." ".$end_time;
                //print $r_endtime;
            }
            else{
                $r_endtime = $reserve_day." ".$end_time;//
            }

            //$r_starttime = new DateTime($r_starttime);
            //$r_endtime = new DateTime($r_endtime);
            //$r_endtime = $reserve_day." ".$end_time;//
            //print'<br/>';
            $sql = 'select room_number from facility_table where room_name = ?';
            $stmt = $conn->prepare($sql);
            $stmt->execute([$room_name]);
            $rec=$stmt->fetch();
            $room_number = $rec['room_number'];//
            //print $room_number;
            $sql = 'select max_number_of_people from facility_table where room_number = ?';
            $stmt = $conn->prepare($sql);
            $stmt->execute([$room_number]);
            $rec=$stmt->fetch();
            $max_people = $rec['max_number_of_people'];
            //print $max_people;

            ////////////////////////////////////////////////////////////////
            $check_user_id_null = "true";
            $check_user_id_cnt = "true";
            $check_numeric = "true";
            $check_max_people = "true";
            $check_item_max = "true";
            $check_time = "true";
            ////////////////////////////////////////////////////////////////

            if($one_time_user_id == null){
                print'<p>利用者名（ユーザー名）を入力してください。</p>';
                print'<form method="post" action="admin_reservation_edit_01.php">';
                    print'<input type="hidden" name="room_ctg" value="'.$room_ctg.'">';
                    print'<input type="hidden" name="user_id" value="'.$one_time_user_id.'">';
                    print'<input type="hidden" name="reservation_number" value="'.$reservation_number.'">';
                    print'<input type="submit" value="予約画面に戻る">';
                print'</form>';
                print'<form method="post" action="admin_reservation_edit_stop.php">';
                    print'<input type="hidden" name="reservation_number" value="'.$reservation_number.'">';
                    print'<input type="submit" value="変更を中止する">';
                print'</form>';
                $check_user_id_null = "false";
            }

            if($check_user_id_null == "true"){
                if(mb_strlen($one_time_user_id) > 30){
                    print'<p>利用者名（ユーザーID）は30文字以内で入力してください。</p>';
                    print'<form method="post" action="admin_reservation_edit_01.php">';
                        print'<input type="hidden" name="room_ctg" value="'.$room_ctg.'">';
                        print'<input type="hidden" name="user_id" value="'.$one_time_user_id.'">';
                        print'<input type="hidden" name="reservation_number" value="'.$reservation_number.'">';
                        print'<input type="submit" value="予約画面に戻る">';
                    print'</form>';
                    print'<form method="post" action="admin_reservation_edit_stop.php">';
                        print'<input type="hidden" name="reservation_number" value="'.$reservation_number.'">';
                        print'<input type="submit" value="変更を中止する">';
                    print'</form>';
                    $check_user_id_cnt = "false";                
                }   
            }

            if($check_user_id_null == "true" && $check_user_id_cnt=="true"){
                if(!is_numeric($number_of_user)){
                    print'<p>予約人数を半角数字で入力してください。</p>';
                    print'<form method="post" action="admin_reservation_edit_01.php">';
                        print'<input type="hidden" name="room_ctg" value="'.$room_ctg.'">';
                        print'<input type="hidden" name="user_id" value="'.$one_time_user_id.'">';
                        print'<input type="hidden" name="reservation_number" value="'.$reservation_number.'">';
                        print'<input type="submit" value="予約画面に戻る">';
                    print'</form>';
                    print'<form method="post" action="admin_reservation_edit_stop.php">';
                        print'<input type="hidden" name="reservation_number" value="'.$reservation_number.'">';
                        print'<input type="submit" value="変更を中止する">';
                    print'</form>';
                    $check_numeric = "false";                  
                }
            }

            if($check_user_id_null == "true" && $check_user_id_cnt=="true" && $check_numeric=="true"){
                if($number_of_user > $max_people){
                    print'<p>予約人数が適切ではありません。</p>';
                    print'<form method="post" action="admin_reservation_edit_01.php">';
                        print'<input type="hidden" name="room_ctg" value="'.$room_ctg.'">';
                        print'<input type="hidden" name="user_id" value="'.$one_time_user_id.'">';
                        print'<input type="hidden" name="reservation_number" value="'.$reservation_number.'">';
                        print'<input type="submit" value="予約画面に戻る">';
                    print'</form>';
                    print'<form method="post" action="admin_reservation_edit_stop.php">';
                        print'<input type="hidden" name="reservation_number" value="'.$reservation_number.'">';
                        print'<input type="submit" value="変更を中止する">';
                    print'</form>';
                    $check_max_people = "false";
                }
            }


            if($check_user_id_null == "true" && $check_user_id_cnt=="true" && $check_numeric=="true" && $check_max_people=="true"){
                $reserve_num = 0;
                $item_cnt_check = "true";
                if($item_name_data!=null){
                    $item_display_cnt = 0;
                    for($cnt_data=0;$cnt_data<$item_cnt;$cnt_data++){
                        $sql = 'select total_of_item from item_table where item_name = ?';
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$item_name_data[$cnt_data]]);
                        $rec=$stmt->fetch();
                        if($item_pieces_data[$cnt_data] > $rec['total_of_item']){
                            $name[] = $item_name_data[$cnt_data];
                            $item_cnt_check = "false";
                            $item_display_cnt++;
                        }
                        else{
                            continue;
                        }
                    }
                }
                if($item_cnt_check == "false"){
                    for($item_display=0;$item_display<$item_display_cnt;$item_display++){
                        print '<p>備品の数量が最大値を超えています。：'.$name[$item_display].'</p>';
                    }
                    print'<form method="post" action="admin_reservation_add_01.php">';
                        print'<input type="hidden" name="room_ctg" value="'.$room_ctg.'">';
                        print'<input type="hidden" name="user_id" value="'.$one_time_user_id.'">';
                        print'<input type="hidden" name="reservation_number" value="'.$reservation_number.'">';
                        print'<input type="submit" value="予約画面に戻る">';
                    print'</form>';
                    print'<form method="post" action="admin_reservation_edit_stop.php">';
                        print'<input type="hidden" name="reservation_number" value="'.$reservation_number.'">';
                        print'<input type="submit" value="変更を中止する">';
                    print'</form>';
                    $check_item_max = "false";
                }   
            }

            if($check_user_id_null == "true" && $check_user_id_cnt == "true" && $check_numeric == "true" && $check_max_people == "true" && $check_item_max == "true"){
                if($r_starttime >= $r_endtime){
                    print'<p>予約時間が適切ではありません。</p>';
                    print'<form method="post" action="admin_reservation_edit_01.php">';
                        print'<input type="hidden" name="room_ctg" value="'.$room_ctg.'">';
                        print'<input type="hidden" name="user_id" value="'.$one_time_user_id.'">';
                        print'<input type="hidden" name="reservation_number" value="'.$reservation_number.'">';
                        print'<input type="submit" value="予約画面に戻る">';
                    print'</form>';
                    print'<form method="post" action="admin_reservation_edit_stop.php">';
                        print'<input type="hidden" name="reservation_number" value="'.$reservation_number.'">';
                        print'<input type="submit" value="変更を中止する">';
                    print'</form>';
                    $check_time = "false";
                }
            }           


            if($check_user_id_null == "true" && $check_user_id_cnt == "true" && $check_numeric == "true" && $check_max_people == "true" && $check_item_max == "true" && $check_time == "true"){
                $sql="SELECT reservation_number
                FROM reservation_table
                WHERE(('$r_starttime'=start_time_of_use)or
                ('$r_endtime'=end_time_of_use)or
                ('$r_starttime'>start_time_of_use  and '$r_starttime'<end_time_of_use) or
                ('$r_endtime'>start_time_of_use and '$r_endtime'<end_time_of_use)or
                ('$r_starttime'<start_time_of_use and '$r_endtime'>end_time_of_use)) and 
                cancel_flag='' and
                room_number='$room_number' ";

            /*$sql="SELECT reservation_number
                  FROM reservation_table
                  WHERE(('$r_starttime'>start_time_of_use  and '$r_starttime'<end_time_of_use) or
                  ('$r_endtime'>start_time_of_use and '$r_endtime'<end_time_of_use)or
                  ('$r_starttime'<start_time_of_use and '$r_endtime'>end_time_of_use)) and 
                  cancel_flag='' and
                  room_number='$room_number' ";*/

            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            $rec=$stmt->fetch(PDO::FETCH_ASSOC);
            $r_starttime = new DateTime($r_starttime);
            $r_endtime = new DateTime($r_endtime);
            if($rec==true)
            {
                print "指定された時間帯は既に予約されています。";
                print '<button type="button" onclick="history.back()">戻る</button>';
                //echo $e->get_message();
            }
            else
            {
                //print "予約可能です <br>";

                //部屋料金と貸出料金の合計金額
                $total_rental_price = 0;

                //貸し出す備品の料金を計算
                $sql ='SELECT * FROM extension_table WHERE change_extension_date =
                                                    (SELECT MAX(change_extension_date) FROM extension_table)';
                $stmt = $dbh->prepare($sql);
                $stmt->execute();
                $rec = $stmt->fetch(PDO::FETCH_ASSOC);
                $use_ex = $rec['use_extension'];
                $rental_ex = $rec['rental_flag'];
                $price_ex = $rec['price_flag'];
                $equip_ex = $rec['equipment_flag'];
                if($use_ex=='Y')
                {
                    if($rental_ex=='R')
                    {
                        var_dump($item_names);
                        var_dump($item_cnt);
                        for($i=0;$i<$item_cnt;$i++)
                        {
                            if(empty($item_names[$i])){
                                continue;
                            }
                            echo $item_names[$i];
                            echo $item_pieces[$i].'個';
                            echo '<br>';
                        }
                    }
                    if($price_ex=='P')
                    {
                        if (is_array($item_names) && is_array($item_pieces))
                        {
                            foreach ($item_names as $index => $item_name) 
                            {
                                if (!empty($item_name) && !empty($item_pieces[$index])) 
                                {
                                    // 備品番号と単価を取得し計算
                                    $sql_item = "SELECT rental_unit_price FROM item_table WHERE item_name = ?";
                                    $stmt = $dbh->prepare($sql_item);
                                    $stmt->execute([$item_name]);
                                    $item_data = $stmt->fetch(PDO::FETCH_ASSOC);
                                    $rental_price = $item_data['rental_unit_price'] * $item_pieces[$index];
                                    $item_price_sum[] = $rental_price;//備品
                                    $total_rental_price += $rental_price;
                                }
                            }
                        }

                        //施設の時間単位、単位あたりの料金を取得
                        $sql_facility = "SELECT time_of_unit,time_of_unit_price 
                                         FROM facility_table
                                         WHERE room_number = ?";
                        $stmt = $dbh->prepare($sql_facility);
                        $stmt->execute([$room_number]);
                        $facility_data = $stmt->fetch(PDO::FETCH_ASSOC);

                        //予約時間の差分を分単位で取得
                        $time_diff = $r_endtime->diff($r_starttime);
                        $total_minites = ($time_diff->days * 24 * 60) + ($time_diff->h * 60) + $time_diff->i;

                        //時間単位での料金計算
                        if($facility_data['time_of_unit']!=0){
                            $units = ceil($total_minites / $facility_data['time_of_unit']);
                            $facility_price = $units * $facility_data['time_of_unit_price'];
                        }
                        else{
                            $units = 0;
                            $facility_price = $units * $facility_data['time_of_unit_price'];
                        }

                        //部屋の料金を合算
                        $total_rental_price += $facility_price;
                    }
                    
                }
                    // 予約内容の表示
                    echo '<h2>この内容で予約しますか？</h2><br>';
                    if($one_time_user_id == null){
                        echo '<p>利用者名（ユーザー名）: 管理者</p>';
                    }
                    else{
                        echo '<p>利用者名（ユーザー名）: ' . htmlspecialchars($one_time_user_id) . '</p>';
                    }
                    //echo '<p>利用者名（ユーザー名）: ' . htmlspecialchars($one_time_user_id) . '</p>';
                    echo '<p>部屋名: ' . htmlspecialchars($room_name) . '</p>';
                    echo '<p>利用人数: ' . htmlspecialchars($number_of_user) . '人</p>';

                    
                    echo '<p>利用開始時間: ' . $r_starttime->format('Y-m-d H:i') . '</p>';
                    echo '<p>利用終了時間: ' . $r_endtime->format('Y-m-d H:i') . '</p>';

                    //貸出備品がある場合は表示
                    echo '<p>貸出備品</P>';
                    if($item_name_data!=null){
                        for($display = 0; $display < $item_cnt; $display++){
                            echo $item_name_data[$display].':'.$item_pieces_data[$display].'個<br><br>';
                        }
                    }
                    else{
                        echo '<p>なし</P>';
                    }

                    echo '<p>備考: ' . htmlspecialchars($remark) . '</p>';
                    ////////////////////////////////////////////////////////////////////////////////^^^^^^^^^^^^^^^^^^^^^^
                    if($use_ex == "Y"){
                        if($price_ex == "P"){
                            echo '<p>施設料金: ' . number_format($facility_price) . '円</p>';
                        }
                        if($rental_ex == "R"){
                            echo '<p>備品料金: ' . number_format($total_rental_price - $facility_price) . '円</p>';
                        }
                        if($price_ex == "P"){
                           echo '<p>合計料金: ' . number_format($total_rental_price) . '円</p>'; 
                        }
                    }
                    ////////////////////////////////////////////////////////////////////////////////^^^^^^^^^^^^^^^^^^^^^^
                    ?>
                    
                    <!-- 予約内容をreservation_done.phpへ送信 -->
                    <form method="post" action="admin_reservation_edit_done.php">
                    <input type="hidden" name="one_time_user_id" value="<?php echo $one_time_user_id;?>">   
                    <input type="hidden" name="reservation_number" value="<?php echo $reservation_number;?>">
                    <input type="hidden" name="room_number" value="<?php echo $room_number;?>">
                    <input type="hidden" name="user_sum" value="<?php echo $number_of_user;?>">
                    <input type="hidden" name="r_starttime" value="<?php echo $r_starttime->format('Y-m-d H:i');?>">
                    <input type="hidden" name="r_endtime" value="<?php echo $r_endtime->format('Y-m-d H:i');?>">
                    <input type="hidden" name="total_rental_price" value="<?php echo $total_rental_price;?>">
                    <input type="hidden" name="remark" value="<?php echo $remark;?>">

                    <!-- 備品貸出テーブルへ追加のため -->
                    <?php
                     if($item_name_data!=null){
                        print'<p>備品追加可能</p>';
                        print'<input type="hidden" name="item_cnt" value="'.$item_cnt.'">';
                        print'<input type="hidden" name="not_rental" value="false">';
                        for($send_data=0;$send_data<$item_cnt;$send_data++){
                            print'<input type="hidden" name="db_item_number['.$send_data.']" value="'.$db_item_number[$send_data].'">';
                            print'<input type="hidden" name="item_pieces['.$send_data.']" value="'.$item_pieces_data[$send_data].'">';
                            /////////////////////////////////////////////////////////////////////////////^^^^^^^^^^^^^^^^
                            if($price_ex=='P'){
                                print'<input type="hidden" name="price_ex" value="P">';
                                print'<input type="hidden" name="item_price_sum['.$send_data.']" value="'.$item_price_sum[$send_data].'">';
                            }
                            else{
                                print'<input type="hidden" name="price_ex" value=" ">';
                            }
                            /////////////////////////////////////////////////////////////////////////////^^^^^^^^^^^^^^^^^
                        }
                     }
                     else{
                        print'<input type="hidden" name="price_ex" value=" ">';///////
                        print'<input type="hidden" name="not_rental" value="true">';
                     }
                    ?>

                    <button type="submit" name="action" value="Yes">はい</button>
                    </form>
                    <?php
                    print'<form method="post" action="admin_reservation_edit_stop.php">';
                        print'<input type="hidden" name="reservation_number" value="'.$reservation_number.'">';
                        print'<input type="submit" value="いいえ">';
                    print'</form>';
                //}
                //else
                //{

                //}
            }
            }
        }
        catch(Exception $e)
        {
            print $e;
            echo "エラーメッセージ: " . htmlspecialchars($e->getMessage()) . "\n";
            error_log($e->getMessage(), 0);
 
        }
        
         ?>
    </body>
</html>