<?php
include_once "../../common/DB_switch.php";
// include "../../common/connect.php";
include "../../common/session.php";
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title></title>
        <link rel="stylesheet" href="../../common/admin_basic.css">
        <link rel="stylesheet" href="check.css"> 
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

            print'<p>仮予約を取り消しました</p>';
            print'<form action="../../login/admin_home.php">';
                print'<input type="submit" value="ホーム画面に戻る" class="button"><br/>';
            print'</form>';
            print'<p>もう一度予約する場合はこちら</p>';
            print'<form action="admin_room.php">';
                print'<input type="submit" value="予約画面へ" class="button">';
            print'</form>';
        ?>
    </body>
</html>