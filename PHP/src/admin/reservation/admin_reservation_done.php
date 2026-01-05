<?php
include_once "../../common/DB_switch.php";
// include_once "../../common/connect.php";
include_once "../../common/session.php";
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="../../common/admin_basic.css">
<link rel="stylesheet" href="check.css">
<title>予約完了</title>
</head>
<body>
    <header class="admin-header">
        <h1>予約完了</h1>
    </header>
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
        $remark = $_POST['remark'];

        //備品の貸出がある場合
        $not_rental = $_POST['not_rental'];
        //var_dump($not_rental);
        if($not_rental == "false"){
            $db_item_number = $_POST['db_item_number'];
            $item_pieces = $_POST['item_pieces'];
            $item_price_sum = $_POST['item_price_sum'];

            /*var_dump($db_item_number);
            print"<br>";
            var_dump($item_pieces);
            print"<br>";
            var_dump($item_price_sum);
            print"<br>";*/
        }

        
        //備品の貸出がある場合
        if($not_rental == "false"){
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
//<div class="content">を追加
print '<div class="content">';

        echo '<h2>予約が完了しました。<h2><br>';
        echo '<p>予約番号：' . htmlspecialchars($reservation_number) . '</p>';
        print'<form action="../../login/admin_home.php">';
            print'<input type="submit" value="ホーム画面に戻る" class="button"><br/>';
        print'</form>';
        print'<p>もう一度予約する場合はこちら</p>';
        print'<form action="admin_room.php">';
            print'<input type="submit" value="予約画面へ" class="button">';
        print'</form>';

//</div>を追加
print '</div>';
    }
    else
    {
        //予約キャンセルの場合、仮予約を削除
        $sql = 'DELETE FROM reservation_table WHERE reservation_number = ?';
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$_POST['reservation_number']]);

        echo '<h2>予約をキャンセルしました</h2><br>';
        print'<form action="../../login/admin_home.php">';
            print'<input type="submit" value="ホーム画面に戻る" class="button"><br/>';
        print'</form>';
        print'<p>もう一度予約する場合はこちら</p>';
        print'<form action="admin_room.php">';
            print'<input type="submit" class="button" value="予約画面へ">';
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
