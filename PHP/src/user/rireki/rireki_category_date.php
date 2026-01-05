<?php
include_once "../../common/connect.php";
include_once "../../common/session.php";
$_SESSION['pagename']="利用履歴";
include_once "../../login/user_home.php";
$_SESSION['pagename']="ホームページ";
?>
<!DOCTYPE html>
<html>
    <head>
    <link rel="stylesheet" href="rireki.css">
        <title>日時ソート</title>
        <body>
        <p class="title">表示する日付を選択</p>
<?php
print '<form action="rireki.php" method=post>';
print '<p class="right">';
print '<input type="date" class="date" name="atai">';
print '<input type="hidden" name="cate" value="start_time_of_use">';
print '<input type="submit" class="buttonsize" value="検索">';
print '<input type="hidden" class="pagename" name="pagename" value="利用履歴">';
print '</p>';
print '</form>';
?>

<?php
print '<form action="rireki.php" method="post">';
print '<p class="right">';
print '<input type="submit" class="buttonsize" name="back" value="戻る">';
print '<input type="hidden" class="pagename" name="pagename" value="利用履歴">';
print '</p></form>';
?>
</body>
</html>