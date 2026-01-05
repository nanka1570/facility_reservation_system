<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title></title>
        <meta name="" content="">
    </head>
    <body>
        <?php
        //include "connect.php";
        //include "session.php";
        include_once "../../common/DB_switch.php";
        // include_once "../../common/connect.php";
        include_once "../../common/session.php";
            //部屋の数を数えるSQL
            $_SESSION['room_ctg'] = 0;
            $room_cnt = 0;
            //$sql = 'select distinct category_name ,category_number from category_table';//学校用
            $sql = 'select distinct C.category_name ,C.category_number from facility_table AS f,category_table AS C
                    WHERE F.category_number =C.category_number';
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $rec=$stmt->fetch();
            if($rec==false){
                print "該当する名前はありません";
            }else{
                while($rec==true){
                    print '<table>';
                        print '<th>';
                            print $rec['category_name'];

                        print '<th>';
                        print '<td>';
                            //print $rec['category_number'];
                            print '<form method="post" action="admin_reservation_edit_02.php">';
                                print'<input type="hidden" name="user_id" value="'.$_POST['user_id'].'">';
                                print'<input type="hidden" name="date" value="'.$_POST['date'].'">';
                                print '<input type="hidden" name="room_ctg" value="'.$rec['category_number'].'">';
                                print '<input type="hidden" name="reservation_number" value="'.$_POST['reservation_number'].'">';
                                print '<input type="submit" value="予約フォームへ">';
                            print '</form>';
                        print '</td>';
                    print '</table>';
                    $rec=$stmt->fetch();
                    /*print $rec['category_name'];
                    print "<br/>";
                    $room_cnt = $room_cnt + 1;
                    $rec=$stmt->fetch();*/
                }
            }
            print'<form action="../../login/admin_home.php">';
                print'<input type="submit" value="ホーム画面へ">';
            print'</form>';
            //print (int)$room_cnt;

            $conn=null;
            $stmt=null; 
        ?>
    </body>
</html>