<?php
include_once "../../common/sanitize.php";
$post=sanitizeinput($_POST);
$question_value="A";//selectのvalueの初期値
$secret_question=1;//selectの初期値

if(isset($_POST['reset'])==true){//認証ボタンを押した時textの内容を保持
    if(isset($_POST['mail_address']))
    {
        $mail_address=$_POST['mail_address'];
    }else{
        $mail_address="";
    }

    // if(isset($_POST['user_Id']))
    // {
    //     $user_Id=$_POST['user_Id'];
    // }else{
    //     $user_Id="";
    // }

    if(isset($_POST["secret_question"]))
    {
        $secret_question = $_POST["secret_question"];
        if($secret_question==1){
            $question_value="A";
        }
        elseif($secret_question==2){
            $question_value="B";
        }
        elseif($secret_question==3){
            $question_value="C";
        }
    }

    if(isset($_POST["secret_answer"]))
    {
        $secret_answer = $_POST["secret_answer"];
    }else{
        $secret_answer="";
    }
}
else{
    //$user_Id="";
    $mail_address="";
    $secret_answer="";
}
?>
<html>
    <head>
        <meta charset="utf-8">
        <title></title>
        <meta name="" content="">
        <link rel="stylesheet" href="user.css">
        <h1 class="top">秘密の質問の再設定</h1>
    </head>
    <body>
    <form action="question_reset.php" method="POST">
    <!-- <p>ユーザーID　　　
    <input type="text" name="user_Id" value="<?php print $user_Id ?>"></p>
<?php
// if(isset($_POST['reset'])==true){
//     if($user_Id=='')
//     {
//         print '<p>ユーザーIDが入力されていません。</p>';
//     }
//}
?> -->
    <br><p>メールアドレス 　
    <input type="text" name="mail_address" value="<?php print $mail_address ?>"></p>
<?php
if(isset($_POST['reset'])==true){
    if($mail_address=='')
    {
        print '<p>メールアドレスが入力されていません。</p>';
    }
}
?>
    <br>秘密の質問 　　　　　　　　 質問
    <select name="secret_question">
        <option value="<?php print $secret_question ?>" selected hidden>質問<?php print $question_value ?></option>
        <option value="1">質問A</option>
        <option value="2">質問B</option>
        <option value="3">質問C</option>
    </select>
    <br><br>回答　　　 　　　
    <input type="text" name="secret_answer" value="<?php print $secret_answer ?>">
<?php
if(isset($_POST['reset'])==true){
    if($secret_answer=='')
    {
        print '<p>質問の回答が入力されていません。</p>';
    }
}
?> 
    <br><br>
    <input type="submit" value="認証">
    <input type="hidden" name="reset" value="reset">
    </form>
<?php
    if(/*$user_Id=='' ||*/ $mail_address==''  || $secret_answer=='')
    {
        print '<br />';	
        include_once "question_reset.php";  
    }
    else
    {
        include_once "question_reset_done.php";
        print '<form method="post" action="question_reset_done.php">';
        // print '<input type="hidden" name="user_Id" value="'.$user_Id.'">';
        print '<input type="hidden" name="mail_address" value="'.$mail_address.'">';
        print '<input type="hidden" name="secret_question" value="'.$secret_question.'">';
        print '<input type="hidden" name="secret_answer" value="'.$secret_answer.'">';
        print '<br />';
        print '</form>';
    }
?>
        <a href="private_question.php"><button>戻る</button></a></p>
<a href="../../login/user_login.php">ログインページへ</a><br /><br />
    </body>
</html>
