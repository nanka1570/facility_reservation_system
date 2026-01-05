<?php
include_once "../../common/connect.php";
include_once "../../common/sanitize.php";
//質問の再設定
?>
<!-- <!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title></title>
</head>
<body> -->
<?php
try
{
    $post=sanitizeinput($_POST);
    $mail_address=$_POST['mail_address'];
    $secret_question=$_POST['secret_question'];
    $secret_answer=$_POST['secret_answer'];

    $dbh=$conn;
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $sql='SELECT * FROM user_table 
          WHERE mail_address= ?';
    $stmt=$dbh->prepare($sql);
    $data = [$mail_address];
    $stmt->execute($data);
    $rec=$stmt->fetch(PDO::FETCH_ASSOC);
    if($rec==false){
    echo '<p>そちらのメールアドレスは登録されておりません。</p>';
    $dbh=null;
    }
    else{
        $dbh2=$conn;
        $dbh2->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $sql2='UPDATE user_table 
            SET secret_question= :secret_question,secret_answer=:secret_answer
            WHERE mail_address= :mail_address';
        $stmt2=$dbh2->prepare($sql2);
        $stmt2->bindParam(':mail_address', $mail_address, PDO::PARAM_STR);
        $stmt2->bindParam(':secret_question', $secret_question, PDO::PARAM_STR);
        $stmt2->bindParam(':secret_answer', $secret_answer, PDO::PARAM_STR);
        $stmt2->execute();
        echo '<p>再設定しました。</p>';
        echo '<a href="private_question.php">2段階認証へ</a>';
        $dbh2=null;
    }
    $dbh=null;
}
catch (Exception $e)
{
	print $e;
	print 'ただいま障害により大変ご迷惑をお掛けしております。';
}
?>

<!-- </body>
</html> -->