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
        <meta name="" content="">
    </head>
    <body>
        <?php
            $reservation_number = $_POST['reservation_number'];
            
            $dbh = $conn; 
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 

            $sql = 'DELETE FROM reservation_table WHERE reservation_number = ?';
            $stmt = $dbh->prepare($sql);
            $stmt->execute([$reservation_number]);

            $dbh = null;
            $dbh = $conn; 
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 

            $sql = 'DELETE FROM history_table WHERE reservation_number = ?';
            $stmt = $dbh->prepare($sql);
            $stmt->execute([$reservation_number]);

            print'<p class="check">仮予約を取り消しました</p>';
            print'<form action="../../login/user_home.php">';
                print'<p class="buttons"><input type="submit" class="button" value="ホーム画面に戻る"></p>';
            print'</form>';
            print'<p class="check">もう一度予約する場合はこちら</p>';
            print'<form action="room.php">';
                print'<p class="buttons"><input type="submit" class="button" value="予約画面へ"></p>';
            print'</form>';
        ?>
    </body>
</html>