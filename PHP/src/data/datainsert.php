<?php
include "connect.php";
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>データ挿入</title>
<link rel="stylesheet" href="table.css">
<style>
body{
    background-color: darkgray;
}
p{
    color:red;
    font-size:25px; 
}
input{
    font-size: 100px;
    width: 500px;
    height: 400px;
}
</style>
</head>
<body>
<?php
$table=$_POST['table'];
print "<p>".$table."テーブルにデータを挿入</p>";
print'<form action="tabledata.php">';
print'<input type="submit" value="戻る">';
print'</form>';
switch($table)
{
    case'user':
    try{
        $sql="insert user_table values(
        '".$_POST['userid']."',
        '".$_POST['pass']."',
        '".$_POST['u_name']."',
        '".$_POST['mail']."',
        '".$_POST['s_ques']."',
        '".$_POST['s_ans']."',
        '".$_POST['status']."')";
        $stmt=$conn->prepare($sql);
        $stmt->execute();
    }catch(Exception $e){
        print $e;
        exit;
    }
    print "user_id".$_POST['userid']."のデータを挿入しました";
        break;

    case'reservation':
    try{
        $sql="insert reservation_table values(
        '".$_POST['reser_num']."',
        '".$_POST['id']."',
        '".$_POST['room_num']."',
        '".$_POST['num_user']."',
        '".$_POST['start']."',
        '".$_POST['end']."',
        '".$_POST['can_f']."',
        '".$_POST['sum_p']."',
        '".$_POST['remark']."')
        insert history_table values(
        '".$_POST['reser_num']."',
        '".$_POST['id']."',
        '".$_POST['room_name']."',
        '".$_POST['num_user']."',
        '".$_POST['start']."',
        '".$_POST['end']."',
        '".$_POST['can_f']."',
        '".$_POST['sum_p']."',
        '".$_POST['remark']."')";
        $stmt=$conn->prepare($sql);
        $stmt->execute();
    }catch(Exception $e){
        print $e;
        exit;
    }
        break;
        print "reservation_number".$_POST['reser_num']."のデータを挿入しました";
        case'history':
            try{
                $sql="insert history_table values(
                '".$_POST['reser_num']."',
                '".$_POST['id']."',
                '".$_POST['room_name']."',
                '".$_POST['num_user']."',
                '".$_POST['start']."',
                '".$_POST['end']."',
                '".$_POST['can_f']."',
                '".$_POST['sum_p']."',
                '".$_POST['remark']."')";
                $stmt=$conn->prepare($sql);
                $stmt->execute();
            }catch(Exception $e){
                print $e;
                exit;
            }
                break;
                print "reservation_number".$_POST['reser_num']."のデータを挿入しました";

    case'facility':
    try{
        $sql="insert facility_table values(
        '".$_POST['room_num']."',
        '".$_POST['room_name']."',
        '".$_POST['maxpeople']."',
        '".$_POST['usable']."',
        '".$_POST['equipment']."',
        '".$_POST['time_unit']."',
        '".$_POST['unit_price']."',
        '".$_POST['category_num']."')";
        $stmt=$conn->prepare($sql);
        $stmt->execute();
    }catch(Exception $e){
        print $e;
        exit;
    }
    print "room_number".$_POST['room_num']."のデータを挿入しました";
        break;

    case'category':
    try{
        $sql="insert category_table values(
        '".$_POST['category_num']."',
        '".$_POST['category_name']."')";
        $stmt=$conn->prepare($sql);
        $stmt->execute();
    }catch(Exception $e){
        print $e;
        exit;
    }
    print "category_number".$_POST['category_num']."のデータを挿入しました";
        break;

    case'inquily':
    try{
        $sql="insert inquily_table values(
        '".$_POST['id']."',
        '".$_POST['serial']."',
        '".$_POST['text']."',
        '".$_POST['time']."',
        '".$_POST['t_category']."')";
        $stmt=$conn->prepare($sql);
        $stmt->execute();
    }catch(Exception $e){
        print $e;
        exit;
    }
    print "serial_number".$_POST['serial']."のデータを挿入しました";
        break;

    case'item':
        try{
        $sql="insert item_table values(
        '".$_POST['item_num']."',
        '".$_POST['item_name']."',
        '".$_POST['total']."',
        '".$_POST['unit_price']."')";
        $stmt=$conn->prepare($sql);
        $stmt->execute();
    }catch(Exception $e){
        print $e;
        exit;
    }
    print "item_number".$_POST['item_num']."のデータを挿入しました";
        break;

    case'rental':
    try{
        $sql="insert rental_table values(
        '".$_POST['reser_num']."',
        '".$_POST['item_num']."',
        '".$_POST['number_rental']."',
        '".$_POST['rental_price']."')";
        $stmt=$conn->prepare($sql);
        $stmt->execute();
    }catch(Exception $e){
        print $e;
        exit;
    }
    print "reservation_number".$_POST['reser_num'].",item_number".$_POST['item_num']."のデータを挿入しました";
        break;

    case'extension':
    try{
        $sql="insert extension_table values(
        '".$_POST['date']."',
        '".$_POST['extension']."',
        '".$_POST['rental_f']."',
        '".$_POST['price_f']."',
        '".$_POST['eq_f']."')";
        $stmt=$conn->prepare($sql);
        $stmt->execute();
    }catch(Exception $e){
        print $e;
        exit;
    }
    print "change_extension_date".$_POST['date']."のデータを挿入しました";
        break;
    default:
    break;
}

?>
</body>
</html>