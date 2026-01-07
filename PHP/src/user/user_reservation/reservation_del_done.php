<?php
include_once "../../common/connect.php"; 
include_once "../../common/session.php"; 

?>
<!DOCTYPE html> 
<html> 
<head> 
<meta charset="UTF-8"> 
<link rel="stylesheet" href="../../common/user_basic.css">
<link rel="stylesheet" href="reservation.css"> 
<title></title> 
</head> 
<body> 
    <?php
        $reservation_number = $_POST['reservation_number'];
        $sql = "UPDATE reservation_table SET cancel_flag = 'C' WHERE reservation_number = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$reservation_number]);

        $reservation_number = $_POST['reservation_number'];
        $sql = "UPDATE history_table SET cancel_flag = 'C' WHERE reservation_number = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$reservation_number]);
        
        print'<p class="check">予約をキャンセルしました。</p>';
        print'<form action="../../login/user_home.php">';
            print'<p class="buttons"><input type="submit" class="color" value="ホーム画面に戻る"></p>';
        print'</form>';
    ?>
</body>
</html>