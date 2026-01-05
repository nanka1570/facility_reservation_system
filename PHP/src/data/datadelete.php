<?php
include "connect.php";
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>テーブル全件表示</title>
<link rel="stylesheet" href="table.css">
<style>
    body{
        background-color: darkgray;
    }
    p{
        color:blue;
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
$table=$_POST['table_d'];
print "<p>".$table."テーブルからデータを削除</p>";
print'<form action="tabledata.php">';
print'<input type="submit" value="戻る">';
print'</form>';
switch($table)
{
    case'user':
    try{
        $sql="delete from user_table where user_id='".$_POST['id']."'";
        $stmt=$conn->prepare($sql);
        $stmt->execute();
    }catch(Exception $e){
        print $e;
        exit;
    }
    print "user_id".$_POST['id']."のデータを削除しました";
        break;

    case'reservation':
    try{
        $sql="delete from reservation_table where reservation_number='".$_POST['resnum']."'
              delete from history_table where reservation_number='".$_POST['resnum']."'";
        $stmt=$conn->prepare($sql);
        $stmt->execute();
    }catch(Exception $e){
        print $e;
        exit;
    }
        print "reservation_number".$_POST['resnum']."のデータを削除しました";
        break;

    // case'history':
    //     try{
    //         $sql="delete from history_table where reservation_number='".$_POST['resnum']."'";
    //         $stmt=$conn->prepare($sql);
    //         $stmt->execute();
    //     }catch(Exception $e){
    //         print $e;
    //         exit;
    //     }
    //         print "reservation_number".$_POST['resnum']."のデータを削除しました";
    //         break;

    case'facility':
    try{
        $sql="delete from facility_table where room_number='".$_POST['num']."'";
        $stmt=$conn->prepare($sql);
        $stmt->execute();
    }catch(Exception $e){
        print $e;
        exit;
    }
    print "room_number".$_POST['num']."のデータを削除しました";
        break;

    case'category':
    try{
        $sql="delete from category_table where category_number='".$_POST['num']."'";
        $stmt=$conn->prepare($sql);
        $stmt->execute();
    }catch(Exception $e){
        print $e;
        exit;
    }
    print "category_number".$_POST['num']."のデータを削除しました";
        break;

    case'inquily':
    try{
        $sql="delete from inquily_table where serial_number='".$_POST['num']."'";
        $stmt=$conn->prepare($sql);
        $stmt->execute();
    }catch(Exception $e){
        print $e;
        exit;
    }
    print "serial_number".$_POST['num']."のデータを削除しました";
        break;

    case'item':
        try{
        $sql="delete from item_table where item_number='".$_POST['num']."'";
        $stmt=$conn->prepare($sql);
        $stmt->execute();
    }catch(Exception $e){
        print $e;
        exit;
    }
    print "item_number".$_POST['num']."のデータを削除しました";
        break;

    case'rental':
    try{
        $sql="delete from rental_table where reservation_number='".$_POST['rnum']."' and item_number='".$_POST['inum']."'";
        $stmt=$conn->prepare($sql);
        $stmt->execute();
    }catch(Exception $e){
        print $e;
        exit;
    }
    print "reservation_number".$_POST['rnum'].",item_number".$_POST['inum']."のデータを削除しました";
        break;

    case'extension':
    try{
        $sql="delete from extension_table where change_extension_date='".$_POST['change']."'";
        $stmt=$conn->prepare($sql);
        $stmt->execute();
    }catch(Exception $e){
        print $e;
        exit;
    }
    print "change_extension_date".$_POST['change']."のデータを削除しました";
        break;
    default:
    break;
}

?>
</body>
</html>