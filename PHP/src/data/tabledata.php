<?php
include "connect.php";
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>テーブル全件表示</title>
<link rel="stylesheet" href="tabledata.css">
</head>
<body>
<?php
    print"<p class=table-name style='color:red;'>ユーザーテーブル</p>";
    
    print '<form action="datainsert.php" method="post">';
    print '<input type="text" name="userid" placeholder="id" >';
    print '<input type="text" name="pass" placeholder="pass" >';
    print '<input type="text" name="u_name" placeholder="name" >';
    print '<input type="text" name="mail" placeholder="mail" >';
    print '<input type="text" name="s_ques" placeholder="question(1~5)" >';
    print '<input type="text" name="s_ans" placeholder="answer" >';
    print '<input type="hidden" name="table" value="user">';
    print '<input type="submit" onclick="location.href=datainsert.php" value ="追加"></form>';

    print"<table>";
    print"<tr>";
    print"<th></th>";
    print"<th>user_id</th>";
    print"<th>password</th>";
    print"<th>user_name</th>";
    print"<th>mail_address</th>";
    print"<th>secret_question</th>";
    print"<th>secret_answer</th>";
    print"</tr>";
try{
    $sql="select * from user_table";
    $stmt=$conn->prepare($sql);
    $stmt->execute();
    while(true)
    {
        $rec=$stmt->fetch(PDO::FETCH_ASSOC);
        if($rec==false){
            break;
        }
        print "<tr>";
        print '<form action="datadelete.php" method="post">';
        print '<input type="hidden" name="table_d" value="user">';
        print '<input type="hidden" name="id" value="'.$rec['user_id'].'">';
        print '<td><input type="submit" onclick="location.href=datadelete.php" value="消去"></td>';
        print "<td>";print $rec['user_id'];print "</td>";
        print "<td>";print $rec['password'];print "</td>";
        print "<td>";print $rec['user_name'];print "</td>";
        print "<td>";print $rec['mail_address'];print "</td>";
        print "<td>";print $rec['secret_question'];print "</td>";
        print "<td>";print $rec['secret_answer'];print "</td></form>";
        print "</tr>";
    }
}
catch(Exception $e){
    print $e;
    exit();
}
print "</table>";

print"<p class=table-name style='color:blue;'>予約テーブル</p>";

print '<form action="datainsert.php" method="post">';
print '<input type="text" name="reser_num" placeholder="reservation_num" >';
print '<input type="text" name="id" placeholder="id" >';
print '<input type="text" name="room_num" placeholder="room_num" >';
print '<input type="text" name="room_name" placeholder="room_name(history_table)" >';
print '<input type="text" name="num_user" placeholder="number_of_user" >';
print '<input type="text" name="start" value="2025-00-00 00:00" >';
print '<input type="text" name="end" value="2025-00-00 00:00" >';
print '<input type="text" name="can_f" placeholder="cancel" >';
print '<input type="text" name="sum_p" placeholder="sum_of_price" >';
print '<input type="text" name="remark" placeholder="remark" >';
print '<input type="hidden" name="table" value="reservation">';
print '<input type="submit" onclick="location.href=datainsert.php" value ="追加"></form>';

print"<table>";
print"<tr>";
print"<th></th>";
print"<th>reservation_number</th>";
print"<th>user_id</th>";
print"<th>room_number</th>";
print"<th>number_of_user</th>";
print"<th>start_time_of_use</th>";
print"<th>end_time_of_use</th>";
print"<th>cancel_flag</th>";
print"<th>sum_of_price</th>";
print"<th>remark</th>";
print"</tr>";
try{
$sql="select * from reservation_table";
$stmt=$conn->prepare($sql);
$stmt->execute();
while(true)
{
    $rec=$stmt->fetch(PDO::FETCH_ASSOC);
    if($rec==false){
        break;
    }
    print "<tr>";
    print '<form action="datadelete.php" method="post">';
    print '<input type="hidden" name="table_d" value="reservation">';
    print '<input type="hidden" name="resnum" value="'.$rec['reservation_number'].'">'; 
    print '<td><input type="submit" onclick="location.href=datadelete.php" value="消去"></td>';
    print "<td>";print $rec['reservation_number'];print "</td>";
    print "<td>";print $rec['user_id'];print "</td>";
    print "<td>";print $rec['room_number'];print "</td>";
    print "<td>";print $rec['number_of_user'];print "</td>";
    print "<td>";print $rec['start_time_of_use'];print "</td>";
    print "<td>";print $rec['end_time_of_use'];print "</td>";
    print "<td>";print $rec['cancel_flag'];print "</td>";
    print "<td>";print $rec['sum_of_price'];print "</td>";
    print "<td>";print $rec['remark'];print "</td></form>";
    print "</tr>";
}
}
catch(Exception $e){
print $e;
exit();
}
print "</table>";

print"<p class=table-name style='color:gray;'>予約履歴テーブル</p>";

print"<table>";
print"<tr>";
print"<th>reservation_number</th>";
print"<th>user_id</th>";
print"<th>room_name</th>";
print"<th>number_of_user</th>";
print"<th>start_time_of_use</th>";
print"<th>end_time_of_use</th>";
print"<th>cancel_flag</th>";
print"<th>sum_of_price</th>";
print"<th>remark</th>";
print"</tr>";
try{
$sql="select * from history_table";
$stmt=$conn->prepare($sql);
$stmt->execute();
while(true)
{
    $rec=$stmt->fetch(PDO::FETCH_ASSOC);
    if($rec==false){
        break;
    }
    print "<tr>";
    print '<form action="datadelete.php" method="post">';
    print '<input type="hidden" name="table_d" value="history">';
    print '<input type="hidden" name="resnum" value="'.$rec['reservation_number'].'">'; 
    print "<td>";print $rec['reservation_number'];print "</td>";
    print "<td>";print $rec['user_id'];print "</td>";
    print "<td>";print $rec['room_name'];print "</td>";
    print "<td>";print $rec['number_of_user'];print "</td>";
    print "<td>";print $rec['start_time_of_use'];print "</td>";
    print "<td>";print $rec['end_time_of_use'];print "</td>";
    print "<td>";print $rec['cancel_flag'];print "</td>";
    print "<td>";print $rec['sum_of_price'];print "</td>";
    print "<td>";print $rec['remark'];print "</td></form>";
    print "</tr>";
}
}
catch(Exception $e){
print $e;
exit();
}
print "</table>";

print"<p class=table-name style='color:yellow;'>施設テーブル</p>";

print '<form action="datainsert.php" method="post">';
print '<input type="text" name="room_num" placeholder="room_num" >';
print '<input type="text" name="room_name" placeholder="room_name" >';
print '<input type="text" name="maxpeople" placeholder="maxpeople" >';
print '<input type="text" name="usable" placeholder="usable_category(Y or N)" >';
print '<input type="text" name="equipment" placeholder="equipment" >';
print '<input type="text" name="time_unit" placeholder="time_unit" >';
print '<input type="text" name="unit_price" placeholder="unit_price" >';
print '<input type="text" name="category_num" placeholder="category_num" >';
print '<input type="text" name="time_ex" placeholder="time_extension" >';
print '<input type="hidden" name="table" value="facility">';
print '<input type="submit" onclick="location.href=datainsert.php" value ="追加"></form>';

print"<table>";
print"<tr>";
print"<th></th>";
print"<th>room_number</th>";
print"<th>room_name</th>";
print"<th>max_number_of_people</th>";
print"<th>usable_category</th>";
print"<th>equipment</th>";
print"<th>time_of_unit</th>";
print"<th>time_of_unit_price</th>";
print"<th>category_number</th>";
print"<th>time_extension</th>";
print"</tr>";
try{
$sql="select * from facility_table";
$stmt=$conn->prepare($sql);
$stmt->execute();
while(true)
{
    $rec=$stmt->fetch(PDO::FETCH_ASSOC);
    if($rec==false){
        break;
    }
    print "<tr>";
    print '<form action="datadelete.php" method="post">';
    print '<input type="hidden" name="table_d" value="facility">';
    print '<input type="hidden" name="num" value="'.$rec['room_number'].'">';
    print '<td><input type="submit" onclick="location.href=datadelete.php" value="消去"></td>';
    print "<td>";print $rec['room_number'];print "</td>";
    print "<td>";print $rec['room_name'];print "</td>";
    print "<td>";print $rec['max_number_of_people'];print "</td>";
    print "<td>";print $rec['usable_category'];print "</td>";
    print "<td>";print $rec['equipment'];print "</td>";
    print "<td>";print $rec['time_of_unit'];print "</td>";
    print "<td>";print $rec['time_of_unit_price'];print "</td>";
    print "<td>";print $rec['category_number'];print "</td></form>";
    print "<td>";print $rec['time_extension'];print "</td></form>";
    print "</tr>";
}
}
catch(Exception $e){
print $e;
exit();
}
print "</table>";

print"<p class=table-name style='color:aqua;'>分類テーブル</p>";

print '<form action="datainsert.php" method="post">';
print '<input type="text" name="category_num" placeholder="category_num" >';
print '<input type="text" name="category_name" placeholder="category_name" >';
print '<input type="hidden" name="table" value="category">';
print '<input type="submit" onclick="location.href=datainsert.php" value ="追加"></form>';


print"<table>";
print"<tr>";
print"<th></th>";
print"<th>category_number</th>";
print"<th>category_name</th>";
print"</tr>";
try{
$sql="select * from category_table";
$stmt=$conn->prepare($sql);
$stmt->execute();
while(true)
{
    $rec=$stmt->fetch(PDO::FETCH_ASSOC);
    if($rec==false){
        break;
    }
    print "<tr>";
    print '<form action="datadelete.php" method="post">';
    print '<input type="hidden" name="table_d" value="category">';
    print '<input type="hidden" name="num" value="'.$rec['category_number'].'">';
    print '<td><input type="submit" onclick="location.href=datadelete.php" value="消去"></td>';
    print "<td>";print $rec['category_number'];print "</td>";
    print "<td>";print $rec['category_name'];print "</td></form>";
    print "</tr>";
}
}
catch(Exception $e){
print $e;
exit();
}
print "</table>";

print"<p class=table-name style='color:orange;'>問合せテーブル</p>";

print '<form action="datainsert.php" method="post">';
print '<input type="text" name="id" placeholder="id" >';
print '<input type="text" name="serial" placeholder="serialnum" >';
print '<input type="text" name="text" placeholder="text" >';
print '<input type="text" name="time" value="2025-00-00 00:00" >';
print '<input type="text" name="t_category" placeholder="text_category(A or Y)">';
print '<input type="hidden" name="table" value="inquily">';
print '<input type="submit" onclick="location.href=datainsert.php" value ="追加"></form>';

print"<table>";
print"<tr>";
print"<th></th>";
print"<th>user_id</th>";
print"<th>serial_number</th>";
print"<th>text</th>";
print"<th>inquily_time</th>";
print"<th>text_category</th>";
print"</tr>";
try{
$sql="select * from inquily_table";
$stmt=$conn->prepare($sql);
$stmt->execute();
while(true)
{
    $rec=$stmt->fetch(PDO::FETCH_ASSOC);
    if($rec==false){
        break;
    }
    print "<tr>";
    print '<form action="datadelete.php" method="post">';
    print '<input type="hidden" name="table_d" value="inquily">';
    print '<input type="hidden" name="num" value="'.$rec['serial_number'].'">';
    print '<td><input type="submit" onclick="location.href=datadelete.php" value="消去"></td>';
    print "<td>";print $rec['user_id'];print "</td>";
    print "<td>";print $rec['serial_number'];print "</td>";
    print "<td>";print $rec['text'];print "</td>";
    print "<td>";print $rec['inquily_time'];print "</td>";
    print "<td>";print $rec['text_category'];print "</td></form>";
    print "</tr>";
}
}
catch(Exception $e){
print $e;
exit();
}
print "</table>";

print"<p class=table-name style='color:slateblue;'>備品テーブル</p>";

print '<form action="datainsert.php" method="post">';
print '<input type="text" name="item_num" placeholder="item_num" >';
print '<input type="text" name="item_name" placeholder="item_name" >';
print '<input type="text" name="total" placeholder="total" >';
print '<input type="text" name="unit_price" placeholder="unit_price" >';
print '<input type="hidden" name="table" value="item">';
print '<input type="submit" onclick="location.href=datainsert.php" value ="追加"></form>';

print"<table>";
print"<tr>";
print"<th></th>";
print"<th>item_number</th>";
print"<th>item_name</th>";
print"<th>total_of_item</th>";
print"<th>rental_unit_price</th>";
print"</tr>";
try{
$sql="select * from item_table";
$stmt=$conn->prepare($sql);
$stmt->execute();
while(true)
{
    $rec=$stmt->fetch(PDO::FETCH_ASSOC);
    if($rec==false){
        break;
    }
    print "<tr>";
    print '<form action="datadelete.php" method="post">';
    print '<input type="hidden" name="table_d" value="item">';
    print '<input type="hidden" name="num" value="'.$rec['item_number'].'">';
    print '<td><input type="submit" onclick="location.href=datadelete.php" value="消去"></td>';
    print "<td>";print $rec['item_number'];print "</td>";
    print "<td>";print $rec['item_name'];print "</td>";
    print "<td>";print $rec['total_of_item'];print "</td>";
    print "<td>";print $rec['rental_unit_price'];print "</td></form>";
    print "</tr>";
}
}
catch(Exception $e){
print $e;
exit();
}
print "</table>";

print"<p class=table-name style='color:magenta;'>貸出テーブル</p>";

print '<form action="datainsert.php" method="post">';
print '<input type="text" name="reser_num" placeholder="reservation_num" >';
print '<input type="text" name="item_num" placeholder="item_num" >';
print '<input type="text" name="number_rental" placeholder="number_rental" >';
print '<input type="text" name="rental_price" placeholder="rental_price" >';
print '<input type="hidden" name="table" value="rental">';
print '<input type="submit" onclick="location.href=datainsert.php" value ="追加"></form>';

print"<table>";
print"<tr>";
print"<th></th>";
print"<th>reservation_number</th>";
print"<th>item_number</th>";
print"<th>number_of_rental</th>";
print"<th>rental_price</th>";
print"</tr>";
try{
$sql="select * from rental_table";
$stmt=$conn->prepare($sql);
$stmt->execute();
while(true)
{
    $rec=$stmt->fetch(PDO::FETCH_ASSOC);
    if($rec==false){
        break;
    }
    print "<tr>";
    print '<form action="datadelete.php" method="post">';
    print '<input type="hidden" name="table_d" value="rental">';
    print '<input type="hidden" name="rnum" value="'.$rec['reservation_number'].'">';
    print '<input type="hidden" name="inum" value="'.$rec['item_number'].'">';
    print '<td><input type="submit" onclick="location.href=datadelete.php" value="消去"></td>';
    print "<td>";print $rec['reservation_number'];print "</td>";
    print "<td>";print $rec['item_number'];print "</td>";
    print "<td>";print $rec['number_of_rental'];print "</td>";
    print "<td>";print $rec['rental_price'];print "</td></form>";
    print "</tr>";
}
}
catch(Exception $e){
print $e;
exit();
}
print "</table>";

print"<p class=table-name style='color:darkblue;'>拡張機能テーブル</p>";

print '<form action="datainsert.php" method="post">';
print '<input type="text" name="date" value="2025-00-00 00:00" >';
print '<input type="text" name="extension" placeholder="Y or N" >';
print '<input type="text" name="rental_f" value="R" >';
print '<input type="text" name="price_f" value="P" >';
print '<input type="text" name="eq_f" value="E" >';
print '<input type="text" name="time_f" value="T" >';
print '<input type="hidden" name="table" value="extension">';
print '<input type="submit" onclick="location.href=datainsert.php" value ="追加"></form>';

print"<table>";
print"<tr>";
print"<th></th>";
print"<th>change_extension_date</th>";
print"<th>use_extension</th>";
print"<th>rental_flag</th>";
print"<th>price_flag</th>";
print"<th>equipment_flag</th>";
print"<th>time_extension_flag</th>";
print"</tr>";
try{
$sql="select * from extension_table";
$stmt=$conn->prepare($sql);
$stmt->execute();
while(true)
{
    $rec=$stmt->fetch(PDO::FETCH_ASSOC);
    if($rec==false){
        break;
    }
    $date=$rec['change_extension_date'];
    print "<tr>";
    print '<form action="datadelete.php" method="post">';
    print '<input type="hidden" name="table_d" value="extension">';
    print '<input type="hidden" name="change" value="'.$rec['change_extension_date'].'">';
    print '<td><input type="submit" onclick="location.href=datadelete.php" value="消去"></td>';
    print "<td>";print $rec['change_extension_date'];"</td>";
    print "<td>";print $rec['use_extension'];print "</td>";
    print "<td>";print $rec['rental_flag'];print "</td>";
    print "<td>";print $rec['price_flag'];print "</td>";
    print "<td>";print $rec['equipment_flag'];print "</td>";
    print "<td>";print $rec['time_extension_flag'];print "</td></form>";
}
}
catch(Exception $e){
print $e;
exit();
}
print "</table>";

$conn=null;
?>

</body>
</html>