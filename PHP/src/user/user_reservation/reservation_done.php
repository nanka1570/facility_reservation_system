<?php
include_once "../../common/connect.php";
include_once "../../common/session.php";
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="reservation.css">
<title></title>
</head>
<body>
<?php
try
{
    $action = $_POST['action'];
    if($action == 'Yes')
    {
        $reservation_number = $_POST['reservation_number'];
        $room_number = $_POST['room_number'];
        $user_sum = $_POST['user_sum'];
        $r_starttime = $_POST['r_starttime'];
        $r_endtime = $_POST['r_endtime'];
        $total_rental_price = $_POST['total_rental_price'];
        $price_ex = $_POST['price_ex'];
        $remark = $_POST['remark'];

        //備品の貸出がある場合
        $not_rental = $_POST['not_rental'];
        //var_dump($not_rental);
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
        //予約を確定し、DBに反映させる
        $sql = 'UPDATE reservation_table 
                SET cancel_flag = ?,
                    sum_of_price = ?,
                    remark= ?
                WHERE reservation_number = ?';
        $stmt = $dbh->prepare($sql);
        $stmt->execute([
            '',
            $total_rental_price,
            $remark,
            $reservation_number
        ]);
        $dbh = null;

        //予約履歴テーブルにも追加する
        $dbh = $conn; 
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
        $sql = 'UPDATE history_table 
                SET cancel_flag = ?,
                    sum_of_price = ?,
                    remark= ?
                WHERE reservation_number = ?';
        $stmt = $dbh->prepare($sql);
        $stmt->execute([
            '',
            $total_rental_price,
            $remark,
            $reservation_number
        ]);


        echo '<h2 class="check">予約が完了しました。<h2><br>';
        echo '<p class="check">予約番号：' . htmlspecialchars($reservation_number) . '</p>';
        print'<form action="../../login/user_home.php">';
            print'<p class="buttons"><input type="submit" class="button" value="ホーム画面に戻る"></p>';
        print'</form>';
        print'<p class="check">もう一度予約する場合はこちら</p>';
        print'<form action="room.php">';
            print'<p class="buttons"><input type="submit" class="button" value="予約画面へ"></p>';
        print'</form>';
    }
    else
    {
        //予約キャンセルの場合、仮予約を削除
        $sql = 'DELETE FROM reservation_table WHERE reservation_number = ?';
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$_POST['reservation_number']]);

        echo '<h2 class="check">予約をキャンセルしました</h2><br>';
        print'<form action="../../login/user_home.php">';
            print'<p class="buttons"><input type="submit" class="button" value="ホーム画面に戻る"></p>';
        print'</form>';
        print'<p class="check">もう一度予約する場合はこちら</p>';
        print'<form action="room.php">';
            print'<p class="buttons"><input type="submit" class="button" value="予約画面へ"></p>';
        print'</form>';
    }        
}
catch(Exception $e)
{
    echo "エラーが発生しました：" . htmlspecialchars($e->getMessage());
}

?>
</body>
</html>
