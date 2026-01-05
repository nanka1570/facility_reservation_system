<?php
    //include "connect.php";
//部屋の数を数えるSQL
    $room_cnt = 0;
    $sql = 'select room_name, room_number from facility_table where category_number = ?';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$room_num]);
    $rec=$stmt->fetch();
    if($rec==false){
        print "該当する名前はありません";
    }else{
        while($rec==true){
            $room_name[] = $rec['room_name'];
            $room_number[] = $rec['room_number'];
            //print "<br/>";
            $room_cnt = $room_cnt + 1;
            $rec=$stmt->fetch();
        }
    }
    //print (int)$room_cnt;
    //var_dump($room_name);
    //$conn=null;
    $stmt=null; 
?>