<?php
//include_once "../connect.php";
//include_once "../session.php";
include_once "../common/connect.php";
include_once "../common/session.php";
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        $tex=$_POST['hensin'];
        $id=$_POST['user_id'];
        if($tex==null){
            print 'お問合せ内容が入力されていません';
            print '<br />';
            print '<form action="controlq_kanri.php" method="post" name="form">';
            print '<from>';
            
        }
        else{
            print $id;print'からの問い合わせ';
            print '<br />';
            print 'この内容で送信しますか?';
            print '<br />';
            print $tex; 
            print '<br />'; 
        }
            print '<form method="post" action="chat_done_kanri.php">';
        print '<input type="hidden" name="text" value="'.$tex.'">'; 
        print '<input type="submit" onclick="location.href=chat_done_kanri.php" value="確定">';
        print '<input type="hidden" name="user_id" value="'.$id.'">';
        print '</form>';

        print '<form action="controlq_kanri.php" method="post">';
        print '<input type="hidden" name="user_id" value="'.$id.'">';
        print '<input type="submit" onclick="location.href=controlq_kanri.php" value="戻る">';
        print '</form>';
            
        
        
       
        ?>
       
    </body>
</html>