<?php
include_once "../../common/connect.php";
include_once "../../common/session.php";
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<link rel="stylesheet" href="../../common/user_basic.css">
<link rel="stylesheet" href="reservation.css">
</head>
<body>
    <?php
        //var_dump($_POST);
        //print"<br>";
        $reservation_number = $_POST['reservation_number'];
        $user_id = $_POST['one_time_user_id'];
        $room_number = $_POST['room_number'];
        $number_of_user = $_POST['user_sum'];
        $start_time_of_use = $_POST['r_starttime'];
        $end_time_of_use = $_POST['r_endtime'];
        $sum_of_price =$_POST['total_rental_price'];
        $cancel_flag = " ";
        $price_ex = $_POST['price_ex'];
        $remark = $_POST['remark'];

        //備品の貸出がある場合
        $not_rental = $_POST['not_rental'];
        //var_dump($not_rental);
        //print"<br>";
        if($not_rental == "false"){
            $db_item_number = $_POST['db_item_number'];
            $item_pieces = $_POST['item_pieces'];
            if($price_ex=='P'){
                $item_price_sum = $_POST['item_price_sum'];
            }
            /*var_dump($db_item_number);
            print"<br>";
            var_dump($item_pieces);
            print"<br>";
            var_dump($item_price_sum);
            print"<br>";*/
        }

        //備品の貸出がある場合
        if($not_rental == "false"){
            $dbh = $conn; 
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = 'SELECT * FROM rental_table WHERE reservation_number = ?';
            $stmt=$dbh->prepare($sql);
            $stmt->execute([$reservation_number]);
            $rental_check = $stmt->fetch(PDO::FETCH_ASSOC);
            $dbh = null;
            //var_dump($rental_check);
            //print"<br>";
            if($rental_check!=null){
                $dbh = $conn; 
                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $sql = 'DELETE FROM rental_table WHERE reservation_number = ?';
                $stmt=$dbh->prepare($sql);
                $stmt->execute([$reservation_number]);
                $dbh = null;
            }

            if($price_ex == "P"){
                $item_cnt = $_POST['item_cnt'];
                for($insert_cnt=0; $insert_cnt<$item_cnt; $insert_cnt++){
                    $dbh = $conn; 
                    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $sql = 'INSERT INTO rental_table
                        (reservation_number ,
                        item_number,
                        number_of_rental,
                        rental_price) VALUES (?,?,?,?)';
                    $stmt=$dbh->prepare($sql);
                    $stmt->execute([
                        $reservation_number,
                        $db_item_number[$insert_cnt],
                        $item_pieces[$insert_cnt],
                        $item_price_sum[$insert_cnt]
                    ]);
                    }
                $dbh = null;     
            }else{
                $item_cnt = $_POST['item_cnt'];
                for($insert_cnt=0; $insert_cnt<$item_cnt; $insert_cnt++){
                    $dbh = $conn; 
                    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $sql = 'INSERT INTO rental_table
                        (reservation_number ,
                        item_number,
                        number_of_rental,
                        rental_price) VALUES (?,?,?,?)';
                    $stmt=$dbh->prepare($sql);
                    $stmt->execute([
                        $reservation_number,
                        $db_item_number[$insert_cnt],
                        $item_pieces[$insert_cnt],
                        0
                    ]);
                }
                $dbh = null;            
            }
        }


        $dbh = $conn; 
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
        $sql = 'UPDATE reservation_table SET 
        reservation_number = :reservation_number,
        user_id = :user_id,
        room_number = :room_number,
        number_of_user = :number_of_user,
        start_time_of_use = :start_time_of_use,
        end_time_of_use = :end_time_of_use,
        cancel_flag = :cancel_flag,
        sum_of_price = :sum_of_price,
        remark = :remark WHERE reservation_number = :reservation_number2';

        $stmt = $dbh->prepare($sql);

        $stmt->bindParam(':reservation_number', $reservation_number);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':room_number', $room_number);
        $stmt->bindParam(':number_of_user', $number_of_user);
        $stmt->bindParam(':start_time_of_use', $start_time_of_use);
        $stmt->bindParam(':end_time_of_use', $end_time_of_use);
        $stmt->bindParam(':cancel_flag', $cancel_flag);
        $stmt->bindParam(':sum_of_price', $sum_of_price);
        $stmt->bindParam(':remark', $remark);
        $stmt->bindParam(':reservation_number2', $reservation_number);

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
        $dbh = null;

        $dbh = $conn; 
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
        $sql = 'UPDATE history_table SET 
        reservation_number = :reservation_number,
        user_id = :user_id,
        room_name = :room_name,
        number_of_user = :number_of_user,
        start_time_of_use = :start_time_of_use,
        end_time_of_use = :end_time_of_use,
        cancel_flag = :cancel_flag,
        sum_of_price = :sum_of_price,
        remark = :remark WHERE reservation_number = :reservation_number2';

        $stmt = $dbh->prepare($sql);

        $stmt->bindParam(':reservation_number', $reservation_number);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':room_name', $room_name);
        $stmt->bindParam(':number_of_user', $number_of_user);
        $stmt->bindParam(':start_time_of_use', $start_time_of_use);
        $stmt->bindParam(':end_time_of_use', $end_time_of_use);
        $stmt->bindParam(':cancel_flag', $cancel_flag);
        $stmt->bindParam(':sum_of_price', $sum_of_price);
        $stmt->bindParam(':remark', $remark);
        $stmt->bindParam(':reservation_number2', $reservation_number);

        $stmt -> execute();
        $dbh = null;

        print'<p class="check">予約を変更しました。</p>';

        print'<form action="../../login/user_home.php">';
            print'<p class="buttons"><input type="submit" class="color" value="ホーム画面に戻る"></p>';
        print'</form>';
        print'<p class="check">もう一度予約を変更する場合はこちら</p>';
        print'<form action="reservation_edit_list.php">';
            print'<p class="buttons"><input type="submit" class="color" value="予約変更画面へ"></p>';
        print'</form>';

    ?>
</body>
</html>