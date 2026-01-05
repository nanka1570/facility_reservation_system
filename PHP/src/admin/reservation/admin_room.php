<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>新規予約</title>
        <!-- <link rel="stylesheet" href="check.css">
        <meta name="" content=""> -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../../common/admin_basic.css">
        <link rel="stylesheet" href="reservation_new.css">
    </head>
    <body>
        <header class="admin-header">
            <h1>新規予約</h1>
        </header>
        <?php
        include_once "../../common/DB_switch.php";
        // include_once "../../common/connect.php";
        include_once "../../common/session.php";
            //部屋の数を数えるSQL
            $_SESSION['room_ctg'] = 0;
            $room_cnt = 0;
            $sql = 'select distinct category_name, category_number from category_table WHERE category_number != 0';
            //$sql = 'select distinct category_name ,category_number from facility_table';
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $rec=$stmt->fetch();
            if($rec==false){
                print "該当する名前はありません";
            }else{
                while($rec==true){
                    // category_cssのclassを追加
                    print '<table>';
                            print '<th class="category_css">';
                                print $rec['category_name'];
                        print '<td>';
                            //print $rec['category_number'];
                            print '<form method="post" action="admin_reservation_add_01.php">';
                                print '<input type="hidden" name="room_ctg" value='."{$rec['category_number']}";print'>';
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